<?php
$company_name = gc_get_acf_value('company_name', get_the_ID(), get_the_title());
$position = gc_get_acf_value('position_label', get_the_ID(), '');
$date_range = gc_get_date_range(gc_get_acf_value('start_date'), gc_get_acf_value('end_date'));
$company_logo = gc_get_acf_value('company_logo', get_the_ID(), null);
$linked_logo_id = gc_get_logo_attachment_for_experience(get_the_ID());
?>

<article <?php post_class('gc-timeline-item'); ?>>
	<div class="gc-timeline-item__marker"></div>
	<div class="gc-timeline-item__content">
		<?php if ($linked_logo_id) : ?>
			<div class="gc-timeline-item__logo">
				<?php echo wp_get_attachment_image($linked_logo_id, 'thumbnail'); ?>
			</div>
		<?php elseif (is_array($company_logo) && ! empty($company_logo['ID'])) : ?>
			<div class="gc-timeline-item__logo">
				<?php echo wp_get_attachment_image($company_logo['ID'], 'thumbnail'); ?>
			</div>
		<?php endif; ?>
		<p class="gc-timeline-item__date"><?php echo esc_html($date_range); ?></p>
		<h2><?php echo esc_html($company_name); ?></h2>
		<?php if ($position) : ?>
			<p class="gc-timeline-item__role"><?php echo esc_html($position); ?></p>
		<?php endif; ?>
		<div class="gc-content-copy">
			<?php the_content(); ?>
		</div>
	</div>
</article>