<?php

function fct_gc_register_content_types()
{
	register_post_type('project', [
		'labels' => [
			'name' => 'Projets',
			'singular_name' => 'Projet',
			'add_new_item' => 'Ajouter un projet',
			'edit_item' => 'Modifier le projet',
			'new_item' => 'Nouveau projet',
			'view_item' => 'Voir le projet',
			'search_items' => 'Rechercher des projets',
		],
		'public' => true,
		'show_in_rest' => true,
		'menu_icon' => 'dashicons-portfolio',
		'has_archive' => true,
		'rewrite' => ['slug' => 'projets'],
		'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions'],
	]);

	register_post_type('experience', [
		'labels' => [
			'name' => 'Expériences',
			'singular_name' => 'Expérience',
			'add_new_item' => 'Ajouter une expérience',
			'edit_item' => 'Modifier l\'expérience',
			'new_item' => 'Nouvelle expérience',
			'view_item' => 'Voir l\'expérience',
			'search_items' => 'Rechercher des expériences',
		],
		'public' => true,
		'show_in_rest' => true,
		'menu_icon' => 'dashicons-clock',
		'has_archive' => true,
		'rewrite' => ['slug' => 'experiences'],
		'supports' => ['title', 'editor', 'thumbnail', 'revisions'],
	]);

	register_taxonomy('gc_category', ['project', 'post', 'experience'], [
		'labels' => [
			'name' => 'Catégories',
			'singular_name' => 'Catégorie',
		],
		'public' => true,
		'hierarchical' => true,
		'show_in_rest' => true,
		'rewrite' => ['slug' => 'categorie'],
	]);

	register_taxonomy('position', ['project', 'experience'], [
		'labels' => [
			'name' => 'Positions',
			'singular_name' => 'Position',
		],
		'public' => true,
		'hierarchical' => true,
		'show_in_rest' => true,
		'rewrite' => ['slug' => 'position'],
	]);

	register_taxonomy('technology', ['project', 'post'], [
		'labels' => [
			'name' => 'Technologies',
			'singular_name' => 'Technologie',
		],
		'public' => true,
		'hierarchical' => true,
		'show_in_rest' => true,
		'rewrite' => ['slug' => 'technologie'],
	]);

	register_post_type('gc_video', [
		'labels' => [
			'name'          => 'Vidéos',
			'singular_name' => 'Vidéo',
			'add_new_item'  => 'Ajouter une vidéo',
			'edit_item'     => 'Modifier la vidéo',
			'new_item'      => 'Nouvelle vidéo',
			'view_item'     => 'Voir la vidéo',
			'search_items'  => 'Rechercher des vidéos',
		],
		'public'       => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-video-alt3',
		'has_archive'  => false,
		'rewrite'      => ['slug' => 'videos'],
		'supports'     => ['title', 'revisions'],
	]);
}

add_action('init', 'fct_gc_register_content_types');

function fct_gc_register_media_taxonomy_support()
{
	register_taxonomy_for_object_type('gc_category', 'attachment');
	register_taxonomy_for_object_type('technology', 'attachment');
	register_taxonomy_for_object_type('gc_category', 'gc_video');
	register_taxonomy_for_object_type('technology', 'gc_video');
}

add_action('init', 'fct_gc_register_media_taxonomy_support', 20);

function fct_gc_seed_taxonomy_terms()
{
	$terms = [
		'gc_category' => [
			'Web design',
			'UX-UI design',
			'Front-end development',
			'Game design',
			'Level design',
			'3D production',
			'Graphism',
			'Concept art',
			'Pixel art',
			'Animation',
			'Motion design',
			'Editing',
			'Video restoration',
			'Photo editing',
			'Digital grading',
		],
		'position' => [
			'Web designer',
			'UX-UI designer',
			'Front-end developer',
			'Game designer',
			'Level designer',
			'3D artist',
			'Graphist',
			'Concept artist',
			'Pixel artist',
			'Animator',
			'Motion designer',
			'Editor',
			'Video artist',
			'2D artist',
		],
		'technology' => [
			'Photoshop',
			'After effects',
			'Premiere Pro',
			'Illustrator',
			'Maya',
			'Marmoset',
			'Z-brush',
			'Unity',
			'Unreal Engine',
			'Topaz Video AI',
			'Figma',
			'HTML',
			'JavaScript',
			'CSS',
			'SCSS',
			'PHP',
			'WordPress',
		],
	];

	foreach ($terms as $taxonomy => $taxonomy_terms) {
		foreach ($taxonomy_terms as $term_name) {
			if (! term_exists($term_name, $taxonomy)) {
				wp_insert_term($term_name, $taxonomy);
			}
		}
	}

	flush_rewrite_rules();
}

add_action('after_switch_theme', 'fct_gc_seed_taxonomy_terms');

function fct_gc_render_related_experience_meta_box($post, $nonce_action, $nonce_name)
{
	wp_nonce_field($nonce_action, $nonce_name);

	$selected_experience_id = (int) get_post_meta($post->ID, 'related_experience', true);
	$experiences = get_posts([
		'post_type' => 'experience',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'orderby' => 'title',
		'order' => 'ASC',
	]);

	echo '<p><label for="gc_related_experience">Choisir une experience unique:</label></p>';
	echo '<select id="gc_related_experience" name="gc_related_experience" style="width:100%;">';
	echo '<option value="">Aucune experience</option>';

	foreach ($experiences as $experience) {
		echo '<option value="' . esc_attr((string) $experience->ID) . '" ' . selected($selected_experience_id, (int) $experience->ID, false) . '>' . esc_html($experience->post_title) . '</option>';
	}

	echo '</select>';
}

function fct_gc_add_project_relationship_meta_box()
{
	add_meta_box(
		'gc_project_related_experience',
		'Experience liee',
		'fct_gc_render_project_relationship_meta_box',
		'project',
		'side',
		'default'
	);

	add_meta_box(
		'gc_project_cover_image',
		'Cover image',
		'fct_gc_render_project_cover_image_meta_box',
		'project',
		'side',
		'default'
	);

	add_meta_box(
		'gc_project_cover_video',
		'Cover media YouTube',
		'fct_gc_render_project_cover_video_meta_box',
		'project',
		'side',
		'default'
	);

	add_meta_box(
		'gc_project_dates',
		'Dates du projet',
		'fct_gc_render_project_dates_meta_box',
		'project',
		'side',
		'default'
	);
}

add_action('add_meta_boxes_project', 'fct_gc_add_project_relationship_meta_box');

function fct_gc_render_project_relationship_meta_box($post)
{
	fct_gc_render_related_experience_meta_box($post, 'gc_project_relationship_nonce', 'gc_project_relationship_nonce');
}

function fct_gc_render_project_cover_image_meta_box($post)
{
	wp_nonce_field('gc_project_cover_image_nonce', 'gc_project_cover_image_nonce');
	$image_id  = (int) get_post_meta($post->ID, 'cover_image_id', true);
	$image_url = $image_id ? (string) wp_get_attachment_image_url($image_id, 'medium') : '';
	?>
	<img id="gc-cover-image-preview" src="<?php echo esc_url($image_url); ?>" style="width:100%;height:auto;display:<?php echo $image_url ? 'block' : 'none'; ?>;margin-bottom:8px;" />
	<input type="hidden" id="gc_cover_image_id" name="gc_cover_image_id" value="<?php echo esc_attr($image_id ?: ''); ?>" />
	<button type="button" id="gc-cover-image-select" class="button button-secondary" style="width:100%;"><?php echo $image_id ? esc_html("Changer l'image") : esc_html('Choisir une image'); ?></button>
	<button type="button" id="gc-cover-image-remove" class="button" style="width:100%;margin-top:4px;display:<?php echo $image_id ? 'block' : 'none'; ?>;"><?php esc_html_e('Supprimer'); ?></button>
	<p class="description" style="margin-top:6px;">Affiché en tête de page. Si absent, la Featured Image est utilisée en fallback.</p>
	<script>
	jQuery(function($) {
		var frame;
		$('#gc-cover-image-select').on('click', function(e) {
			e.preventDefault();
			if (frame) { frame.open(); return; }
			frame = wp.media({ title: 'Cover image', button: { text: 'Sélectionner' }, multiple: false });
			frame.on('select', function() {
				var att = frame.state().get('selection').first().toJSON();
				$('#gc_cover_image_id').val(att.id);
				var url = att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url;
				$('#gc-cover-image-preview').attr('src', url).show();
				$('#gc-cover-image-remove').show();
				$('#gc-cover-image-select').text("Changer l'image");
			});
			frame.open();
		});
		$('#gc-cover-image-remove').on('click', function(e) {
			e.preventDefault();
			$('#gc_cover_image_id').val('');
			$('#gc-cover-image-preview').hide().attr('src', '');
			$(this).hide();
			$('#gc-cover-image-select').text('Choisir une image');
		});
	});
	</script>
	<?php
}

function fct_gc_render_project_cover_video_meta_box($post)
{
	wp_nonce_field('gc_project_cover_video_nonce', 'gc_project_cover_video_nonce');
	$url = (string) get_post_meta($post->ID, 'cover_youtube_url', true);
	echo '<p><label for="gc_cover_youtube_url"><strong>URL YouTube (cover)</strong></label></p>';
	echo '<input type="url" id="gc_cover_youtube_url" name="gc_cover_youtube_url" value="' . esc_attr($url) . '" style="width:100%;" placeholder="https://www.youtube.com/watch?v=..." />';
	echo '<p class="description" style="margin-top:6px;">Prend le dessus sur le champ \'main visual\' ACF. Laisser vide pour utiliser l\'image.</p>';
}

function fct_gc_format_date_for_input($value)
{
	$value = trim((string) $value);

	if ($value === '') {
		return '';
	}

	if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1) {
		return $value;
	}

	if (preg_match('/^\d{8}$/', $value) === 1) {
		$year = substr($value, 0, 4);
		$month = substr($value, 4, 2);
		$day = substr($value, 6, 2);

		return $year . '-' . $month . '-' . $day;
	}

	$timestamp = strtotime($value);

	return $timestamp ? gmdate('Y-m-d', $timestamp) : '';
}

function fct_gc_render_project_dates_meta_box($post)
{
	wp_nonce_field('gc_project_dates_nonce', 'gc_project_dates_nonce');

	$start_date = fct_gc_format_date_for_input(get_post_meta($post->ID, 'start_date', true));
	$end_date = fct_gc_format_date_for_input(get_post_meta($post->ID, 'end_date', true));

	echo '<p><label for="gc_project_start_date">Start date</label></p>';
	echo '<input type="date" id="gc_project_start_date" name="gc_project_start_date" value="' . esc_attr($start_date) . '" style="width:100%;" />';

	echo '<p style="margin-top:10px;"><label for="gc_project_end_date">End date</label></p>';
	echo '<input type="date" id="gc_project_end_date" name="gc_project_end_date" value="' . esc_attr($end_date) . '" style="width:100%;" />';
}

function fct_gc_add_post_relationship_meta_box()
{
	add_meta_box(
		'gc_post_related_experience',
		'Experience liee',
		'fct_gc_render_post_relationship_meta_box',
		'post',
		'side',
		'default'
	);
}

add_action('add_meta_boxes_post', 'fct_gc_add_post_relationship_meta_box');

function fct_gc_render_post_relationship_meta_box($post)
{
	fct_gc_render_related_experience_meta_box($post, 'gc_post_relationship_nonce', 'gc_post_relationship_nonce');
}

function fct_gc_save_related_experience_meta($post_id, $nonce_action, $nonce_name)
{
	if (! isset($_POST[$nonce_name])) {
		return;
	}

	if (! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[$nonce_name])), $nonce_action)) {
		return;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	if (! current_user_can('edit_post', $post_id)) {
		return;
	}

	$related_experience = isset($_POST['gc_related_experience']) ? (int) $_POST['gc_related_experience'] : 0;

	if ($related_experience > 0) {
		update_post_meta($post_id, 'related_experience', $related_experience);
		return;
	}

	delete_post_meta($post_id, 'related_experience');
}

function fct_gc_save_project_relationship_meta_box($post_id)
{
	fct_gc_save_related_experience_meta($post_id, 'gc_project_relationship_nonce', 'gc_project_relationship_nonce');
}

add_action('save_post_project', 'fct_gc_save_project_relationship_meta_box');

function fct_gc_save_project_cover_video_meta_box($post_id)
{
	if (! isset($_POST['gc_project_cover_video_nonce'])) {
		return;
	}
	if (! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['gc_project_cover_video_nonce'])), 'gc_project_cover_video_nonce')) {
		return;
	}
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}
	if (! current_user_can('edit_post', $post_id)) {
		return;
	}
	$url  = isset($_POST['gc_cover_youtube_url']) ? sanitize_url(wp_unslash($_POST['gc_cover_youtube_url'])) : '';
	$host = $url ? (string) wp_parse_url($url, PHP_URL_HOST) : '';
	if (in_array($host, ['www.youtube.com', 'youtube.com', 'youtu.be'], true)) {
		update_post_meta($post_id, 'cover_youtube_url', $url);
	} else {
		delete_post_meta($post_id, 'cover_youtube_url');
	}
}
add_action('save_post_project', 'fct_gc_save_project_cover_video_meta_box');

function fct_gc_enqueue_project_cover_image_media($hook)
{
	global $post;
	if (! in_array($hook, ['post.php', 'post-new.php'], true)) {
		return;
	}
	if (isset($post) && $post->post_type === 'project') {
		wp_enqueue_media();
	}
}
add_action('admin_enqueue_scripts', 'fct_gc_enqueue_project_cover_image_media');

function fct_gc_save_project_cover_image_meta_box($post_id)
{
	if (! isset($_POST['gc_project_cover_image_nonce'])) {
		return;
	}
	if (! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['gc_project_cover_image_nonce'])), 'gc_project_cover_image_nonce')) {
		return;
	}
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}
	if (! current_user_can('edit_post', $post_id)) {
		return;
	}
	$image_id = isset($_POST['gc_cover_image_id']) ? (int) $_POST['gc_cover_image_id'] : 0;
	if ($image_id > 0 && get_post_type($image_id) === 'attachment') {
		update_post_meta($post_id, 'cover_image_id', $image_id);
	} else {
		delete_post_meta($post_id, 'cover_image_id');
	}
}
add_action('save_post_project', 'fct_gc_save_project_cover_image_meta_box');

function fct_gc_save_project_dates_meta_box($post_id)
{
	if (! isset($_POST['gc_project_dates_nonce'])) {
		return;
	}

	if (! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['gc_project_dates_nonce'])), 'gc_project_dates_nonce')) {
		return;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	if (! current_user_can('edit_post', $post_id)) {
		return;
	}

	$start_date = isset($_POST['gc_project_start_date']) ? sanitize_text_field(wp_unslash($_POST['gc_project_start_date'])) : '';
	$end_date = isset($_POST['gc_project_end_date']) ? sanitize_text_field(wp_unslash($_POST['gc_project_end_date'])) : '';

	if ($start_date !== '') {
		update_post_meta($post_id, 'start_date', $start_date);
	} else {
		delete_post_meta($post_id, 'start_date');
	}

	if ($end_date !== '') {
		update_post_meta($post_id, 'end_date', $end_date);
	} else {
		delete_post_meta($post_id, 'end_date');
	}
}

add_action('save_post_project', 'fct_gc_save_project_dates_meta_box');

function fct_gc_save_post_relationship_meta_box($post_id)
{
	fct_gc_save_related_experience_meta($post_id, 'gc_post_relationship_nonce', 'gc_post_relationship_nonce');
}

add_action('save_post_post', 'fct_gc_save_post_relationship_meta_box');

/* ==========================================================================
   CPT gc_video — YouTube URL metabox
   ========================================================================== */

function fct_gc_add_video_meta_boxes()
{
	add_meta_box(
		'gc_video_details',
		'Détails vidéo',
		'fct_gc_render_video_meta_box',
		'gc_video',
		'normal',
		'high'
	);
}
add_action('add_meta_boxes_gc_video', 'fct_gc_add_video_meta_boxes');

function fct_gc_render_video_meta_box($post)
{
	wp_nonce_field('gc_video_meta_nonce', 'gc_video_meta_nonce');

	$youtube_url    = (string) get_post_meta($post->ID, 'youtube_url', true);
	$media_year     = (string) get_post_meta($post->ID, 'media_year', true);
	$text_fr        = (string) get_post_meta($post->ID, 'gc_media_text_fr', true);
	$text_en        = (string) get_post_meta($post->ID, 'gc_media_text_en', true);
	$related_project    = (int) get_post_meta($post->ID, 'related_project', true);
	$related_experience = (int) get_post_meta($post->ID, 'related_experience', true);

	$projects = get_posts([
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

	echo '<table class="form-table"><tbody>';

	echo '<tr><th><label for="gc_youtube_url">URL YouTube</label></th>';
	echo '<td><input type="url" id="gc_youtube_url" name="gc_youtube_url" value="' . esc_attr($youtube_url) . '" style="width:100%;" placeholder="https://www.youtube.com/watch?v=..." />';
	echo '<p class="description">URL complète YouTube ou youtu.be.</p></td></tr>';

	echo '<tr><th><label for="gc_video_year">Année</label></th>';
	echo '<td><input type="number" id="gc_video_year" name="gc_video_year" value="' . esc_attr($media_year) . '" min="1900" max="2100" style="width:100px;" /></td></tr>';

	echo '<tr><th><label for="gc_video_text_fr">Description FR</label></th>';
	echo '<td><textarea id="gc_video_text_fr" name="gc_video_text_fr" rows="3" style="width:100%;">' . esc_textarea($text_fr) . '</textarea></td></tr>';

	echo '<tr><th><label for="gc_video_text_en">Description EN</label></th>';
	echo '<td><textarea id="gc_video_text_en" name="gc_video_text_en" rows="3" style="width:100%;">' . esc_textarea($text_en) . '</textarea></td></tr>';

	echo '<tr><th><label for="gc_video_related_project">Projet lié</label></th>';
	echo '<td><select id="gc_video_related_project" name="gc_video_related_project" style="width:100%;">';
	echo '<option value="">Aucun projet</option>';
	foreach ($projects as $project) {
		echo '<option value="' . esc_attr((string) $project->ID) . '" ' . selected($related_project, (int) $project->ID, false) . '>' . esc_html($project->post_title) . '</option>';
	}
	echo '</select></td></tr>';

	echo '<tr><th><label for="gc_video_related_experience">Expérience liée</label></th>';
	echo '<td><select id="gc_video_related_experience" name="gc_video_related_experience" style="width:100%;">';
	echo '<option value="">Aucune expérience</option>';
	foreach ($experiences as $experience) {
		echo '<option value="' . esc_attr((string) $experience->ID) . '" ' . selected($related_experience, (int) $experience->ID, false) . '>' . esc_html($experience->post_title) . '</option>';
	}
	echo '</select></td></tr>';

	echo '</tbody></table>';
}

function fct_gc_save_video_meta_box($post_id)
{
	if (! isset($_POST['gc_video_meta_nonce'])) {
		return;
	}

	if (! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['gc_video_meta_nonce'])), 'gc_video_meta_nonce')) {
		return;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	if (! current_user_can('edit_post', $post_id)) {
		return;
	}

	if (isset($_POST['gc_youtube_url'])) {
		$url = sanitize_url(wp_unslash($_POST['gc_youtube_url']));
		$host = wp_parse_url($url, PHP_URL_HOST);
		if (in_array((string) $host, ['www.youtube.com', 'youtube.com', 'youtu.be'], true)) {
			update_post_meta($post_id, 'youtube_url', $url);
		} else {
			delete_post_meta($post_id, 'youtube_url');
		}
	}

	$year = isset($_POST['gc_video_year']) ? (int) $_POST['gc_video_year'] : 0;
	if ($year > 0) {
		update_post_meta($post_id, 'media_year', $year);
	} else {
		delete_post_meta($post_id, 'media_year');
	}

	if (isset($_POST['gc_video_text_fr'])) {
		update_post_meta($post_id, 'gc_media_text_fr', sanitize_textarea_field(wp_unslash($_POST['gc_video_text_fr'])));
	}

	if (isset($_POST['gc_video_text_en'])) {
		update_post_meta($post_id, 'gc_media_text_en', sanitize_textarea_field(wp_unslash($_POST['gc_video_text_en'])));
	}

	$related_project = isset($_POST['gc_video_related_project']) ? (int) $_POST['gc_video_related_project'] : 0;
	if ($related_project > 0) {
		update_post_meta($post_id, 'related_project', $related_project);
	} else {
		delete_post_meta($post_id, 'related_project');
	}

	$related_experience = isset($_POST['gc_video_related_experience']) ? (int) $_POST['gc_video_related_experience'] : 0;
	if ($related_experience > 0) {
		update_post_meta($post_id, 'related_experience', $related_experience);
	} else {
		delete_post_meta($post_id, 'related_experience');
	}
}
add_action('save_post_gc_video', 'fct_gc_save_video_meta_box');