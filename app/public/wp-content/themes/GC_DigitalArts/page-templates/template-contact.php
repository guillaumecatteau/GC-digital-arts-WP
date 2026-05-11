<?php
/*
Template Name: Contact
*/

get_header();

$form_shortcode = gc_get_acf_value('contact_form_shortcode', get_queried_object_id(), gc_get_acf_value('contact_form_shortcode', 'option'));
?>

<main class="gc-main gc-main--flow">
	<?php while (have_posts()) : the_post(); ?>
		<section class="gc-page-hero">
			<p class="gc-eyebrow">Contact</p>
			<h1><?php the_title(); ?></h1>
			<div class="gc-content-copy"><?php the_content(); ?></div>
		</section>
	<?php endwhile; ?>

	<section class="gc-grid gc-grid--two">
		<div class="gc-sidebar-card">
			<h2>Coordonnées</h2>
			<div class="gc-sidebar-card__stack">
				<?php if ($email = gc_get_acf_value('contact_email', 'option')) : ?>
					<p><strong>Email</strong><br><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></p>
				<?php endif; ?>
				<?php if ($phone = gc_get_acf_value('contact_phone', 'option')) : ?>
					<p><strong>Téléphone</strong><br><a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a></p>
				<?php endif; ?>
				<?php if ($location = gc_get_acf_value('contact_location', 'option')) : ?>
					<p><strong>Localisation</strong><br><?php echo esc_html($location); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<div class="gc-form-panel">
			<h2>Formulaire</h2>
			<?php if ($form_shortcode) : ?>
				<?php echo do_shortcode($form_shortcode); ?>
			<?php else : ?>
				<p>Ajoutez le shortcode du formulaire via ACF dans le champ contact_form_shortcode.</p>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php get_footer(); ?>