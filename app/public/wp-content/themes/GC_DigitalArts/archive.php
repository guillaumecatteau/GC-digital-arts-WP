<?php

get_header();
?>

<main class="gc-main gc-archive-layout">
	<section class="gc-page-hero">
		<p class="gc-eyebrow">Archive</p>
		<h1><?php the_archive_title(); ?></h1>
		<?php the_archive_description('<div class="gc-content-copy">', '</div>'); ?>
	</section>

	<div class="gc-card-grid">
		<?php if (have_posts()) : ?>
			<?php while (have_posts()) : the_post(); ?>
				<?php if (get_post_type() === 'experience') : ?>
					<?php get_template_part('template-parts/card', 'experience'); ?>
				<?php else : ?>
					<?php get_template_part('template-parts/card', 'work'); ?>
				<?php endif; ?>
			<?php endwhile; ?>
		<?php else : ?>
			<p>Aucun contenu publié dans cette archive.</p>
		<?php endif; ?>
	</div>

	<?php the_posts_pagination(); ?>
</main>

<?php get_footer(); ?>