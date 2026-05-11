<?php

get_header();
?>

<main class="gc-main gc-main--flow">
	<?php while (have_posts()) : the_post(); ?>
		<article <?php post_class('gc-article'); ?>>
			<header class="gc-page-hero">
				<p class="gc-eyebrow">Page</p>
				<h1><?php the_title(); ?></h1>
			</header>
			<div class="gc-content-copy">
				<?php the_content(); ?>
			</div>
		</article>
	<?php endwhile; ?>
</main>

<?php get_footer(); ?>