<?php
/**
 * Provides functions for profiling PHP code. For basic usage, include this file
 * at the top of the page. Throughout the code, call profiler_start() and
 * profiler_stop() to profile sections of the code. At the end, call
 * profiler_dump() to print the results of the profile.
 */

class Profiler
{
  private $_name;
	private $_start;
	private $_time;

	public function __construct($name)
	{
		$this->_name = $name;
		$this->reset();
	}

	public function __toString()
	{
		$time = round($this->getTime(), 2);
		return "[Profiler '{$this->_name}' ${time}s]";
	}

	public function getName()
	{
		return $this->_name;
	}

	public function getStart()
	{
		return $this->_start;
	}

	public function getTime()
	{
		if ($this->_start != 0)
			return microtime(true) - $this->_start + $this->_time;
		else
			return $this->_time;
	}

	public function reset()
	{
		$_start = 0;
		$_time = 0.0;
	}

	public function start()
	{
		if ($this->_start == 0)
			$this->_start = microtime(true);
	}

	public function stop()
	{
		if ($this->_start != 0) {
			$this->_time += microtime(true) - $this->_start;
			$this->_start = 0;
		}
	}
}

class GlobalProfiler extends Profiler
{
	private static $_instance = null;

	private static $_children = array();

	public function __construct()
	{
	parent::__construct('__GLOBAL__');
	}

	public function __toString()
	{
		$children = array();
		$str = '';

		foreach (GlobalProfiler::$_children as $name => $profiler) {
			$time = round($profiler->getTime(), 2);
			$children[] = "'{$name}': {$time}s";
		}
		if (!empty($children))
			$str .= ' {' . implode(", ", $children) . '}';

		$time = round($this->getTime(), 2);
		return "[GlobalProfiler {$time}s{$str}]";
	}

	public function instance()
	{
		if (GlobalProfiler::$_instance == null)
		  GlobalProfiler::$_instance = new GlobalProfiler();
		return GlobalProfiler::$_instance;
	}

	public function getChild($name)
	{
		if (!array_key_exists($name, GlobalProfiler::$_children))
		  GlobalProfiler::$_children[$name] = new Profiler($name);
		return GlobalProfiler::$_children[$name];
	}
}

GlobalProfiler::instance()->start();

function profiler_dump($name = null)
{
	if ($name == null)
		$profiler = GlobalProfiler::instance();
	else
		$profiler = GlobalProfiler::getChild($name);	
	error_log(var_export($profiler, TRUE));
}

function profiler_start($name)
{
	$profiler = GlobalProfiler::getChild($name);
	$profiler->start();
}

function profiler_stop($name)
{
	$profiler = GlobalProfiler::getChild($name);
	$profiler->stop();
}
