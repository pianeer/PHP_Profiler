PHP_Profiler
============

A simple PHP profiler to inspect certain parts of code.

To begin using this, just include this script inside your code and place the part of your code you'd like to inspect 
between the profiler_start and profiler_stop function calls. Finally call profiler_dump() to view the results.
An example below:

<?php
include_once('Profiler.php');

//Passing a parameter is optional

profiler_start($loop_time);

/*
Your Code...
*/

profiler_stop($loop_time);

profiler_dump($loop_time);

?>
