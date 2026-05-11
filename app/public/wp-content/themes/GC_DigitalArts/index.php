<?php

get_header();
?>

<main class="gc-main gc-main--flow">
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<article <?php post_class('gc-article'); ?>>
				<header class="gc-article__header">
					<p class="gc-eyebrow"><?php echo esc_html(get_post_type_object(get_post_type())->labels->singular_name ?? 'Contenu'); ?></p>
					<h1><?php the_title(); ?></h1>
				</header>
				<div class="gc-content-copy">
					<?php the_content(); ?>
				</div>
			</article>
		<?php endwhile; ?>
	<?php else : ?>
		<section class="gc-empty-state">
			<h1>Aucun contenu trouvé</h1>
			<p>Le thème est prêt, mais aucun contenu correspondant n'a encore été publié.</p>
		</section>
	<?php endif; ?>
</main>
<?php
get_footer();
