<?php

function gc_get_current_lang()
{
	if (isset($_GET['lang'])) {
		$requested_lang = sanitize_text_field(wp_unslash($_GET['lang']));

		if (in_array($requested_lang, ['fr', 'en'], true)) {
			return $requested_lang;
		}
	}

	if (isset($_COOKIE['gc_lang']) && in_array($_COOKIE['gc_lang'], ['fr', 'en'], true)) {
		return $_COOKIE['gc_lang'];
	}

	$accept_language = strtolower((string) ($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''));
	$first_locale = trim(explode(',', $accept_language)[0] ?? '');

	return str_starts_with($first_locale, 'fr') ? 'fr' : 'en';
}

function gc_lang_url($lang)
{
	if (! in_array($lang, ['fr', 'en'], true)) {
		$lang = 'en';
	}

	return add_query_arg('lang', $lang);
}

function gc_t($fr, $en = '')
{
	$current_lang = gc_get_current_lang();

	if ($current_lang === 'fr') {
		return $fr;
	}

	return $en !== '' ? $en : $fr;
}

function gc_get_translated_text($text_fr, $text_en, $fallback = '')
{
	$current_lang = gc_get_current_lang();

	if ($current_lang === 'fr') {
		return $text_fr ?: ($text_en ?: $fallback);
	}

	return $text_en ?: ($text_fr ?: $fallback);
}

function gc_get_acf_value($field_name, $post_id = false, $default = '')
{
	if (! function_exists('get_field')) {
		return $default;
	}

	$value = get_field($field_name, $post_id);

	if ($value === null || $value === '' || $value === []) {
		return $default;
	}

	return $value;
}

function gc_format_date_value($value, $format = 'F Y')
{
	if (! $value) {
		return '';
	}

	$formats = ['Ymd', 'Y-m-d', 'd/m/Y'];

	foreach ($formats as $candidate) {
		$date = DateTime::createFromFormat($candidate, (string) $value);

		if ($date instanceof DateTime) {
			return wp_date($format, $date->getTimestamp());
		}
	}

	$timestamp = strtotime((string) $value);

	return $timestamp ? wp_date($format, $timestamp) : (string) $value;
}

function gc_get_date_range($start, $end)
{
	$start_label = gc_format_date_value($start);
	$end_label = $end ? gc_format_date_value($end) : 'Aujourd\'hui';

	if (! $start_label && ! $end) {
		return '';
	}

	if (! $start_label) {
		return $end_label;
	}

	return trim($start_label . ' - ' . $end_label);
}

function gc_get_project_date_value($field_name, $post_id = null)
{
	$post_id = $post_id ?: get_the_ID();
	$value = gc_get_acf_value($field_name, $post_id, '');

	if ($value !== '') {
		return $value;
	}

	return (string) get_post_meta((int) $post_id, $field_name, true);
}

function gc_get_media_html($post_id = null, $field_name = 'main_visual', $size = 'gc-card', $class_name = 'gc-media', $fallback_thumbnail = true)
{
	$post_id = $post_id ?: get_the_ID();
	$media = gc_get_acf_value($field_name, $post_id, null);

	if (is_array($media)) {
		if (isset($media['type']) && $media['type'] === 'image' && ! empty($media['ID'])) {
			return wp_get_attachment_image($media['ID'], $size, false, ['class' => $class_name]);
		}

		if (! empty($media['mime_type']) && str_starts_with($media['mime_type'], 'video/') && ! empty($media['ID'])) {
			return wp_video_shortcode([
				'src' => wp_get_attachment_url($media['ID']),
				'class' => $class_name,
			]);
		}

		if (! empty($media['ID'])) {
			return wp_get_attachment_image($media['ID'], $size, false, ['class' => $class_name]);
		}
	}

	if (is_numeric($media)) {
		return wp_get_attachment_image((int) $media, $size, false, ['class' => $class_name]);
	}

	if ($fallback_thumbnail && has_post_thumbnail($post_id)) {
		return get_the_post_thumbnail($post_id, $size, ['class' => $class_name]);
	}

	return sprintf('<div class="%1$s %1$s--placeholder"><span>Visual pending</span></div>', esc_attr($class_name));
}

function gc_render_term_links($post_id, $taxonomy)
{
	$terms = get_the_terms($post_id, $taxonomy);

	if (empty($terms) || is_wp_error($terms)) {
		return '';
	}

	$output = [];

	foreach ($terms as $term) {
		$output[] = sprintf(
			'<a class="gc-tag" href="%s">%s</a>',
			esc_url(get_term_link($term)),
			esc_html($term->name)
		);
	}

	return implode('', $output);
}

function gc_get_social_links()
{
	$links = gc_get_acf_value('social_links', 'option', []);

	return is_array($links) ? $links : [];
}

function gc_get_related_posts_for_project($project_id)
{
	$query = new WP_Query([
		'post_type' => 'post',
		'posts_per_page' => -1,
		'post_status' => 'publish',
		'fields' => 'ids',
		'meta_query' => [
			[
				'key' => 'related_projects',
				'value' => '"' . (int) $project_id . '"',
				'compare' => 'LIKE',
			],
		],
	]);

	return $query->posts;
}

function gc_get_related_attachments_for_project($project_id)
{
	$query = new WP_Query([
		'post_type'      => 'attachment',
		'posts_per_page' => -1,
		'post_status'    => 'inherit',
		'fields'         => 'ids',
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
		'meta_query'     => [
			'relation' => 'AND',
			[
				'key'     => 'related_project',
				'value'   => (int) $project_id,
				'compare' => '=',
				'type'    => 'NUMERIC',
			],
			[
				'relation' => 'OR',
				[
					'key'     => 'gc_media_type',
					'value'   => 'media',
					'compare' => '=',
				],
				[
					'key'     => 'gc_media_type',
					'compare' => 'NOT EXISTS',
				],
			],
		],
	]);

	return $query->posts;
}

function gc_get_related_videos_for_project($project_id)
{
	$query = new WP_Query([
		'post_type'      => 'gc_video',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'meta_query'     => [
			[
				'key'     => 'related_project',
				'value'   => (int) $project_id,
				'compare' => '=',
				'type'    => 'NUMERIC',
			],
		],
	]);

	return $query->posts;
}

function gc_get_related_attachments_for_post($post_id)
{
	$query = new WP_Query([
		'post_type' => 'attachment',
		'posts_per_page' => -1,
		'post_status' => 'inherit',
		'fields' => 'ids',
		'meta_query' => [
			[
				'key' => 'gc_media_type',
				'value' => 'media',
				'compare' => '=',
			],
			[
				'key' => 'related_post',
				'value' => (int) $post_id,
				'compare' => '=',
				'type' => 'NUMERIC',
			],
		],
	]);

	return $query->posts;
}

function gc_get_attachment_media_type($attachment_id)
{
	$type = (string) get_post_meta((int) $attachment_id, 'gc_media_type', true);

	if (! in_array($type, ['media', 'logo', 'background'], true)) {
		return 'media';
	}

	return $type;
}

function gc_get_attachment_media_html($attachment_id, $size = 'large', $class_name = 'gc-media')
{
	$attachment_id = (int) $attachment_id;

	if ($attachment_id <= 0) {
		return '';
	}

	$mime_type = get_post_mime_type($attachment_id);

	if (is_string($mime_type) && str_starts_with($mime_type, 'video/')) {
		return wp_video_shortcode([
			'src' => wp_get_attachment_url($attachment_id),
			'class' => $class_name,
		]);
	}

	return wp_get_attachment_image($attachment_id, $size, false, ['class' => $class_name]);
}

function gc_get_attachment_translated_text($attachment_id)
{
	if (gc_get_attachment_media_type($attachment_id) !== 'media') {
		return '';
	}

	$text_fr = (string) get_post_meta($attachment_id, 'gc_media_text_fr', true);
	$text_en = (string) get_post_meta($attachment_id, 'gc_media_text_en', true);

	return gc_get_translated_text($text_fr, $text_en, '');
}

function gc_get_attachment_year($attachment_id)
{
	$year = (int) get_post_meta((int) $attachment_id, 'media_year', true);

	return $year > 0 ? $year : null;
}

function gc_get_logo_attachment_for_experience($experience_id)
{
	$query = new WP_Query([
		'post_type' => 'attachment',
		'posts_per_page' => 1,
		'post_status' => 'inherit',
		'fields' => 'ids',
		'meta_query' => [
			[
				'key' => 'gc_media_type',
				'value' => 'logo',
				'compare' => '=',
			],
			[
				'key' => 'related_experience',
				'value' => (int) $experience_id,
				'compare' => '=',
				'type' => 'NUMERIC',
			],
		],
	]);

	return isset($query->posts[0]) ? (int) $query->posts[0] : null;
}

function gc_get_background_attachment_url($usage = 'global')
{
	$usage = sanitize_text_field((string) $usage);

	if (! in_array($usage, ['global', 'home', 'experience', 'blog', 'contact'], true)) {
		$usage = 'global';
	}

	$query = new WP_Query([
		'post_type' => 'attachment',
		'posts_per_page' => 1,
		'post_status' => 'inherit',
		'fields' => 'ids',
		'meta_query' => [
			[
				'key' => 'gc_media_type',
				'value' => 'background',
				'compare' => '=',
			],
			[
				'key' => 'background_usage',
				'value' => $usage,
				'compare' => '=',
			],
		],
	]);

	$attachment_id = isset($query->posts[0]) ? (int) $query->posts[0] : 0;

	if ($attachment_id <= 0 && $usage !== 'global') {
		return gc_get_background_attachment_url('global');
	}

	return $attachment_id > 0 ? wp_get_attachment_url($attachment_id) : '';
}

function gc_get_project_gallery_attachments($project_id)
{
	$layouts = gc_get_acf_value('pagebuilder', $project_id, []);

	if (! is_array($layouts)) {
		return [];
	}

	$attachment_ids = [];
	$seen = [];

	foreach ($layouts as $layout) {
		if (! is_array($layout) || ($layout['acf_fc_layout'] ?? '') !== 'gallerie') {
			continue;
		}

		$images = $layout['gallery'] ?? [];

		foreach ($images as $item) {
			if (is_numeric($item)) {
				$id = (int) $item;
			} elseif (is_array($item) && ! empty($item['ID'])) {
				$id = (int) $item['ID'];
			} else {
				continue;
			}
			if ($id > 0 && ! isset($seen[$id])) {
				$seen[$id] = true;
				$attachment_ids[] = $id;
			}
		}
	}

	return $attachment_ids;
}

function gc_get_related_experience_for_project($project_id)
{
	$experience_id = (int) get_post_meta((int) $project_id, 'related_experience', true);

	if ($experience_id <= 0) {
		return null;
	}

	$experience = get_post($experience_id);

	if (! $experience || $experience->post_type !== 'experience' || $experience->post_status !== 'publish') {
		return null;
	}

	return $experience_id;
}

function gc_get_selected_expertise_term($post_id)
{
	$field_value = gc_get_acf_value('expertise_category', $post_id, null);

	if ($field_value instanceof WP_Term) {
		return $field_value;
	}

	if (is_numeric($field_value)) {
		return get_term((int) $field_value, 'gc_category');
	}

	if (is_array($field_value) && isset($field_value['term_id'])) {
		return get_term((int) $field_value['term_id'], 'gc_category');
	}

	$slug = get_post_field('post_name', $post_id);

	if (! $slug) {
		return null;
	}

	$term = get_term_by('slug', $slug, 'gc_category');

	return $term instanceof WP_Term ? $term : null;
}