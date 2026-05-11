<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php
$background_usage = 'global';

if (is_front_page()) {
	$background_usage = 'home';
} elseif (is_post_type_archive('experience') || is_page_template('page-templates/template-experience.php')) {
	$background_usage = 'experience';
} elseif (is_home() || is_singular('post')) {
	$background_usage = 'blog';
} elseif (is_page_template('page-templates/template-contact.php')) {
	$background_usage = 'contact';
}

$background_url = gc_get_background_attachment_url($background_usage);
$shell_style = $background_url ? '--gc-bg-image:url(' . esc_url($background_url) . ');' : '';
?>
<div class="gc-site-shell"<?php echo $shell_style ? ' style="' . esc_attr($shell_style) . '"' : ''; ?>>
	<header class="gc-site-header">
		<div class="gc-site-header__inner">
			<div class="gc-branding">
				<?php if (has_custom_logo()) : ?>
					<div class="gc-branding__home gc-branding__home--logo">
						<?php the_custom_logo(); ?>
					</div>
				<?php else : ?>
					<a class="gc-branding__home" href="<?php echo esc_url(home_url('/')); ?>" aria-label="Retour à l'accueil">
						<span class="gc-branding__mark">GC</span>
					</a>
				<?php endif; ?>
				<div class="gc-branding__copy">
					<a class="gc-branding__title" href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
					<p class="gc-branding__tagline"><?php echo esc_html(get_bloginfo('description')); ?></p>
				</div>
			</div>

			<button class="gc-menu-toggle" type="button" aria-expanded="false" aria-controls="gc-primary-nav">
				<span>Menu</span>
			</button>

			<nav id="gc-primary-nav" class="gc-primary-nav" aria-label="Menu principal">
				<?php
				wp_nav_menu([
					'theme_location' => 'primary',
					'container' => false,
					'menu_class' => 'gc-menu',
					'fallback_cb' => false,
				]);
				?>
				<div class="gc-lang-switcher" aria-label="Language switcher">
					<?php $lang = gc_get_current_lang(); ?>
					<a class="gc-lang-switcher__link <?php echo $lang === 'fr' ? 'is-active' : ''; ?>" href="<?php echo esc_url(gc_lang_url('fr')); ?>">FR</a>
					<a class="gc-lang-switcher__link <?php echo $lang === 'en' ? 'is-active' : ''; ?>" href="<?php echo esc_url(gc_lang_url('en')); ?>">EN</a>
				</div>
			</nav>
		</div>
	</header>
	<div class="gc-site-content">
