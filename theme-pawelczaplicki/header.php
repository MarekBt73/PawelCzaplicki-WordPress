<?php

declare(strict_types=1);

?><!doctype html>
<html <?php language_attributes(); ?> class="scroll-smooth">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php if ( is_front_page() ) : ?>
	<!-- Sticky header – Protokół 17:00™ (strona główna) -->
	<header id="main-header" class="fixed top-0 w-full z-50 transition-all duration-300 py-3 bg-white border-b border-gray-100">
		<div class="max-w-7xl mx-auto px-6 md:px-12 flex justify-between items-center">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center space-x-3">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo-2.png' ); ?>" alt="<?php esc_attr_e( 'Czaplicki – logo', 'pawelczaplicki' ); ?>" class="h-8 w-auto">
			</a>
			<button
				type="button"
				class="inline-flex items-center justify-center rounded-full border border-gray-200 bg-white p-2 text-gray-700 md:hidden"
				aria-label="<?php esc_attr_e( 'Przełącz menu', 'pawelczaplicki' ); ?>"
				aria-expanded="false"
				id="p17-mobile-toggle"
			>
				<span class="sr-only"><?php esc_html_e( 'Menu', 'pawelczaplicki' ); ?></span>
				<span class="p17-burger-icon block w-5">
					<span class="block h-0.5 w-full bg-gray-800 rounded-sm mb-1"></span>
					<span class="block h-0.5 w-full bg-gray-800 rounded-sm mb-1"></span>
					<span class="block h-0.5 w-full bg-gray-800 rounded-sm"></span>
				</span>
			</button>
			<nav class="hidden md:flex space-x-10 items-center p17-nav-desktop" aria-label="<?php esc_attr_e( 'Główne menu', 'pawelczaplicki' ); ?>">
				<?php
				if ( has_nav_menu( 'primary' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'container'      => false,
							'menu_class'     => 'flex space-x-10 items-center list-none m-0 p-0',
							'fallback_cb'    => false,
							'link_before'    => '',
							'link_after'     => '',
							'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
						)
					);
				} else {
					?>
					<a href="#problem" class="text-sm font-medium text-gray-500 hover:text-brand-red transition-colors">Problem</a>
					<a href="#rozwiazanie" class="text-sm font-medium text-gray-500 hover:text-brand-red transition-colors">Metodologia</a>
					<a href="#oferta" class="text-sm font-medium text-gray-500 hover:text-brand-red transition-colors">Oferta</a>
					<a href="#faq" class="text-sm font-medium text-gray-500 hover:text-brand-red transition-colors">FAQ</a>
				<?php } ?>
			</nav>
			<nav class="p17-nav-mobile hidden absolute left-0 right-0 top-full mt-2 mx-6 bg-white border border-gray-200 rounded-2xl shadow-lg px-5 py-4 md:hidden" aria-label="<?php esc_attr_e( 'Główne menu mobilne', 'pawelczaplicki' ); ?>">
				<?php
				if ( has_nav_menu( 'primary' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'container'      => false,
							'menu_class'     => 'flex flex-col space-y-3 list-none m-0 p-0',
							'fallback_cb'    => false,
							'link_before'    => '',
							'link_after'     => '',
							'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
						)
					);
				} else {
					?>
					<a href="#problem" class="block text-sm font-medium text-gray-700 hover:text-brand-red transition-colors">Problem</a>
					<a href="#rozwiazanie" class="block text-sm font-medium text-gray-700 hover:text-brand-red transition-colors">Metodologia</a>
					<a href="#oferta" class="block text-sm font-medium text-gray-700 hover:text-brand-red transition-colors">Oferta</a>
					<a href="#faq" class="block text-sm font-medium text-gray-700 hover:text-brand-red transition-colors">FAQ</a>
					<?php
				}
				?>
			</nav>
			<div class="hidden md:block">
				<a href="<?php echo esc_url( home_url( '/protokol-17-00/' ) ); ?>" class="inline-flex items-center text-sm font-bold text-brand-red hover:text-brand-dark transition-colors tracking-wide uppercase">
					Poznaj Protokół 17:00™ <i data-lucide="arrow-right" class="ml-2 w-4 h-4"></i>
				</a>
			</div>
		</div>
	</header>
<?php else : ?>
	<!-- Header – pozostałe strony (stary motyw) -->
		<header class="pc-header pc-container pl-6">
		<div class="pc-logo">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo-2.png' ); ?>" alt="<?php esc_attr_e( 'Czaplicki – logo', 'pawelczaplicki' ); ?>">
			</a>
		</div>
		<button type="button" class="pc-nav-toggle" aria-label="<?php esc_attr_e( 'Przełącz menu', 'pawelczaplicki' ); ?>" aria-expanded="false">
			<span></span>
			<span></span>
		</button>
		<nav class="pc-nav" aria-label="<?php esc_attr_e( 'Główne menu', 'pawelczaplicki' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'pc-nav-list',
					'fallback_cb'    => false,
				)
			);
			?>
			<a class="pc-btn pc-btn--primary pc-header-cta" href="<?php echo esc_url( home_url( '/' ) ); ?>#oferta">
				Poznaj Protokół 17:00™
			</a>
		</nav>
	</header>
<?php endif; ?>

<main class="<?php echo is_front_page() ? '' : 'pc-main'; ?>">
