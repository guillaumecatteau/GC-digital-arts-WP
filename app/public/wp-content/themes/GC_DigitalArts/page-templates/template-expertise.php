<?php
/*
Template Name: Expertise
*/

get_header();

$page_id = get_queried_object_id();
$term = gc_get_selected_expertise_term($page_id);

$query_args = [
	'post_type' => 'project',
	'post_status' => 'publish',
	'posts_per_page' => -1,
];

if ($term instanceof WP_Term) {
	$query_args['tax_query'] = [[
		'taxonomy' => 'gc_category',
		'field' => 'term_id',
		'terms' => $term->term_id,
	]];
}

$projects_query = new WP_Query($query_args);
?>

<main class="gc-main gc-main--flow">
	<?php while (have_posts()) : the_post(); ?>
		<section class="gc-page-hero">
			<p class="gc-eyebrow">Expertise</p>
			<h1><?php the_title(); ?></h1>
			<div class="gc-content-copy"><?php the_content(); ?></div>
		</section>
	<?php endwhile; ?>

	<section class="gc-section gc-section--panel">
		<div class="gc-section__heading gc-section__heading--split">
			<div>
				<p class="gc-eyebrow">Portfolio associé</p>
				<h2><?php echo esc_html($term instanceof WP_Term ? $term->name : 'Projets liés'); ?></h2>
			</div>
			<?php if ($term instanceof WP_Term) : ?>
				<a class="gc-text-link" href="<?php echo esc_url(get_term_link($term)); ?>">Voir l'archive de catégorie</a>
			<?php endif; ?>
		</div>

		<div class="gc-card-grid">
			<?php if ($projects_query->have_posts()) : ?>
				<?php while ($projects_query->have_posts()) : $projects_query->the_post(); ?>
					<?php get_template_part('template-parts/card', 'work'); ?>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<p>Aucun projet n'est encore relié à cette expertise. Associez une catégorie ACF ou la taxonomie gc_category sur les projets.</p>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php get_footer(); ?>