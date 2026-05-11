<?php

get_header();

$project_id = get_queried_object_id();

$start_date = gc_get_project_date_value('start_date', $project_id);
$end_date = gc_get_project_date_value('end_date', $project_id);
$translated_text = gc_get_translated_text(
	gc_get_acf_value('text_fr', $project_id),
	gc_get_acf_value('text_en', $project_id),
	''
);
$related_posts = gc_get_related_posts_for_project($project_id);
$related_media_ids = gc_get_related_attachments_for_project($project_id);
$gallery_attachment_ids = gc_get_project_gallery_attachments($project_id);


// Images from the ACF gallery always show. For related attachments, only 'media' type.
// Preserve insertion order: ACF gallery first, then any related media not already in gallery.
$all_attachment_ids = $gallery_attachment_ids;
$seen_ids = array_flip($gallery_attachment_ids);
foreach ($related_media_ids as $rid) {
	if (gc_get_attachment_media_type((int) $rid) === 'media' && ! isset($seen_ids[$rid])) {
		$all_attachment_ids[] = $rid;
		$seen_ids[$rid] = true;
	}
}
$related_video_ids = gc_get_related_videos_for_project($project_id);
$cover_youtube_url = (string) get_post_meta($project_id, 'cover_youtube_url', true);
$cover_image_id    = (int) get_post_meta($project_id, 'cover_image_id', true);
?>

<main class="gc-main gc-main--flow">
	<?php while (have_posts()) : the_post(); ?>
		<article <?php post_class('gc-article'); ?>>
			<header class="gc-page-hero">
				<p class="gc-eyebrow"><?php echo esc_html(gc_t('Projet', 'Project')); ?></p>
				<h1><?php the_title(); ?></h1>
				<?php if ($date_range = gc_get_date_range($start_date, $end_date)) : ?>
					<p><?php echo esc_html($date_range); ?></p>
				<?php endif; ?>
				<div class="gc-term-list">
					<?php echo wp_kses_post(gc_render_term_links(get_the_ID(), 'gc_category')); ?>
					<?php echo wp_kses_post(gc_render_term_links(get_the_ID(), 'position')); ?>
				</div>
			</header>

			<div class="gc-article__media">
				<?php
				$cover_video_id = '';
				if ($cover_youtube_url && preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $cover_youtube_url, $m)) {
					$cover_video_id = $m[1];
				}
				if ($cover_video_id) : ?>
					<div class="gc-media gc-media--hero" style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;">
						<iframe
							src="https://www.youtube-nocookie.com/embed/<?php echo esc_attr($cover_video_id); ?>"
							title="<?php echo esc_attr(get_the_title()); ?>"
							allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
							allowfullscreen
							loading="lazy"
							style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;"
						></iframe>
					</div>
				<?php elseif ($cover_image_id) : ?>
					<?php echo wp_get_attachment_image($cover_image_id, 'gc-hero', false, ['class' => 'gc-media gc-media--hero']); ?>
				<?php elseif (has_post_thumbnail()) : ?>
					<?php the_post_thumbnail('gc-hero', ['class' => 'gc-media gc-media--hero']); ?>
				<?php endif; ?>
			</div>

			<div class="gc-grid gc-grid--two">
				<section class="gc-content-copy">
					<?php the_content(); ?>
					<?php if ($translated_text) : ?>
						<h2><?php echo esc_html(gc_t('Description', 'Description')); ?></h2>
						<?php echo wp_kses_post(wpautop($translated_text)); ?>
					<?php endif; ?>
				</section>

				<aside class="gc-sidebar-card">
					<h2><?php echo esc_html(gc_t('Fiche projet', 'Project details')); ?></h2>
					<div class="gc-sidebar-card__stack">
						<?php if ($related_experience_id = gc_get_related_experience_for_project(get_the_ID())) : ?>
							<div>
								<h3><?php echo esc_html(gc_t('Experience liee', 'Related experience')); ?></h3>
								<p><a href="<?php echo esc_url(get_permalink($related_experience_id)); ?>"><?php echo esc_html(get_the_title($related_experience_id)); ?></a></p>
							</div>
						<?php endif; ?>
						<div>
							<h3><?php echo esc_html(gc_t('Technologies', 'Technologies')); ?></h3>
							<div class="gc-term-list"><?php echo wp_kses_post(gc_render_term_links(get_the_ID(), 'technology')); ?></div>
						</div>
					</div>
				</aside>
			</div>

<?php if ($all_attachment_ids || $related_video_ids) : ?>
			<section class="gc-section gc-section--panel">
				<div class="gc-section__heading">
					<p class="gc-eyebrow"><?php echo esc_html(gc_t('Galerie', 'Gallery')); ?></p>
					<h2><?php echo esc_html(gc_t('Médias du projet', 'Project media')); ?></h2>
				</div>
				<div class="gc-gallery-grid">
					<?php foreach ($all_attachment_ids as $attachment_id) : ?>
						<figure class="gc-gallery-grid__item">
							<?php echo wp_kses_post(gc_get_attachment_media_html($attachment_id, 'large', 'gc-gallery-grid__image')); ?>
							<?php if ($media_text = gc_get_attachment_translated_text($attachment_id)) : ?>
								<figcaption class="gc-gallery-grid__caption"><?php echo esc_html($media_text); ?></figcaption>
							<?php endif; ?>
							<div class="gc-term-list">
								<?php echo wp_kses_post(gc_render_term_links($attachment_id, 'gc_category')); ?>
								<?php echo wp_kses_post(gc_render_term_links($attachment_id, 'technology')); ?>
							</div>
						</figure>
					<?php endforeach; ?>
					<?php foreach ($related_video_ids as $video_id) : ?>
						<?php $post = get_post($video_id); setup_postdata($post); ?>
						<?php get_template_part('template-parts/card', 'video'); ?>
						<?php wp_reset_postdata(); ?>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ($related_posts) : ?>
				<section class="gc-section gc-section--panel">
					<div class="gc-section__heading">
						<p class="gc-eyebrow"><?php echo esc_html(gc_t('Blog lié', 'Related blog')); ?></p>
						<h2><?php echo esc_html(gc_t('Articles associés', 'Related posts')); ?></h2>
					</div>
					<div class="gc-card-grid gc-card-grid--posts">
						<?php foreach ($related_posts as $related_post_id) : ?>
							<?php $post = get_post($related_post_id); setup_postdata($post); ?>
							<?php get_template_part('template-parts/card', 'work'); ?>
						<?php endforeach; ?>
						<?php wp_reset_postdata(); ?>
					</div>
				</section>
			<?php endif; ?>
		</article>
	<?php endwhile; ?>
</main>

<?php get_footer(); ?>