<?php

// Load all theme function files following the fct_*.php convention.
foreach (glob(get_template_directory() . '/fct/fct_*.php') as $fct_file) {
	require_once $fct_file;
}
