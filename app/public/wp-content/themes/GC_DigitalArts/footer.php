<?php
$social_links = gc_get_social_links();
$contact_email = gc_get_acf_value('contact_email', 'option');
$contact_phone = gc_get_acf_value('contact_phone', 'option');
$contact_location = gc_get_acf_value('contact_location', 'option');
$footer_text = gc_get_acf_value('footer_text', 'option', get_bloginfo('name'));
?>
	</div>
	<footer class="gc-site-footer" id="footer">
		<div class="gc-site-footer__inner">
			<div>
				<p class="gc-site-footer__title"><?php bloginfo('name'); ?></p>
				<p class="gc-site-footer__text"><?php echo esc_html($footer_text); ?></p>
			</div>

			<div>
				<p class="gc-site-footer__heading">Navigation</p>
				<?php
				wp_nav_menu([
					'theme_location' => 'footer_primary',
					'container' => false,
					'menu_class' => 'gc-footer-menu',
					'fallback_cb' => false,
				]);
				?>
			</div>

			<div>
				<p class="gc-site-footer__heading">Contact</p>
				<ul class="gc-site-footer__contact-list">
					<?php if ($contact_email) : ?>
						<li><a href="mailto:<?php echo esc_attr($contact_email); ?>"><?php echo esc_html($contact_email); ?></a></li>
					<?php endif; ?>
					<?php if ($contact_phone) : ?>
						<li><a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $contact_phone)); ?>"><?php echo esc_html($contact_phone); ?></a></li>
					<?php endif; ?>
					<?php if ($contact_location) : ?>
						<li><?php echo esc_html($contact_location); ?></li>
					<?php endif; ?>
				</ul>
			</div>

			<?php if ($social_links) : ?>
				<div>
					<p class="gc-site-footer__heading">Réseaux</p>
					<ul class="gc-social-links">
						<?php foreach ($social_links as $link) : ?>
							<li>
								<a href="<?php echo esc_url($link['url'] ?? '#'); ?>" target="_blank" rel="noreferrer noopener">
									<?php echo esc_html($link['label'] ?? 'Link'); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		</div>
	</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
