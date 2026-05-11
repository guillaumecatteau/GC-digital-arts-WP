<?php

function fct_gc_theme_setup()
{
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('menus');
	add_theme_support('custom-logo', [
		'height' => 120,
		'width' => 120,
		'flex-height' => true,
		'flex-width' => true,
	]);
	add_theme_support('html5', [
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	]);

	register_nav_menus([
		'primary' => 'Menu principal',
		'footer_primary' => 'Menu footer principal',
		'footer_secondary' => 'Menu footer secondaire',
		'social' => 'Menu social',
	]);

	add_image_size('gc-card', 720, 540, true);
	add_image_size('gc-hero', 1600, 900, true);
}

add_action('after_setup_theme', 'fct_gc_theme_setup');

function fct_gc_handle_language_switch()
{
	if (! isset($_GET['lang'])) {
		return;
	}

	$lang = sanitize_text_field(wp_unslash($_GET['lang']));

	if (! in_array($lang, ['fr', 'en'], true)) {
		return;
	}

	setcookie('gc_lang', $lang, time() + (DAY_IN_SECONDS * 180), COOKIEPATH ?: '/');
	$_COOKIE['gc_lang'] = $lang;
}

add_action('init', 'fct_gc_handle_language_switch');

function fct_gc_register_acf_options_pages()
{
	if (! function_exists('acf_add_options_page')) {
		return;
	}

	$parent = acf_add_options_page([
		'page_title' => 'GC Digital Arts',
		'menu_title' => 'GC Digital Arts',
		'menu_slug' => 'gc-digital-arts-options',
		'capability' => 'edit_posts',
		'redirect' => true,
	]);

	if (! $parent) {
		return;
	}

	acf_add_options_sub_page([
		'page_title' => 'Réglages globaux',
		'menu_title' => 'Réglages globaux',
		'parent_slug' => $parent['menu_slug'],
	]);

	acf_add_options_sub_page([
		'page_title' => 'Réseaux sociaux',
		'menu_title' => 'Réseaux sociaux',
		'parent_slug' => $parent['menu_slug'],
	]);

	acf_add_options_sub_page([
		'page_title' => 'Contact',
		'menu_title' => 'Contact',
		'parent_slug' => $parent['menu_slug'],
	]);
}

add_action('acf/init', 'fct_gc_register_acf_options_pages');
