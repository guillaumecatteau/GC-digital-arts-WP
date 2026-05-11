<?php

get_header();

$experience_query = new WP_Query([
	'post_type' => 'experience',
	'post_status' => 'publish',
	'posts_per_page' => -1,
	'meta_key' => 'start_date',
	'orderby' => 'meta_value',
	'order' => 'DESC',
]);
?>

<main class="gc-main gc-main--flow">
	<section class="gc-page-hero">
		<p class="gc-eyebrow">Expérience</p>
		<h1>Timeline</h1>
		<p>Les expériences et formations peuvent être saisies dans le CPT dédié puis mises en avant ici et sur la page Expérience.</p>
	</section>

	<div class="gc-timeline">
		<?php if ($experience_query->have_posts()) : ?>
			<?php while ($experience_query->have_posts()) : $experience_query->the_post(); ?>
				<?php get_template_part('template-parts/card', 'experience'); ?>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		<?php endif; ?>
	</div>
</main>

<?php get_footer(); ?>