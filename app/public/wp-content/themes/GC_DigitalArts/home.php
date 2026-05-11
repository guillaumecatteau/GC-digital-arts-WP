<?php

get_header();

global $wpdb;

$selected_year = isset($_GET['year']) ? (int) $_GET['year'] : 0;
$selected_category = isset($_GET['gc_category']) ? sanitize_text_field(wp_unslash($_GET['gc_category'])) : '';
$selected_project = isset($_GET['project']) ? (int) $_GET['project'] : 0;

$query_args = [
	'post_type' => 'post',
	'post_status' => 'publish',
	'paged' => max(1, get_query_var('paged', 1)),
];

if ($selected_year) {
	$query_args['date_query'] = [['year' => $selected_year]];
}

if ($selected_category) {
	$query_args['tax_query'] = [[
		'taxonomy' => 'gc_category',
		'field' => 'slug',
		'terms' => $selected_category,
	]];
}

if ($selected_project) {
	$query_args['meta_query'] = [[
		'key' => 'related_projects',
		'value' => '"' . $selected_project . '"',
		'compare' => 'LIKE',
	]];
}

$blog_query = new WP_Query($query_args);
$years = $wpdb->get_col("SELECT DISTINCT YEAR(post_date) FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date DESC");
$projects = get_posts(['post_type' => 'project', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC']);
?>

<main class="gc-main gc-archive-layout">
	<section class="gc-page-hero">
		<p class="gc-eyebrow">Blog</p>
		<h1><?php echo esc_html(get_the_title(get_option('page_for_posts')) ?: 'Articles'); ?></h1>
		<p>Filtrage prévu par année, catégorie et projet relié via ACF.</p>
	</section>

	<form class="gc-filters" method="get">
		<label>
			<span>Année</span>
			<select name="year">
				<option value="">Toutes</option>
				<?php foreach ($years as $year) : ?>
					<option value="<?php echo esc_attr($year); ?>" <?php selected($selected_year, (int) $year); ?>><?php echo esc_html($year); ?></option>
				<?php endforeach; ?>
			</select>
		</label>

		<label>
			<span>Catégorie</span>
			<?php wp_dropdown_categories([
				'taxonomy' => 'gc_category',
				'name' => 'gc_category',
				'show_option_all' => 'Toutes',
				'value_field' => 'slug',
				'selected' => $selected_category,
				'hide_empty' => false,
			]); ?>
		</label>

		<label>
			<span>Projet</span>
			<select name="project">
				<option value="">Tous</option>
				<?php foreach ($projects as $project) : ?>
					<option value="<?php echo esc_attr($project->ID); ?>" <?php selected($selected_project, $project->ID); ?>><?php echo esc_html($project->post_title); ?></option>
				<?php endforeach; ?>
			</select>
		</label>

		<button class="gc-button" type="submit">Filtrer</button>
	</form>

	<div class="gc-card-grid gc-card-grid--posts">
		<?php if ($blog_query->have_posts()) : ?>
			<?php while ($blog_query->have_posts()) : $blog_query->the_post(); ?>
				<?php get_template_part('template-parts/card', 'work'); ?>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		<?php else : ?>
			<p>Aucun article ne correspond aux filtres sélectionnés.</p>
		<?php endif; ?>
	</div>

	<?php
	echo wp_kses_post(paginate_links([
		'total' => $blog_query->max_num_pages,
		'current' => max(1, get_query_var('paged', 1)),
	]));
	?>
</main>

<?php get_footer(); ?>