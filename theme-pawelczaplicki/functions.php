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

		wp_enqueue_style(
			'pawelczaplicki-tailwind',
			get_template_directory_uri() . '/assets/css/tailwind.css',
			array(),
			$theme_version
		);

		wp_enqueue_style(
			'pawelczaplicki-fonts',
			get_template_directory_uri() . '/assets/css/fonts.css',
			array( 'pawelczaplicki-tailwind' ),
			$theme_version
		);

		wp_enqueue_style(
			'pawelczaplicki-main',
			get_template_directory_uri() . '/assets/css/main.css',
			array( 'pawelczaplicki-tailwind', 'pawelczaplicki-fonts' ),
			$theme_version
		);

		wp_enqueue_script(
			'pawelczaplicki-scroll-reveal',
			get_template_directory_uri() . '/assets/js/scroll-reveal.js',
			array(),
			$theme_version,
			true
		);
	}
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
	}
);

