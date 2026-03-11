<?php

declare(strict_types=1);

/**
 * Podstawowe funkcje motywu Pawel Czaplicki.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	static function (): void {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

		register_nav_menus(
			array(
				'primary' => __( 'Główne menu', 'pawelczaplicki' ),
				'footer'  => __( 'Menu w stopce', 'pawelczaplicki' ),
			)
		);
	}
);

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		$theme_version = wp_get_theme()->get( 'Version' );
		$is_front    = is_front_page();

		if ( $is_front ) {
			// Strona główna: Tailwind CDN + konfiguracja marki, Lucide, własne style
			wp_enqueue_style(
				'pawelczaplicki-google-fonts',
				'https://fonts.googleapis.com/css2?family=Mona+Sans:ital,wdth,wght@0,75..125,200..900;1,75..125,200..900&display=swap',
				array(),
				null
			);
			wp_enqueue_script(
				'pawelczaplicki-tailwind-cdn',
				'https://cdn.tailwindcss.com',
				array(),
				null,
				false
			);
			wp_add_inline_script(
				'pawelczaplicki-tailwind-cdn',
				"tailwind.config = { theme: { extend: { fontFamily: { sans: ['\"Mona Sans\"', 'sans-serif'] }, colors: { brand: { red: '#E7411D', dark: '#111111', gray: '#f4f4f4', light: '#fafafa' } } } } };",
				'after'
			);
			wp_enqueue_script(
				'pawelczaplicki-lucide',
				'https://unpkg.com/lucide@latest/dist/umd/lucide.js',
				array(),
				null,
				false
			);
			wp_enqueue_style(
				'pawelczaplicki-front-page',
				get_template_directory_uri() . '/assets/css/front-page.css',
				array( 'pawelczaplicki-google-fonts' ),
				$theme_version
			);
			wp_enqueue_script(
				'pawelczaplicki-front-page',
				get_template_directory_uri() . '/assets/js/front-page.js',
				array( 'pawelczaplicki-lucide' ),
				$theme_version,
				true
			);
		} else {
			wp_enqueue_style(
				'pawelczaplicki-tailwind',
				get_template_directory_uri() . '/assets/css/tailwind.css',
				array(),
				$theme_version
			);
		}

		wp_enqueue_style(
			'pawelczaplicki-fonts',
			get_template_directory_uri() . '/assets/css/fonts.css',
			array(),
			$theme_version
		);

		if ( ! $is_front ) {
			wp_enqueue_style(
				'pawelczaplicki-main',
				get_template_directory_uri() . '/assets/css/main.css',
				array( 'pawelczaplicki-tailwind', 'pawelczaplicki-fonts' ),
				$theme_version
			);
		}

		if ( ! $is_front ) {
			wp_enqueue_script(
				'pawelczaplicki-scroll-reveal',
				get_template_directory_uri() . '/assets/js/scroll-reveal.js',
				array(),
				$theme_version,
				true
			);
			wp_enqueue_script(
				'pawelczaplicki-nav-toggle',
				get_template_directory_uri() . '/assets/js/nav-toggle.js',
				array(),
				$theme_version,
				true
			);
		}
	}
);

add_filter(
	'body_class',
	static function ( array $classes ): array {
		if ( is_front_page() ) {
			$classes[] = 'scroll-smooth';
			$classes[] = 'text-brand-dark';
			$classes[] = 'bg-white';
			$classes[] = 'relative';
		}
		return $classes;
	}
);

add_filter(
	'nav_menu_link_attributes',
	static function ( array $atts, $item, $args ): array {
		if ( isset( $args->theme_location ) && $args->theme_location === 'primary' && is_front_page() ) {
			$atts['class'] = 'text-sm font-medium text-gray-500 hover:text-brand-red transition-colors';
		}
		return $atts;
	},
	10,
	3
);

add_action(
	'widgets_init',
	static function (): void {
		register_sidebar(
			array(
				'name'          => __( 'Kontakt – widgety', 'pawelczaplicki' ),
				'id'            => 'pawelczaplicki-contact-widgets',
				'description'   => __( 'Widgety wyświetlane w dedykowanym szablonie strony Kontakt.', 'pawelczaplicki' ),
				'before_widget' => '<section class="pc-widget %2$s" id="%1$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="pc-widget-title">',
				'after_title'   => '</h3>',
			)
		);
		register_sidebar(
			array(
				'name'          => __( 'Hero – foto', 'pawelczaplicki' ),
				'id'            => 'hero-foto',
				'description'   => __( 'Grafika pod sekcją Hero na stronie głównej (np. zdjęcie autora).', 'pawelczaplicki' ),
				'before_widget' => '<div class="pc-hero-foto-widget %2$s" id="%1$s">',
				'after_widget'  => '</div>',
				'before_title'  => '',
				'after_title'   => '',
			)
		);
	}
);

