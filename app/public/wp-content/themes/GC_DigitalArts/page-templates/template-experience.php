<?php
/*
Template Name: Experience
*/

get_header();

$experience_query = new WP_Query([
	'post_type'      => 'experience',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'meta_key'       => 'start_date',
	'orderby'        => 'meta_value',
	'order'          => 'ASC',
]);

$projects_query = new WP_Query([
	'post_type' => 'project',
	'post_status' => 'publish',
	'posts_per_page' => 6,
]);
?>

<main class="gc-main gc-main--flow">
	<?php while (have_posts()) : the_post(); ?>
		<section class="gc-page-hero">
			<p class="gc-eyebrow">Expérience</p>
			<h1><?php the_title(); ?></h1>
			<div class="gc-content-copy"><?php the_content(); ?></div>
		</section>
	<?php endwhile; ?>

	<?php if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) : ?>
		<div style="background:#fff3cd;padding:1rem;margin:1rem;font-family:monospace;font-size:.85rem;">
			<strong>DEBUG experience_query</strong><br>
			found: <?php echo $experience_query->found_posts; ?> |
			post_status: publish |
			meta_key: start_date<br>
			<?php
			$all = get_posts(['post_type'=>'experience','post_status'=>'any','numberposts'=>-1,'fields'=>'ids']);
			echo 'Tous les posts experience (any status): ' . implode(', ', $all ?: ['aucun']);
			?>
		</div>
	<?php endif; ?>

	<section class="gc-section">
		<div class="gc-section__heading">
			<p class="gc-eyebrow">Timeline</p>
			<h2>Parcours professionnel et formation</h2>
		</div>
		<div class="gc-timeline">
			<?php if ($experience_query->have_posts()) : ?>
				<?php while ($experience_query->have_posts()) : $experience_query->the_post(); ?>
					<?php get_template_part('template-parts/card', 'experience'); ?>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php endif; ?>
		</div>
	</section>

	<section class="gc-section gc-section--panel">
		<div class="gc-section__heading">
			<p class="gc-eyebrow">Portfolio</p>
			<h2>Projets récents</h2>
		</div>
		<div class="gc-card-grid">
			<?php if ($projects_query->have_posts()) : ?>
				<?php while ($projects_query->have_posts()) : $projects_query->the_post(); ?>
					<?php get_template_part('template-parts/card', 'work'); ?>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php get_footer(); ?>