<?php

get_header();

$selected_category = isset($_GET['gc_category']) ? sanitize_text_field(wp_unslash($_GET['gc_category'])) : '';
$selected_technology = isset($_GET['technology']) ? sanitize_text_field(wp_unslash($_GET['technology'])) : '';

$tax_query = [];

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

$project_query = new WP_Query([
	'post_type' => 'project',
	'post_status' => 'publish',
	'paged' => max(1, get_query_var('paged', 1)),
	'tax_query' => $tax_query ?: [],
]);
?>

<main class="gc-main gc-archive-layout">
	<section class="gc-page-hero">
		<p class="gc-eyebrow">Portfolio</p>
		<h1>Projets</h1>
		<p>Base d'archive prête pour alimenter la home, les pages expertise et les fiches projet.</p>
	</section>

	<form class="gc-filters" method="get">
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
			<span>Technologie</span>
			<?php wp_dropdown_categories([
				'taxonomy' => 'technology',
				'name' => 'technology',
				'show_option_all' => 'Toutes',
				'value_field' => 'slug',
				'selected' => $selected_technology,
				'hide_empty' => false,
			]); ?>
		</label>

		<button class="gc-button" type="submit">Filtrer</button>
	</form>

	<div class="gc-card-grid">
		<?php if ($project_query->have_posts()) : ?>
			<?php while ($project_query->have_posts()) : $project_query->the_post(); ?>
				<?php get_template_part('template-parts/card', 'work'); ?>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		<?php else : ?>
			<p>Aucun projet ne correspond aux filtres sélectionnés.</p>
		<?php endif; ?>
	</div>

	<?php
	echo wp_kses_post(paginate_links([
		'total' => $project_query->max_num_pages,
		'current' => max(1, get_query_var('paged', 1)),
	]));
	?>
</main>

<?php get_footer(); ?>