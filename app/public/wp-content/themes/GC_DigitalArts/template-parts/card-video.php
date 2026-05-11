<?php
$youtube_url = (string) get_post_meta(get_the_ID(), 'youtube_url', true);
$media_year  = (string) get_post_meta(get_the_ID(), 'media_year', true);
$caption     = gc_get_acf_value('gc_media_text_fr', get_the_ID(), '');
if (gc_get_current_lang() === 'en') {
	$caption = gc_get_acf_value('gc_media_text_en', get_the_ID(), $caption);
}

// Extract YouTube video ID from various URL formats.
$video_id = '';
if ($youtube_url) {
	if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $youtube_url, $matches)) {
		$video_id = $matches[1];
	}
}
?>

<?php if ($video_id) : ?>
<figure class="gc-gallery-grid__item gc-gallery-grid__item--video">
	<div class="gc-gallery-grid__video-wrap" style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;">
		<iframe
			src="https://www.youtube-nocookie.com/embed/<?php echo esc_attr($video_id); ?>"
			title="<?php echo esc_attr(get_the_title()); ?>"
			allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
			allowfullscreen
			loading="lazy"
			style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;"
		></iframe>
	</div>
	<?php if ($media_year) : ?>
		<div class="gc-gallery-grid__year"><?php echo esc_html($media_year); ?></div>
	<?php endif; ?>
	<?php if ($caption) : ?>
		<figcaption class="gc-gallery-grid__caption"><?php echo esc_html($caption); ?></figcaption>
	<?php endif; ?>
	<div class="gc-term-list">
		<?php echo wp_kses_post(gc_render_term_links(get_the_ID(), 'gc_category')); ?>
		<?php echo wp_kses_post(gc_render_term_links(get_the_ID(), 'technology')); ?>
	</div>
</figure>
<?php endif; ?>
