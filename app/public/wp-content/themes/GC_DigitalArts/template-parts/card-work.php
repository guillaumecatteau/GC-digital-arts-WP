<?php
$post_type = get_post_type();
$is_project = $post_type === 'project';
$date_label = $is_project ? gc_get_date_range(gc_get_project_date_value('start_date'), gc_get_project_date_value('end_date')) : get_the_date();
$card_label = $is_project ? gc_t('Projet', 'Project') : gc_t('Article', 'Post');
?>

<article <?php post_class('gc-card'); ?>>
	<a class="gc-card__media" href="<?php the_permalink(); ?>">
		<?php echo wp_kses_post(gc_get_media_html(get_the_ID(), 'main_visual')); ?>
	</a>
	<div class="gc-card__body">
		<p class="gc-card__eyebrow"><?php echo esc_html($card_label); ?></p>
		<h3 class="gc-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php if ($date_label) : ?>
			<p class="gc-card__meta"><?php echo esc_html($date_label); ?></p>
		<?php endif; ?>
		<div class="gc-term-list">
			<?php echo wp_kses_post(gc_render_term_links(get_the_ID(), 'gc_category')); ?>
		</div>
	</div>
</article>