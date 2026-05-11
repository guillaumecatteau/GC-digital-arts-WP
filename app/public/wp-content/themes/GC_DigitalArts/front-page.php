<?php

get_header();

$front_page_id = get_queried_object_id();
$hero_title = gc_get_acf_value('home_hero_title', $front_page_id, get_bloginfo('name'));
$hero_intro = gc_get_acf_value('home_hero_intro', $front_page_id, get_bloginfo('description'));
$presentation_title = gc_get_acf_value('presentation_title', $front_page_id, 'Présentation');
$presentation_text = gc_get_acf_value('presentation_text', $front_page_id, get_post_field('post_content', $front_page_id));
$portfolio_title = gc_get_acf_value('portfolio_title', $front_page_id, 'Portfolio');
$blog_title = gc_get_acf_value('blog_title', $front_page_id, 'Blog');
$contact_anchor = gc_get_acf_value('contact_anchor_label', $front_page_id, 'Contact');

$expertise_pages = get_posts([
	'post_type' => 'page',
	'post_status' => 'publish',
	'posts_per_page' => -1,
	'orderby' => 'menu_order title',
	'order' => 'ASC',
	'meta_key' => '_wp_page_template',
	'meta_value' => 'page-templates/template-expertise.php',
]);

$featured_projects = new WP_Query([
	'post_type' => 'project',
	'post_status' => 'publish',
	'posts_per_page' => 6,
]);

$latest_posts = new WP_Query([
	'post_type' => 'post',
	'post_status' => 'publish',
	'posts_per_page' => 3,
]);
?>

<main class="gc-front-page">
	<aside class="gc-section-dots" aria-label="Navigation rapide">
		<a href="#accueil" class="gc-section-dots__link is-active" data-section-dot>Accueil</a>
		<a href="#presentation" class="gc-section-dots__link" data-section-dot>Présentation</a>
		<a href="#expertise" class="gc-section-dots__link" data-section-dot>Expertise</a>
		<a href="#portfolio" class="gc-section-dots__link" data-section-dot>Portfolio</a>
		<a href="#blog" class="gc-section-dots__link" data-section-dot>Blog</a>
		<a href="#contact" class="gc-section-dots__link" data-section-dot><?php echo esc_html($contact_anchor); ?></a>
	</aside>

	<?php if ($social_links = gc_get_social_links()) : ?>
		<ul class="gc-floating-socials" aria-label="Réseaux sociaux">
			<?php foreach ($social_links as $link) : ?>
				<li>
					<a href="<?php echo esc_url($link['url'] ?? '#'); ?>" target="_blank" rel="noreferrer noopener">
						<?php echo esc_html($link['label'] ?? 'Link'); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<section id="accueil" class="gc-section gc-hero-section">
		<div class="gc-hero-section__content">
			<p class="gc-eyebrow">Portfolio artistique WordPress</p>
			<h1><?php echo esc_html($hero_title); ?></h1>
			<p class="gc-hero-section__lead"><?php echo esc_html($hero_intro); ?></p>
			<div class="gc-hero-section__actions">
				<a class="gc-button" href="#portfolio">Voir les projets</a>
				<a class="gc-button gc-button--ghost" href="#contact"><?php echo esc_html($contact_anchor); ?></a>
			</div>
		</div>
	</section>

	<section id="presentation" class="gc-section gc-section--panel">
		<div class="gc-section__heading">
			<p class="gc-eyebrow">Présentation</p>
			<h2><?php echo esc_html($presentation_title); ?></h2>
		</div>
		<div class="gc-content-copy">
			<?php echo wp_kses_post(wpautop($presentation_text)); ?>
		</div>
	</section>

	<section id="expertise" class="gc-section">
		<div class="gc-section__heading">
			<p class="gc-eyebrow">Expertise</p>
			<h2>Domaines d'intervention</h2>
		</div>
		<div class="gc-link-grid">
			<?php if ($expertise_pages) : ?>
				<?php foreach ($expertise_pages as $expertise_page) : ?>
					<a class="gc-link-card" href="<?php echo esc_url(get_permalink($expertise_page)); ?>">
						<span class="gc-link-card__title"><?php echo esc_html(get_the_title($expertise_page)); ?></span>
						<span class="gc-link-card__meta">Voir les projets associés</span>
					</a>
				<?php endforeach; ?>
			<?php else : ?>
				<?php foreach (get_terms(['taxonomy' => 'gc_category', 'hide_empty' => false]) as $term) : ?>
					<a class="gc-link-card" href="<?php echo esc_url(get_term_link($term)); ?>">
						<span class="gc-link-card__title"><?php echo esc_html($term->name); ?></span>
						<span class="gc-link-card__meta">Taxonomie prête pour la page expertise</span>
					</a>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</section>

	<section id="portfolio" class="gc-section gc-section--panel">
		<div class="gc-section__heading gc-section__heading--split">
			<div>
				<p class="gc-eyebrow">Portfolio</p>
				<h2><?php echo esc_html($portfolio_title); ?></h2>
			</div>
			<a class="gc-text-link" href="<?php echo esc_url(get_post_type_archive_link('project')); ?>">Tous les projets</a>
		</div>
		<div class="gc-card-grid">
			<?php if ($featured_projects->have_posts()) : ?>
				<?php while ($featured_projects->have_posts()) : $featured_projects->the_post(); ?>
					<?php get_template_part('template-parts/card', 'work'); ?>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php endif; ?>
		</div>
	</section>

	<section id="blog" class="gc-section">
		<div class="gc-section__heading gc-section__heading--split">
			<div>
				<p class="gc-eyebrow">Blog</p>
				<h2><?php echo esc_html($blog_title); ?></h2>
			</div>
			<a class="gc-text-link" href="<?php echo esc_url(get_permalink(get_option('page_for_posts')) ?: home_url('/blog/')); ?>">Tous les articles</a>
		</div>
		<div class="gc-card-grid gc-card-grid--posts">
			<?php if ($latest_posts->have_posts()) : ?>
				<?php while ($latest_posts->have_posts()) : $latest_posts->the_post(); ?>
					<?php get_template_part('template-parts/card', 'work'); ?>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php endif; ?>
		</div>
	</section>

	<section id="contact" class="gc-section gc-section--panel gc-contact-cta">
		<div class="gc-section__heading">
			<p class="gc-eyebrow">Contact</p>
			<h2>Parlons de votre prochain projet</h2>
		</div>
		<p><?php echo esc_html(gc_get_acf_value('contact_intro', 'option', 'Ajoutez vos informations de contact et le shortcode du formulaire via ACF pour finaliser cette section.')); ?></p>
		<a class="gc-button" href="<?php echo esc_url(home_url('/contact/')); ?>">Accéder à la page contact</a>
	</section>
</main>

<?php get_footer(); ?>