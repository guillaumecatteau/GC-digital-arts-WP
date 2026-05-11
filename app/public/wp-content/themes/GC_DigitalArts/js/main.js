const menuToggle = document.querySelector('.gc-menu-toggle');
const primaryNav = document.querySelector('.gc-primary-nav');

if (menuToggle && primaryNav) {
	menuToggle.addEventListener('click', () => {
		const isOpen = primaryNav.classList.toggle('is-open');
		menuToggle.setAttribute('aria-expanded', String(isOpen));
	});
}

const sectionLinks = [...document.querySelectorAll('[data-section-dot]')];

if (sectionLinks.length > 0 && 'IntersectionObserver' in window) {
	const sections = sectionLinks
		.map((link) => document.querySelector(link.getAttribute('href')))
		.filter(Boolean);

	const observer = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting) {
					return;
				}

				sectionLinks.forEach((link) => {
					link.classList.toggle('is-active', link.getAttribute('href') === `#${entry.target.id}`);
				});
			});
		},
		{
			rootMargin: '-35% 0px -45% 0px',
			threshold: 0.1,
		}
	);

	sections.forEach((section) => observer.observe(section));
}
