<?php
/*
Template Name: Galerie
*/

get_header();

global $wpdb;

$selected_year = isset($_GET['year']) ? (int) $_GET['year'] : 0;
$selected_category = isset($_GET['gc_category']) ? sanitize_text_field(wp_unslash($_GET['gc_category'])) : '';
$selected_project = isset($_GET['project']) ? (int) $_GET['project'] : 0;
$selected_technology = isset($_GET['technology']) ? sanitize_text_field(wp_unslash($_GET['technology'])) : '';

$gallery_args = [
	'post_type' => 'attachment',
	'post_status' => 'inherit',
	'post_mime_type' => ['image', 'video'],
	'posts_per_page' => 30,
	'paged' => max(1, get_query_var('paged', 1)),
	'orderby' => 'date',
	'order' => 'DESC',
];

$tax_query = [];

$meta_query = [];

$meta_query[] = [
	'key' => 'gc_media_type',
	'value' => 'media',
	'compare' => '=',
];

if ($selected_year) {
	$meta_query[] = [
		'key' => 'media_year',
		'value' => (int) $selected_year,
		'compare' => '=',
		'type' => 'NUMERIC',
	];
}

if ($selected_category) {
	$tax_query[] = [
		'taxonomy' => 'gc_category',
		'field' => 'slug',
		'terms' => $selected_category,
	];
}

if ($selected_technology) {
	$tax_query[] = [
		'taxonomy' => 'technology',
		'field' => 'slug',
		'terms' => $selected_technology,
	];
}

if ($tax_query) {
	$gallery_args['tax_query'] = $tax_query;
}

if ($selected_project) {
	$meta_query[] = [
		'relation' => 'OR',
		[
			'key' => 'related_project',
			'value' => $selected_project,
			'compare' => '=',
			'type' => 'NUMERIC',
		],
		[
			'key' => 'related_projects',
			'value' => '"' . $selected_project . '"',
			'compare' => 'LIKE',
		],
	];
}

if ($meta_query) {
	$gallery_args['meta_query'] = $meta_query;
}

$gallery_query = new WP_Query($gallery_args);

// Video query — same category/technology filters, no pagination needed.
$video_args = [
	'post_type'      => 'gc_video',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'orderby'        => 'date',
	'order'          => 'DESC',
];
$video_tax_query = [];
if ($selected_category) {
	$video_tax_query[] = [
		'taxonomy' => 'gc_category',
		'field'    => 'slug',
		'terms'    => $selected_category,
	];
}
if ($selected_technology) {
	$video_tax_query[] = [
		'taxonomy' => 'technology',
		'field'    => 'slug',
		'terms'    => $selected_technology,
	];
}
if ($video_tax_query) {
	$video_args['tax_query'] = $video_tax_query;
}
$video_query = new WP_Query($video_args);

$years = $wpdb->get_col("SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->postmeta} t ON t.post_id = pm.post_id WHERE pm.meta_key = 'media_year' AND t.meta_key = 'gc_media_type' AND t.meta_value = 'media' AND pm.meta_value <> '' ORDER BY pm.meta_value DESC");
$projects = get_posts(['post_type' => 'project', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC']);
?>

<main class="gc-main gc-main--flow">
	<?php while (have_posts()) : the_post(); ?>
		<section class="gc-page-hero">
			<p class="gc-eyebrow"><?php echo esc_html(gc_t('Galerie', 'Gallery')); ?></p>
			<h1><?php the_title(); ?></h1>
			<div class="gc-content-copy"><?php the_content(); ?></div>
		</section>
	<?php endwhile; ?>

	<form class="gc-filters" method="get">
		<label>
			<span><?php echo esc_html(gc_t('Année', 'Year')); ?></span>
			<select name="year">
				<option value=""><?php echo esc_html(gc_t('Toutes', 'All')); ?></option>
				<?php foreach ($years as $year) : ?>
					<option value="<?php echo esc_attr($year); ?>" <?php selected($selected_year, (int) $year); ?>><?php echo esc_html($year); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
		<label>
			<span><?php echo esc_html(gc_t('Catégorie', 'Category')); ?></span>
			<?php wp_dropdown_categories([
				'taxonomy' => 'gc_category',
				'name' => 'gc_category',
				'show_option_all' => gc_t('Toutes', 'All'),
				'value_field' => 'slug',
				'selected' => $selected_category,
				'hide_empty' => false,
			]); ?>
		</label>
		<label>
			<span><?php echo esc_html(gc_t('Projet', 'Project')); ?></span>
			<select name="project">
				<option value=""><?php echo esc_html(gc_t('Tous', 'All')); ?></option>
				<?php foreach ($projects as $project) : ?>
					<option value="<?php echo esc_attr($project->ID); ?>" <?php selected($selected_project, $project->ID); ?>><?php echo esc_html($project->post_title); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
		<label>
			<span><?php echo esc_html(gc_t('Technologie', 'Technology')); ?></span>
			<?php wp_dropdown_categories([
				'taxonomy' => 'technology',
				'name' => 'technology',
				'show_option_all' => gc_t('Toutes', 'All'),
				'value_field' => 'slug',
				'selected' => $selected_technology,
				'hide_empty' => false,
			]); ?>
		</label>
		<button class="gc-button" type="submit"><?php echo esc_html(gc_t('Filtrer', 'Filter')); ?></button>
	</form>

	<div class="gc-gallery-grid">
		<?php if ($gallery_query->have_posts()) : ?>
			<?php while ($gallery_query->have_posts()) : $gallery_query->the_post(); ?>
				<figure class="gc-gallery-grid__item">
					<?php echo wp_kses_post(gc_get_attachment_media_html(get_the_ID(), 'large', 'gc-gallery-grid__image')); ?>
					<?php if ($media_year = gc_get_attachment_year(get_the_ID())) : ?>
						<div class="gc-gallery-grid__year"><?php echo esc_html((string) $media_year); ?></div>
					<?php endif; ?>
					<?php if ($media_text = gc_get_attachment_translated_text(get_the_ID())) : ?>
						<figcaption class="gc-gallery-grid__caption"><?php echo esc_html($media_text); ?></figcaption>
					<?php endif; ?>
					<div class="gc-term-list">
						<?php echo wp_kses_post(gc_render_term_links(get_the_ID(), 'gc_category')); ?>
						<?php echo wp_kses_post(gc_render_term_links(get_the_ID(), 'technology')); ?>
					</div>
				</figure>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		<?php endif; ?>
		<?php if ($video_query->have_posts()) : ?>
			<?php while ($video_query->have_posts()) : $video_query->the_post(); ?>
				<?php get_template_part('template-parts/card', 'video'); ?>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		<?php endif; ?>
		<?php if (! $gallery_query->have_posts() && ! $video_query->have_posts()) : ?>
			<p><?php echo esc_html(gc_t('Aucun média ne correspond aux filtres sélectionnés.', 'No media matches your selected filters.')); ?></p>
		<?php endif; ?>
	</div>

	<?php
	echo wp_kses_post(paginate_links([
		'total' => $gallery_query->max_num_pages,
		'current' => max(1, get_query_var('paged', 1)),
	]));
	?>
</main>

<?php get_footer(); ?>