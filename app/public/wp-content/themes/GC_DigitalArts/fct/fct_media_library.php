<?php

function fct_gc_get_media_type($attachment_id)
{
	$type = (string) get_post_meta($attachment_id, 'gc_media_type', true);

	if (! in_array($type, ['media', 'logo', 'background'], true)) {
		return 'media';
	}

	return $type;
}

function fct_gc_media_field_marker($types)
{
	return '<span class="gc-media-field-marker" data-types="' . esc_attr($types) . '" style="display:none;"></span>';
}

function fct_gc_media_attachment_fields_to_edit($form_fields, $post)
{
	if (! current_user_can('upload_files')) {
		return $form_fields;
	}

	$selected_type = fct_gc_get_media_type($post->ID);
	$selected_project = (int) get_post_meta($post->ID, 'related_project', true);
	$menu_order = (int) $post->menu_order;
	$selected_experience = (int) get_post_meta($post->ID, 'related_experience', true);
	$selected_year = (string) get_post_meta($post->ID, 'media_year', true);
	$background_usage = (string) get_post_meta($post->ID, 'background_usage', true);

	$selected_categories = wp_get_object_terms($post->ID, 'gc_category', ['fields' => 'ids']);
	$selected_technologies = wp_get_object_terms($post->ID, 'technology', ['fields' => 'ids']);

	if (is_wp_error($selected_categories)) {
		$selected_categories = [];
	}

	if (is_wp_error($selected_technologies)) {
		$selected_technologies = [];
	}

	$projects = get_posts([
		'post_type' => 'project',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'orderby' => 'title',
		'order' => 'ASC',
	]);

	$experiences = get_posts([
		'post_type' => 'experience',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'orderby' => 'title',
		'order' => 'ASC',
	]);

	$project_options = '<option value="">Aucun projet</option>';
	$experience_options = '<option value="">Aucune expérience</option>';
	$category_checkboxes = '';
	$technology_checkboxes = '';

	unset($form_fields['gc_category'], $form_fields['technology']);

	foreach ($projects as $project) {
		$project_options .= sprintf(
			'<option value="%1$d" %3$s>%2$s</option>',
			(int) $project->ID,
			esc_html($project->post_title),
			selected((int) $project->ID, $selected_project, false)
		);
	}

	foreach ($experiences as $experience) {
		$experience_options .= sprintf(
			'<option value="%1$d" %3$s>%2$s</option>',
			(int) $experience->ID,
			esc_html($experience->post_title),
			selected((int) $experience->ID, $selected_experience, false)
		);
	}

	$categories = get_terms([
		'taxonomy' => 'gc_category',
		'hide_empty' => false,
	]);

	if (! is_wp_error($categories)) {
		foreach ($categories as $category) {
			$category_checkboxes .= sprintf(
				'<label style="display:block;margin-bottom:4px;"><input type="checkbox" name="attachments[%1$d][gc_categories][]" value="%2$d" %4$s> %3$s</label>',
				(int) $post->ID,
				(int) $category->term_id,
				esc_html($category->name),
				checked(in_array((int) $category->term_id, $selected_categories, true), true, false)
			);
		}
	}

	$technologies = get_terms([
		'taxonomy' => 'technology',
		'hide_empty' => false,
	]);

	if (! is_wp_error($technologies)) {
		foreach ($technologies as $technology) {
			$technology_checkboxes .= sprintf(
				'<label style="display:block;margin-bottom:4px;"><input type="checkbox" name="attachments[%1$d][gc_technologies][]" value="%2$d" %4$s> %3$s</label>',
				(int) $post->ID,
				(int) $technology->term_id,
				esc_html($technology->name),
				checked(in_array((int) $technology->term_id, $selected_technologies, true), true, false)
			);
		}
	}

	$type_radios = sprintf(
		'<label style="margin-right:10px;"><input type="radio" name="attachments[%1$d][gc_media_type]" value="media" %2$s> media</label>
		<label style="margin-right:10px;"><input type="radio" name="attachments[%1$d][gc_media_type]" value="logo" %3$s> logo</label>
		<label><input type="radio" name="attachments[%1$d][gc_media_type]" value="background" %4$s> background</label>',
		(int) $post->ID,
		checked($selected_type, 'media', false),
		checked($selected_type, 'logo', false),
		checked($selected_type, 'background', false)
	);

	$form_fields['gc_media_type'] = [
		'label' => 'Type de media',
		'input' => 'html',
		'html' => '<div class="gc-media-type-wrap">' . $type_radios . '</div>',
		'helps' => 'Choisir un seul type: media, logo, ou background. Les champs se mettent a jour automatiquement.',
	];

	$form_fields['gc_media_text_fr'] = [
		'label' => 'Texte FR',
		'input' => 'html',
		'html' => sprintf(
			'%3$s<textarea name="attachments[%1$d][gc_media_text_fr]" rows="3" style="width:100%%;">%2$s</textarea>',
			(int) $post->ID,
			esc_textarea((string) get_post_meta($post->ID, 'gc_media_text_fr', true)),
			fct_gc_media_field_marker('media')
		),
	];

	$form_fields['gc_media_text_en'] = [
		'label' => 'Texte EN',
		'input' => 'html',
		'html' => sprintf(
			'%3$s<textarea name="attachments[%1$d][gc_media_text_en]" rows="3" style="width:100%%;">%2$s</textarea>',
			(int) $post->ID,
			esc_textarea((string) get_post_meta($post->ID, 'gc_media_text_en', true)),
			fct_gc_media_field_marker('media')
		),
	];

	$form_fields['media_year'] = [
		'label' => 'Annee',
		'input' => 'html',
		'html' => sprintf(
			'%3$s<input type="number" min="1900" max="2100" name="attachments[%1$d][media_year]" value="%2$s" style="width:100%%;" />',
			(int) $post->ID,
			esc_attr($selected_year),
			fct_gc_media_field_marker('media')
		),
	];

	$form_fields['related_project'] = [
		'label' => 'Projet lie',
		'input' => 'html',
		'html' => sprintf(
			'%3$s<select name="attachments[%1$d][related_project]" style="width:100%%;">%2$s</select>',
			(int) $post->ID,
			$project_options,
			fct_gc_media_field_marker('media')
		),
	];

	$form_fields['gc_menu_order'] = [
		'label' => 'Ordre',
		'input' => 'html',
		'html' => sprintf(
			'%2$s<input type="number" name="attachments[%1$d][gc_menu_order]" value="%3$d" style="width:80px;" /><p class="description" style="margin-top:4px;">Tri dans la galerie projet (0 = premier).</p>',
			(int) $post->ID,
			fct_gc_media_field_marker('media'),
			$menu_order
		),
	];

	$form_fields['gc_categories'] = [
		'label' => 'Categories',
		'input' => 'html',
		'html' => sprintf(
			'%3$s<div style="max-height:140px;overflow:auto;border:1px solid #ccd0d4;padding:8px;background:#fff;">%2$s</div>',
			(int) $post->ID,
			$category_checkboxes ?: '<em>Aucune categorie disponible.</em>',
			fct_gc_media_field_marker('media')
		),
	];

	$form_fields['gc_technologies'] = [
		'label' => 'Technologies',
		'input' => 'html',
		'html' => sprintf(
			'%3$s<div style="max-height:140px;overflow:auto;border:1px solid #ccd0d4;padding:8px;background:#fff;">%2$s</div>',
			(int) $post->ID,
			$technology_checkboxes ?: '<em>Aucune technologie disponible.</em>',
			fct_gc_media_field_marker('media')
		),
	];

	$form_fields['related_experience'] = [
		'label' => 'Experience liee',
		'input' => 'html',
		'html' => sprintf(
			'%3$s<select name="attachments[%1$d][related_experience]" style="width:100%%;">%2$s</select>',
			(int) $post->ID,
			$experience_options,
			fct_gc_media_field_marker('logo')
		),
	];

	$form_fields['background_usage'] = [
		'label' => 'Usage background',
		'input' => 'html',
		'html' => sprintf(
			'%7$s<select name="attachments[%1$d][background_usage]" style="width:100%%;">
				<option value="global" %2$s>global</option>
				<option value="home" %3$s>home</option>
				<option value="experience" %4$s>experience</option>
				<option value="blog" %5$s>blog</option>
				<option value="contact" %6$s>contact</option>
			</select>',
			(int) $post->ID,
			selected($background_usage, 'global', false),
			selected($background_usage, 'home', false),
			selected($background_usage, 'experience', false),
			selected($background_usage, 'blog', false),
			selected($background_usage, 'contact', false),
			fct_gc_media_field_marker('background')
		),
	];

	return $form_fields;
}

add_filter('attachment_fields_to_edit', 'fct_gc_media_attachment_fields_to_edit', 10, 2);

function fct_gc_media_type_toggle_script()
{
	if (! is_admin() || ! function_exists('get_current_screen')) {
		return;
	}

	$screen = get_current_screen();

	if (! $screen) {
		return;
	}

	$allowed = ['upload', 'post', 'page', 'project', 'experience'];

	if (! in_array($screen->base, $allowed, true) && ! in_array($screen->post_type, $allowed, true)) {
		return;
	}
	?>
	<script>
	(function ($) {
		function getRoot($element) {
			var $root = $element.closest('.attachment-compat, .compat-item, .media-modal-content, table.compat-attachment-fields');

			if (! $root.length) {
				$root = $(document.body);
			}

			return $root;
		}

		function getFieldWrapper($marker) {
			var $wrapper = $marker.closest('tr.compat-field, .setting, .attachment-compat-item, .form-field');

			if (! $wrapper.length) {
				$wrapper = $marker.parent();
			}

			return $wrapper;
		}

		function applyVisibility($root) {
			var $typeInput = $root.find('input[name$="[gc_media_type]"]:checked').first();
			var type = $typeInput.val() || 'media';

			$root.find('.gc-media-field-marker').each(function () {
				var $marker = $(this);
				var types = String($marker.data('types') || '').split(',');
				var shouldShow = types.indexOf(type) !== -1;
				var $field = getFieldWrapper($marker);

				if (shouldShow) {
					$field.show();
				} else {
					$field.hide();
				}
			});
		}

		function applyEverywhere() {
			$('.gc-media-field-marker').each(function () {
				applyVisibility(getRoot($(this)));
			});
		}

		$(document).on('change', 'input[name$="[gc_media_type]"]', function () {
			applyVisibility(getRoot($(this)));
		});

		$(document).ready(function () {
			applyEverywhere();

			if ('MutationObserver' in window) {
				var observer = new MutationObserver(function (mutations) {
					var shouldRefresh = false;

					mutations.forEach(function (mutation) {
						if (mutation.addedNodes && mutation.addedNodes.length) {
							shouldRefresh = true;
						}
					});

					if (shouldRefresh) {
						applyEverywhere();
					}
				});

				observer.observe(document.body, { childList: true, subtree: true });
			}
		});
	})(jQuery);
	</script>
	<?php
}

add_action('admin_footer', 'fct_gc_media_type_toggle_script');

function fct_gc_media_attachment_fields_to_save($post, $attachment)
{
	if (! current_user_can('upload_files')) {
		return $post;
	}

	$media_type = isset($attachment['gc_media_type']) ? sanitize_text_field((string) $attachment['gc_media_type']) : 'media';

	if (! in_array($media_type, ['media', 'logo', 'background'], true)) {
		$media_type = 'media';
	}

	update_post_meta($post['ID'], 'gc_media_type', $media_type);

	if (isset($attachment['gc_media_text_fr'])) {
		update_post_meta($post['ID'], 'gc_media_text_fr', wp_kses_post($attachment['gc_media_text_fr']));
	}

	if (isset($attachment['gc_media_text_en'])) {
		update_post_meta($post['ID'], 'gc_media_text_en', wp_kses_post($attachment['gc_media_text_en']));
	}

	$media_year = isset($attachment['media_year']) ? (int) $attachment['media_year'] : 0;
	if ($media_year > 0) {
		update_post_meta($post['ID'], 'media_year', $media_year);
	} else {
		delete_post_meta($post['ID'], 'media_year');
	}

	$related_project = isset($attachment['related_project']) ? (int) $attachment['related_project'] : 0;
	$related_experience = isset($attachment['related_experience']) ? (int) $attachment['related_experience'] : 0;

	$background_usage = isset($attachment['background_usage']) ? sanitize_text_field((string) $attachment['background_usage']) : 'global';

	if (! in_array($background_usage, ['global', 'home', 'experience', 'blog', 'contact'], true)) {
		$background_usage = 'global';
	}

	$categories = $attachment['gc_categories'] ?? [];
	if (! is_array($categories)) {
		$categories = [$categories];
	}
	$categories = array_values(array_filter(array_map('intval', $categories)));

	$technologies = $attachment['gc_technologies'] ?? [];
	if (! is_array($technologies)) {
		$technologies = [$technologies];
	}
	$technologies = array_values(array_filter(array_map('intval', $technologies)));

	if ($media_type === 'media') {
		update_post_meta($post['ID'], 'related_project', $related_project);
		delete_post_meta($post['ID'], 'related_experience');
		delete_post_meta($post['ID'], 'background_usage');
		wp_set_object_terms($post['ID'], $categories, 'gc_category', false);
		wp_set_object_terms($post['ID'], $technologies, 'technology', false);
		$new_order = isset($attachment['gc_menu_order']) ? (int) $attachment['gc_menu_order'] : 0;
		wp_update_post(['ID' => (int) $post['ID'], 'menu_order' => $new_order]);
	}

	if ($media_type === 'logo') {
		update_post_meta($post['ID'], 'related_experience', $related_experience);
		delete_post_meta($post['ID'], 'related_project');
		delete_post_meta($post['ID'], 'background_usage');
		delete_post_meta($post['ID'], 'media_year');
		wp_set_object_terms($post['ID'], [], 'gc_category', false);
		wp_set_object_terms($post['ID'], [], 'technology', false);
	}

	if ($media_type === 'background') {
		update_post_meta($post['ID'], 'background_usage', $background_usage);
		delete_post_meta($post['ID'], 'related_project');
		delete_post_meta($post['ID'], 'related_experience');
		delete_post_meta($post['ID'], 'media_year');
		wp_set_object_terms($post['ID'], [], 'gc_category', false);
		wp_set_object_terms($post['ID'], [], 'technology', false);
	}

	return $post;
}

add_filter('attachment_fields_to_save', 'fct_gc_media_attachment_fields_to_save', 10, 2);

function fct_gc_media_bulk_actions($bulk_actions)
{
	$bulk_actions['gc_assign_project'] = 'Assigner un projet (GC)';
	$bulk_actions['gc_set_media_text'] = 'Definir texte FR/EN (GC)';

	return $bulk_actions;
}

add_filter('bulk_actions-upload', 'fct_gc_media_bulk_actions');

function fct_gc_media_bulk_controls($which)
{
	if ($which !== 'top' || ! function_exists('get_current_screen')) {
		return;
	}

	$screen = get_current_screen();

	if (! $screen || $screen->id !== 'upload') {
		return;
	}

	$projects = get_posts([
		'post_type' => 'project',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'orderby' => 'title',
		'order' => 'ASC',
	]);

	$categories = get_terms([
		'taxonomy' => 'gc_category',
		'hide_empty' => false,
	]);

	$technologies = get_terms([
		'taxonomy' => 'technology',
		'hide_empty' => false,
	]);

	echo '<select name="gc_bulk_project" style="max-width:220px;margin-left:8px;">';
	echo '<option value="">Projet pour action GC</option>';

	foreach ($projects as $project) {
		echo '<option value="' . esc_attr((string) $project->ID) . '">' . esc_html($project->post_title) . '</option>';
	}

	echo '</select>';

	echo '<select name="gc_bulk_category" style="max-width:220px;margin-left:8px;">';
	echo '<option value="">Categorie (action GC)</option>';

	if (! is_wp_error($categories)) {
		foreach ($categories as $category) {
			echo '<option value="' . esc_attr((string) $category->term_id) . '">' . esc_html($category->name) . '</option>';
		}
	}

	echo '</select>';

	echo '<select name="gc_bulk_technology" style="max-width:220px;margin-left:8px;">';
	echo '<option value="">Technologie (action GC)</option>';

	if (! is_wp_error($technologies)) {
		foreach ($technologies as $technology) {
			echo '<option value="' . esc_attr((string) $technology->term_id) . '">' . esc_html($technology->name) . '</option>';
		}
	}

	echo '</select>';

	echo '<input type="text" name="gc_bulk_text_fr" placeholder="Texte FR (action GC)" style="margin-left:8px;max-width:220px;" />';
	echo '<input type="text" name="gc_bulk_text_en" placeholder="Text EN (GC action)" style="margin-left:8px;max-width:220px;" />';
}

add_action('restrict_manage_posts', 'fct_gc_media_bulk_controls');

function fct_gc_handle_media_bulk_actions($redirect_url, $doaction, $post_ids)
{
	if (! in_array($doaction, ['gc_assign_project', 'gc_set_media_text'], true)) {
		return $redirect_url;
	}

	if (! current_user_can('upload_files')) {
		return $redirect_url;
	}

	$updated = 0;

	foreach ($post_ids as $post_id) {
		if (get_post_type($post_id) !== 'attachment') {
			continue;
		}

		if ($doaction === 'gc_assign_project') {
			$project_id = isset($_REQUEST['gc_bulk_project']) ? (int) $_REQUEST['gc_bulk_project'] : 0;
			$category_id = isset($_REQUEST['gc_bulk_category']) ? (int) $_REQUEST['gc_bulk_category'] : 0;
			$technology_id = isset($_REQUEST['gc_bulk_technology']) ? (int) $_REQUEST['gc_bulk_technology'] : 0;
			$item_updated = false;

			if ($project_id > 0) {
				update_post_meta($post_id, 'related_project', $project_id);
				update_post_meta($post_id, 'gc_media_type', 'media');
				$item_updated = true;
			}

			if ($category_id > 0) {
				wp_set_object_terms($post_id, [$category_id], 'gc_category', true);
				$item_updated = true;
			}

			if ($technology_id > 0) {
				wp_set_object_terms($post_id, [$technology_id], 'technology', true);
				$item_updated = true;
			}

			if ($item_updated) {
				$updated++;
			}
		}

		if ($doaction === 'gc_set_media_text') {
			$text_fr = isset($_REQUEST['gc_bulk_text_fr']) ? sanitize_text_field(wp_unslash($_REQUEST['gc_bulk_text_fr'])) : '';
			$text_en = isset($_REQUEST['gc_bulk_text_en']) ? sanitize_text_field(wp_unslash($_REQUEST['gc_bulk_text_en'])) : '';

			if ($text_fr !== '') {
				update_post_meta($post_id, 'gc_media_text_fr', $text_fr);
			}

			if ($text_en !== '') {
				update_post_meta($post_id, 'gc_media_text_en', $text_en);
			}

			if ($text_fr !== '' || $text_en !== '') {
				$updated++;
			}
		}
	}

	return add_query_arg('gc_bulk_updated', (string) $updated, $redirect_url);
}

add_filter('handle_bulk_actions-upload', 'fct_gc_handle_media_bulk_actions', 10, 3);

function fct_gc_media_bulk_notices()
{
	if (! isset($_REQUEST['gc_bulk_updated'])) {
		return;
	}

	$count = (int) $_REQUEST['gc_bulk_updated'];

	if ($count <= 0) {
		echo '<div class="notice notice-warning is-dismissible"><p>Aucun media mis a jour par l\'action GC.</p></div>';
		return;
	}

	echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($count) . ' media(s) mis a jour.</p></div>';
}

add_action('admin_notices', 'fct_gc_media_bulk_notices');

/* ==========================================================================
   BULK MODAL EDITOR — multi-selection panel on upload.php
   ========================================================================== */

/**
 * AJAX handler: save metadata for multiple attachments at once.
 */
function fct_gc_media_modal_bulk_update()
{
	check_ajax_referer('gc_media_modal_bulk_update', 'nonce');

	if (! current_user_can('upload_files')) {
		wp_send_json_error(['message' => 'Permission refusee.'], 403);
	}

	$ids = isset($_POST['ids']) ? array_map('absint', (array) $_POST['ids']) : [];
	if (empty($ids)) {
		wp_send_json_error(['message' => 'Aucun id recu.'], 400);
	}

	$fields = [
		'gc_media_type'       => 'sanitize_text_field',
		'gc_media_text_fr'    => 'sanitize_textarea_field',
		'gc_media_text_en'    => 'sanitize_textarea_field',
		'media_year'          => 'absint',
		'related_project'     => 'absint',
		'related_experience'  => 'absint',
		'background_usage'    => 'sanitize_text_field',
	];

	$data = [];

	foreach ($fields as $key => $sanitizer) {
		// Empty string = "don't change". Only apply when explicitly provided (even if 0).
		if (isset($_POST[$key]) && $_POST[$key] !== '') {
			$data[$key] = call_user_func($sanitizer, wp_unslash($_POST[$key]));
		}
	}

	$updated = 0;
	foreach ($ids as $id) {
		if (! get_post($id)) {
			continue;
		}
		foreach ($fields as $key => $sanitizer) {
			if (! array_key_exists($key, $data)) {
				continue;
			}
			$val = $data[$key];
			if ($val === 0 && in_array($key, ['related_project', 'related_experience'], true)) {
				delete_post_meta($id, $key);
			} else {
				update_post_meta($id, $key, $val);
			}
		}
		if (isset($_POST['gc_category_ids_present'])) {
			$cat_ids = isset($_POST['gc_category_ids']) && is_array($_POST['gc_category_ids'])
				? array_values(array_filter(array_map('absint', $_POST['gc_category_ids'])))
				: [];
			wp_set_object_terms($id, $cat_ids, 'gc_category');
		}
		if (isset($_POST['gc_technology_ids_present'])) {
			$tech_ids = isset($_POST['gc_technology_ids']) && is_array($_POST['gc_technology_ids'])
				? array_values(array_filter(array_map('absint', $_POST['gc_technology_ids'])))
				: [];
			wp_set_object_terms($id, $tech_ids, 'technology');
		}
		$updated++;
	}

	wp_send_json_success(['updated' => $updated]);
}
add_action('wp_ajax_gc_media_modal_bulk_update', 'fct_gc_media_modal_bulk_update');

function fct_gc_get_media_terms()
{
	check_ajax_referer('gc_media_modal_bulk_update', 'nonce');

	if (! current_user_can('upload_files')) {
		wp_send_json_error([], 403);
	}

	$ids = isset($_POST['ids']) ? array_map('absint', (array) $_POST['ids']) : [];
	if (empty($ids)) {
		wp_send_json_success(['categories' => [], 'technologies' => []]);
		return;
	}

	$cats  = [];
	$techs = [];

	foreach ($ids as $id) {
		$terms = wp_get_object_terms($id, 'gc_category', ['fields' => 'id=>name']);
		if (! is_wp_error($terms)) {
			foreach ($terms as $tid => $name) {
				$cats[$tid] = $name;
			}
		}
		$terms = wp_get_object_terms($id, 'technology', ['fields' => 'id=>name']);
		if (! is_wp_error($terms)) {
			foreach ($terms as $tid => $name) {
				$techs[$tid] = $name;
			}
		}
	}

	wp_send_json_success([
		'categories'  => array_values(array_map(fn($id, $n) => ['id' => $id, 'label' => $n], array_keys($cats), $cats)),
		'technologies' => array_values(array_map(fn($id, $n) => ['id' => $id, 'label' => $n], array_keys($techs), $techs)),
	]);
}
add_action('wp_ajax_gc_get_media_terms', 'fct_gc_get_media_terms');

/**
 * Inject the bulk-select floating panel on the media library page only.
 * Uses a MutationObserver to detect when WordPress toggles the `.selected`
 * class on attachment grid items — no dependency on wp.media internals.
 */
function fct_gc_media_modal_bulk_editor()
{
	if (! is_admin() || ! function_exists('get_current_screen')) {
		return;
	}
	$screen = get_current_screen();
	if (! $screen || $screen->id !== 'upload') {
		return;
	}

	$projects    = get_posts([
		'post_type'      => 'project',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	]);
	$experiences = get_posts([
		'post_type'      => 'experience',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	]);
	$categories  = get_terms(['taxonomy' => 'gc_category', 'hide_empty' => false]);
	$technologies = get_terms(['taxonomy' => 'technology', 'hide_empty' => false]);

	$cfg = [
		'ajaxUrl'      => admin_url('admin-ajax.php'),
		'nonce'        => wp_create_nonce('gc_media_modal_bulk_update'),
		'projects'     => array_map(fn($p) => ['id' => $p->ID, 'label' => $p->post_title], $projects),
		'experiences'  => array_map(fn($p) => ['id' => $p->ID, 'label' => $p->post_title], $experiences),
		'categories'   => is_wp_error($categories) ? [] : array_map(fn($t) => ['id' => $t->term_id, 'label' => $t->name], $categories),
		'technologies' => is_wp_error($technologies) ? [] : array_map(fn($t) => ['id' => $t->term_id, 'label' => $t->name], $technologies),
	];
	?>
<script>
(function () {
	'use strict';

	var cfg = <?php echo wp_json_encode($cfg); ?>;

	/* ---- helpers ---- */
	function makeOpts(items) {
		return (items || []).map(function (it) {
			return '<option value="' + Number(it.id) + '">' + esc(it.label) + '</option>';
		}).join('');
	}
	function esc(s) {
		return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
	}
	function row(labelText, inputHtml) {
		return '<div style="margin-bottom:12px;">'
			+ '<label style="display:block;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#6c655b;margin-bottom:4px;">' + labelText + '</label>'
			+ inputHtml
			+ '</div>';
	}
	function sel(name, optHtml) {
		return '<select name="' + name + '" style="width:100%;max-width:100%;border:1px solid rgba(29,27,24,0.18);border-radius:6px;padding:5px 8px;background:#fff;color:#1d1b18;">' + optHtml + '</select>';
	}
	function renderTermTags(items) {
		if (!items || items.length === 0) return '<em style="color:#aaa;">aucune</em>';
		return items.map(function (it) {
			return '<span style="display:inline-block;background:#f3efe6;border:1px solid rgba(29,27,24,0.15);border-radius:4px;padding:2px 7px;margin:2px;font-size:11px;">' + esc(it.label) + '</span>';
		}).join('');
	}
	function makeCheckboxGroup(name, items) {
		var html = '<div id="gc-cb-' + name + '" style="max-height:130px;overflow-y:auto;border:1px solid rgba(29,27,24,0.18);border-radius:6px;padding:8px;background:#fff;">';
		(items || []).forEach(function (it) {
			html += '<label style="display:block;margin-bottom:4px;">'
				+ '<input type="checkbox" name="' + name + '[]" value="' + Number(it.id) + '" style="margin-right:5px;">'
				+ esc(it.label) + '</label>';
		});
		html += '</div>';
		return html;
	}
	function getCheckedValues(name) {
		var nodes = panel.querySelectorAll('input[name="' + name + '[]"]:checked');
		var vals = [];
		for (var i = 0; i < nodes.length; i++) vals.push(nodes[i].value);
		return vals;
	}
	var INPUT_STYLE = 'style="width:100%;box-sizing:border-box;border:1px solid rgba(29,27,24,0.18);border-radius:6px;padding:5px 8px;color:#1d1b18;background:#fff;"';

	/* ---- build panel ---- */
	var panel = document.createElement('div');
	panel.id = 'gc-bulk-panel';
	Object.assign(panel.style, {
		display:     'none',
		position:    'fixed',
		top:         '32px',
		right:       '0',
		bottom:      '0',
		width:       '280px',
		background:  '#fffaf2',
		borderLeft:  '1px solid rgba(29,27,24,0.12)',
		borderTop:   '3px solid #cd5c38',
		overflowY:   'auto',
		zIndex:      '99999',
		boxShadow:   '-4px 0 28px rgba(37,25,10,0.10)',
		fontFamily:  '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif',
		fontSize:    '13px',
		color:       '#1d1b18',
	});

	panel.innerHTML =
		'<div style="padding:14px 16px;background:#f3efe6;border-bottom:1px solid rgba(29,27,24,0.12);display:flex;align-items:center;justify-content:space-between;">'
		+   '<strong id="gc-bulk-count" style="font-size:12px;font-weight:700;letter-spacing:0.05em;text-transform:uppercase;color:#1d1b18;"></strong>'
		+   '<button type="button" id="gc-bulk-close" style="background:none;border:none;cursor:pointer;font-size:20px;line-height:1;color:#6c655b;padding:0;">&times;</button>'
		+   '<button type="button" id="gc-bulk-deselect" style="background:none;border:1px solid rgba(29,27,24,0.25);border-radius:4px;cursor:pointer;font-size:11px;font-weight:600;color:#6c655b;padding:3px 8px;margin-left:6px;">Tout désélectionner</button>'
		+ '</div>'
		+ '<div style="padding:16px;">'
		+   '<p style="margin:0 0 14px;color:#6c655b;font-size:11px;font-style:italic;">Laisser vide = ne pas modifier.</p>'
		+   row('Type de media',
				sel('gc_media_type',
					'<option value="">Ne pas changer</option>'
					+ '<option value="media">media</option>'
					+ '<option value="logo">logo</option>'
					+ '<option value="background">background</option>'))
		+   row('Texte FR', '<textarea name="gc_media_text_fr" rows="2" ' + INPUT_STYLE + ' placeholder="Ne pas changer"></textarea>')
		+   row('Texte EN', '<textarea name="gc_media_text_en" rows="2" ' + INPUT_STYLE + ' placeholder="Ne pas changer"></textarea>')
		+   row('Annee', '<input type="number" name="media_year" min="1900" max="2100" ' + INPUT_STYLE + ' placeholder="Ne pas changer">')
		+   row('Projet lie',
				sel('related_project',
					'<option value="">Ne pas changer</option>'
					+ '<option value="0">&#8212; Retirer &#8212;</option>'
					+ makeOpts(cfg.projects)))
		+   row('Experience',
				sel('related_experience',
					'<option value="">Ne pas changer</option>'
					+ '<option value="0">&#8212; Retirer &#8212;</option>'
					+ makeOpts(cfg.experiences)))
		+   row('Usage background',
				sel('background_usage',
					'<option value="">Ne pas changer</option>'
					+ '<option value="global">global</option>'
					+ '<option value="home">home</option>'
					+ '<option value="experience">experience</option>'
					+ '<option value="blog">blog</option>'
					+ '<option value="contact">contact</option>'))
		+   row('Catégories', makeCheckboxGroup('gc_category_ids', cfg.categories))
		+   row('Technologies', makeCheckboxGroup('gc_technology_ids', cfg.technologies))
		+   '<button type="button" id="gc-bulk-apply" style="width:100%;margin-top:4px;padding:10px 14px;background:#cd5c38;color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:600;letter-spacing:0.04em;cursor:pointer;transition:background .15s;">Appliquer</button>'
		+   '<p id="gc-bulk-msg" style="margin-top:8px;min-height:18px;font-size:12px;"></p>'
		+ '</div>';

	document.body.appendChild(panel);

	var countEl  = document.getElementById('gc-bulk-count');
	var msgEl    = document.getElementById('gc-bulk-msg');
	var catsDirty  = false;
	var techsDirty = false;

	/* ---- close button ---- */
	document.getElementById('gc-bulk-close').addEventListener('click', function () {
		panel.style.display = 'none';
	});

	document.getElementById('gc-bulk-deselect').addEventListener('click', function () {
		document.querySelectorAll('.attachments .attachment.selected').forEach(function (el) {
			el.click();
		});
	});

	/* ---- dirty flags when user manually changes a checkbox ---- */
	panel.addEventListener('change', function (e) {
		var name = e.target.name || '';
		if (name === 'gc_category_ids[]')  catsDirty  = true;
		if (name === 'gc_technology_ids[]') techsDirty = true;
	});

	/* ---- get selected attachment IDs from the DOM ---- */
	function getSelectedIds() {
		var nodes = document.querySelectorAll('.attachments .attachment.selected[data-id]');
		var ids = [];
		for (var i = 0; i < nodes.length; i++) {
			var id = parseInt(nodes[i].getAttribute('data-id'), 10);
			if (id > 0) ids.push(id);
		}
		return ids;
	}

	/* ---- show/hide logic ---- */
	var lastCount = 0;

	function loadTerms(ids) {
		/* reset checkboxes and dirty flags for new selection */
		panel.querySelectorAll('input[name="gc_category_ids[]"]').forEach(function (cb) { cb.checked = false; });
		panel.querySelectorAll('input[name="gc_technology_ids[]"]').forEach(function (cb) { cb.checked = false; });
		catsDirty  = false;
		techsDirty = false;

		var xhr = new XMLHttpRequest();
		xhr.open('POST', cfg.ajaxUrl, true);
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xhr.onreadystatechange = function () {
			if (xhr.readyState !== 4) return;
			try {
				var resp = JSON.parse(xhr.responseText);
				if (resp.success) {
					resp.data.categories.forEach(function (it) {
						var cb = panel.querySelector('input[name="gc_category_ids[]"][value="' + Number(it.id) + '"]');
						if (cb) cb.checked = true;
					});
					resp.data.technologies.forEach(function (it) {
						var cb = panel.querySelector('input[name="gc_technology_ids[]"][value="' + Number(it.id) + '"]');
						if (cb) cb.checked = true;
					});
				}
			} catch (e) {}
		};
		var parts = [
			'action=gc_get_media_terms',
			'nonce=' + encodeURIComponent(cfg.nonce),
		];
		ids.forEach(function (id) { parts.push('ids[]=' + id); });
		xhr.send(parts.join('&'));
	}

	function refresh() {
		var ids   = getSelectedIds();
		var count = ids.length;
		if (count === lastCount) return;
		lastCount = count;
		if (count >= 2) {
			countEl.textContent = count + ' images selectionnees';
			panel.style.display = 'block';
			loadTerms(ids);
		} else {
			panel.style.display = 'none';
		}
	}

	/* ---- MutationObserver: watch class changes inside .attachments ---- */
	var root = document;
	var obs = new MutationObserver(function (mutations) {
		for (var i = 0; i < mutations.length; i++) {
			if (mutations[i].type === 'attributes' && mutations[i].attributeName === 'class') {
				refresh();
				return;
			}
			if (mutations[i].type === 'childList') {
				refresh();
				return;
			}
		}
	});
	obs.observe(root.documentElement, {
		subtree:         true,
		attributes:      true,
		attributeFilter: ['class'],
		childList:       true,
	});

	/* ---- Apply button ---- */
	document.getElementById('gc-bulk-apply').addEventListener('click', function () {
		var ids = getSelectedIds();
		if (ids.length < 2) {
			msgEl.style.color = '#d63638';
			msgEl.textContent = 'Selectionnez au moins 2 images.';
			return;
		}

		var formEl = panel;
		function val(name) {
			var el = formEl.querySelector('[name="' + name + '"]');
			return el ? el.value : '';
		}

		var payload = {
			action: 'gc_media_modal_bulk_update',
			nonce:  cfg.nonce,
			ids:    ids,
			gc_media_type:      val('gc_media_type'),
			gc_media_text_fr:   val('gc_media_text_fr'),
			gc_media_text_en:   val('gc_media_text_en'),
			media_year:         val('media_year'),
			related_project:    val('related_project'),
			related_experience: val('related_experience'),
			background_usage:   val('background_usage'),
		};

		/* only include taxonomy data if user explicitly changed something */
		if (catsDirty) {
			payload.gc_category_ids_present = '1';
			payload.gc_category_ids = getCheckedValues('gc_category_ids');
		}
		if (techsDirty) {
			payload.gc_technology_ids_present = '1';
			payload.gc_technology_ids = getCheckedValues('gc_technology_ids');
		}

		var btn = document.getElementById('gc-bulk-apply');
		btn.disabled = true;
		msgEl.style.color = '#50575e';
		msgEl.textContent = 'Enregistrement...';

		var xhr = new XMLHttpRequest();
		xhr.open('POST', cfg.ajaxUrl, true);
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xhr.onreadystatechange = function () {
			if (xhr.readyState !== 4) return;
			btn.disabled = false;
			try {
				var resp = JSON.parse(xhr.responseText);
				if (resp.success) {
					msgEl.style.color = '#00a32a';
					msgEl.textContent = resp.data.updated + ' media(s) mis a jour.';
					/* Refresh Backbone models so the individual details panel
					   shows updated data without requiring a page reload. */
					if (window.wp && wp.media && wp.media.attachment) {
						ids.forEach(function (id) {
							wp.media.attachment(id).fetch();
						});
					}
				} else {
					msgEl.style.color = '#d63638';
					msgEl.textContent = (resp.data && resp.data.message) ? resp.data.message : 'Erreur.';
				}
			} catch (e) {
				msgEl.style.color = '#d63638';
				msgEl.textContent = 'Erreur de reponse.';
			}
		};

		var parts = [];
		for (var k in payload) {
			if (!Object.prototype.hasOwnProperty.call(payload, k)) continue;
			var v = payload[k];
			if (Array.isArray(v)) {
				v.forEach(function (item) {
					parts.push(encodeURIComponent(k + '[]') + '=' + encodeURIComponent(item));
				});
			} else {
				parts.push(encodeURIComponent(k) + '=' + encodeURIComponent(v));
			}
		}
		xhr.send(parts.join('&'));
	});
})();
</script>
	<?php
}
add_action('admin_footer', 'fct_gc_media_modal_bulk_editor');
