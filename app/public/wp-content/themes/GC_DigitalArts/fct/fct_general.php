<?php

function fct_gc_theme_setup()
{
	add_theme_support('post-thumbnails');
	add_theme_support('menus');

	register_nav_menus([
		'header' => 'Menu principal (en-tête)',
		'submenu' => 'Menu secondaire (en-tête)',
		'footer A' => 'Menu footer A (pied de page)',
		'footer B' => 'Menu footer B (pied de page)',
	]);
}

add_action('after_setup_theme', 'fct_gc_theme_setup');

add_action('acf/init', function () {
	if (function_exists('acf_add_options_page')) {
		acf_add_options_page([
			'page_title' => 'General Settings',
			'menu_title' => 'Options',
			'menu_slug' => 'general-settings',
			'capability' => 'edit_posts',
			'redirect' => false,
		]);
	}
});
