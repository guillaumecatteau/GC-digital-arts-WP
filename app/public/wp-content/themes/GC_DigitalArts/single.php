<?php

get_header();

$post_id = get_queried_object_id();
$related_media_ids = gc_get_related_attachments_for_post($post_id);

$translated_text = gc_get_translated_text(
	gc_get_acf_value('text_fr', $post_id),
	gc_get_acf_value('text_en', $post_id),
	''
);
?>

<main class="gc-main gc-main--flow">
	<?php while (have_posts()) : the_post(); ?>
		<article <?php post_class('gc-article'); ?>>
			<header class="gc-page-hero">
				<p class="gc-eyebrow"><?php echo esc_html(gc_t('Blog', 'Blog')); ?></p>
				<h1><?php the_title(); ?></h1>
				<p><?php echo esc_html(get_the_date()); ?></p>
				<div class="gc-term-list"><?php echo wp_kses_post(gc_render_term_links(get_the_ID(), 'gc_category')); ?></div>
			</header>

			<div class="gc-article__media">
				<?php echo wp_kses_post(gc_get_media_html(get_the_ID(), 'main_visual', 'gc-hero', 'gc-media gc-media--hero')); ?>
			</div>

			<div class="gc-grid gc-grid--two">
				<section class="gc-content-copy">
					<?php the_content(); ?>
					<?php if ($translated_text) : ?>
						<h2><?php echo esc_html(gc_t('Contenu', 'Content')); ?></h2>
						<?php echo wp_kses_post(wpautop($translated_text)); ?>
					<?php endif; ?>
				</section>

				<aside class="gc-sidebar-card">
					<h2><?php echo esc_html(gc_t('Métadonnées', 'Metadata')); ?></h2>
					<div class="gc-sidebar-card__stack">
						<div>
							<h3><?php echo esc_html(gc_t('Technologies', 'Technologies')); ?></h3>
							<div class="gc-term-list"><?php echo wp_kses_post(gc_render_term_links(get_the_ID(), 'technology')); ?></div>
						</div>
						<?php if ($related_projects = gc_get_acf_value('related_projects', get_the_ID(), [])) : ?>
							<div>
								<h3><?php echo esc_html(gc_t('Projet lié', 'Related project')); ?></h3>
								<ul class="gc-linked-list">
									<?php foreach ($related_projects as $project) : ?>
										<li><a href="<?php echo esc_url(get_permalink($project)); ?>"><?php echo esc_html(get_the_title($project)); ?></a></li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
					</div>
				</aside>
			</div>

			<?php if ($related_media_ids) : ?>
				<section class="gc-section gc-section--panel">
					<div class="gc-section__heading">
						<p class="gc-eyebrow"><?php echo esc_html(gc_t('Galerie', 'Gallery')); ?></p>
						<h2><?php echo esc_html(gc_t('Médias liés', 'Related media')); ?></h2>
					</div>
					<div class="gc-gallery-grid">
						<?php foreach ($related_media_ids as $attachment_id) : ?>
							<figure class="gc-gallery-grid__item">
								<?php echo wp_kses_post(gc_get_attachment_media_html($attachment_id, 'large', 'gc-gallery-grid__image')); ?>
								<?php if ($media_year = gc_get_attachment_year($attachment_id)) : ?>
									<div class="gc-gallery-grid__year"><?php echo esc_html((string) $media_year); ?></div>
								<?php endif; ?>
								<?php if ($media_text = gc_get_attachment_translated_text($attachment_id)) : ?>
									<figcaption class="gc-gallery-grid__caption"><?php echo esc_html($media_text); ?></figcaption>
								<?php endif; ?>
								<div class="gc-term-list">
									<?php echo wp_kses_post(gc_render_term_links($attachment_id, 'gc_category')); ?>
									<?php echo wp_kses_post(gc_render_term_links($attachment_id, 'technology')); ?>
								</div>
							</figure>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>
		</article>
	<?php endwhile; ?>
</main>

<?php get_footer(); ?>