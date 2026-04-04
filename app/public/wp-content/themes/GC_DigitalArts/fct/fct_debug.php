<?php

function fct_var2console($var, $name = '', $now = false)
{
	if ($var === null) {
		$type = 'NULL';
	} elseif (is_bool($var)) {
		$type = 'BOOL';
	} elseif (is_string($var)) {
		$type = 'STRING[' . strlen($var) . ']';
	} elseif (is_int($var)) {
		$type = 'INT';
	} elseif (is_float($var)) {
		$type = 'FLOAT';
	} elseif (is_array($var)) {
		$type = 'ARRAY[' . count($var) . ']';
	} elseif (is_object($var)) {
		$type = 'OBJECT';
	} elseif (is_resource($var)) {
		$type = 'RESOURCE';
	} else {
		$type = '???';
	}

	if (strlen($name)) {
		fct_str2console("$type $name = " . var_export($var, true) . ';', $now);
	} else {
		fct_str2console("$type = " . var_export($var, true) . ';', $now);
	}
}

function fct_str2console($str, $now = false)
{
	if ($now) {
		echo "<script type='text/javascript'>\n";
		echo "//<![CDATA[\n";
		echo 'console.log(' . json_encode($str) . ');' . "\n";
		echo "//]]>\n";
		echo '</script>';
	} else {
		register_shutdown_function('fct_str2console', $str, true);
	}
}
