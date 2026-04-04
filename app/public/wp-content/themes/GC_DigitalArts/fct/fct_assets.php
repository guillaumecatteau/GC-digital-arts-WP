<?php

function fct_enqueue_assets()
{
	$theme_version = wp_get_theme()->get('Version');

	wp_enqueue_style(
		'gc-theme-style',
		get_stylesheet_uri(),
		[],
		$theme_version
	);

	wp_enqueue_style(
		'gc-google-fonts',
		'https://fonts.googleapis.com/css2?family=Audiowide&family=Sarala:wght@400;700&display=swap',
		[],
		null
	);

	wp_enqueue_style(
		'gc-main-css',
		get_template_directory_uri() . '/css/main.css',
		['gc-theme-style', 'gc-google-fonts'],
		$theme_version
	);

	wp_enqueue_script(
		'gc-main-js',
		get_template_directory_uri() . '/js/main.js',
		[],
		$theme_version,
		true
	);

	wp_script_add_data('gc-main-js', 'type', 'module');
}

add_action('wp_enqueue_scripts', 'fct_enqueue_assets');
