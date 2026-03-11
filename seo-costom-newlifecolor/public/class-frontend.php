<?php
/**
 * Klasa frontendowa.
 *
 * Odpowiada za output meta tagów i Schema.org do <head>.
 *
 * @package SeoCustomNLC
 * @since   1.0.0
 */

declare(strict_types=1);

namespace SeoCustomNLC;

// Zabezpieczenie przed bezpośrednim dostępem.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Klasa Frontend.
 *
 * Renderuje meta tagi i JSON-LD na frontendzie.
 *
 * @since 1.0.0
 */
class Frontend {

	/**
	 * Instancja Meta Tags.
	 *
	 * @var Meta_Tags
	 */
	private Meta_Tags $meta_tags;

	/**
	 * Instancja Schema Generator.
	 *
	 * @var Schema_Generator
	 */
	private Schema_Generator $schema_generator;

	/**
	 * Konstruktor.
	 *
	 * @param Meta_Tags        $meta_tags        Instancja Meta Tags.
	 * @param Schema_Generator $schema_generator Instancja Schema Generator.
	 */
	public function __construct( Meta_Tags $meta_tags, Schema_Generator $schema_generator ) {
		$this->meta_tags        = $meta_tags;
		$this->schema_generator = $schema_generator;
	}

	/**
	 * Wyprowadza meta tagi do <head>.
	 *
	 * Wywołane przez hook wp_head z priorytetem 1.
	 *
	 * @return void
	 */
	public function output_meta_tags(): void {
		// Nie renderuj w admin lub dla feedów.
		if ( is_admin() || is_feed() ) {
			return;
		}

		echo "\n<!-- SEO Custom NewLifeColor - Meta Tags -->\n";

		// Meta description.
		$description = $this->meta_tags->get_description();
		if ( ! empty( $description ) ) {
			printf(
				'<meta name="description" content="%s" />' . "\n",
				esc_attr( $description )
			);
		}

		// Canonical.
		$canonical = $this->meta_tags->get_canonical();
		if ( ! empty( $canonical ) ) {
			printf(
				'<link rel="canonical" href="%s" />' . "\n",
				esc_url( $canonical )
			);
		}

		// Robots.
		$robots = $this->meta_tags->get_robots();
		if ( ! empty( $robots ) ) {
			printf(
				'<meta name="robots" content="%s" />' . "\n",
				esc_attr( $robots )
			);
		}

		// Open Graph.
		$this->output_og_tags();

		// Twitter Card.
		$this->output_twitter_tags();

		echo "<!-- / SEO Custom NewLifeColor -->\n\n";
	}

	/**
	 * Wyprowadza tagi Open Graph.
	 *
	 * @return void
	 */
	private function output_og_tags(): void {
		$og_tags = $this->meta_tags->get_og_tags();

		if ( empty( $og_tags ) ) {
			return;
		}

		echo "\n<!-- Open Graph -->\n";

		foreach ( $og_tags as $property => $content ) {
			// Tablica (np. article:tag).
			if ( is_array( $content ) ) {
				foreach ( $content as $value ) {
					printf(
						'<meta property="%s" content="%s" />' . "\n",
						esc_attr( $property ),
						esc_attr( $value )
					);
				}
			} else {
				printf(
					'<meta property="%s" content="%s" />' . "\n",
					esc_attr( $property ),
					esc_attr( $content )
				);
			}
		}
	}

	/**
	 * Wyprowadza tagi Twitter Card.
	 *
	 * @return void
	 */
	private function output_twitter_tags(): void {
		$twitter_tags = $this->meta_tags->get_twitter_tags();

		if ( empty( $twitter_tags ) ) {
			return;
		}

		echo "\n<!-- Twitter Card -->\n";

		foreach ( $twitter_tags as $name => $content ) {
			printf(
				'<meta name="%s" content="%s" />' . "\n",
				esc_attr( $name ),
				esc_attr( $content )
			);
		}
	}

	/**
	 * Wyprowadza Schema.org JSON-LD.
	 *
	 * Wywołane przez hook wp_head z priorytetem 2.
	 *
	 * @return void
	 */
	public function output_schema(): void {
		// Nie renderuj w admin lub dla feedów.
		if ( is_admin() || is_feed() ) {
			return;
		}

		$post_id = $this->get_current_post_id();

		// Sprawdź czy generować schema.
		if ( $post_id > 0 && ! $this->schema_generator->should_generate_schema( $post_id ) ) {
			return;
		}

		$json_ld = $this->schema_generator->render_json_ld( $post_id );

		if ( ! empty( $json_ld ) ) {
			echo "\n<!-- SEO Custom NewLifeColor - Schema.org JSON-LD -->\n";
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-LD musi być niezmieniony.
			echo $json_ld;
			echo "\n<!-- / Schema.org -->\n\n";
		}
	}

	/**
	 * Filtruje tytuł dokumentu (WordPress 4.4+).
	 *
	 * @param string $title Domyślny tytuł.
	 * @return string Przefiltrowany tytuł.
	 */
	public function filter_document_title( string $title ): string {
		// Nie zmieniaj w admin.
		if ( is_admin() ) {
			return $title;
		}

		$custom_title = $this->meta_tags->get_title();

		if ( ! empty( $custom_title ) ) {
			return $custom_title;
		}

		return $title;
	}

	/**
	 * Filtruje tytuł strony (starszy hook).
	 *
	 * @param string $title Domyślny tytuł.
	 * @param string $sep   Separator.
	 * @return string Przefiltrowany tytuł.
	 */
	public function filter_wp_title( string $title, string $sep = '' ): string {
		// Nie zmieniaj w admin.
		if ( is_admin() ) {
			return $title;
		}

		$custom_title = $this->meta_tags->get_title();

		if ( ! empty( $custom_title ) ) {
			return $custom_title;
		}

		return $title;
	}

	/**
	 * Pobiera aktualny post ID.
	 *
	 * @return int ID posta lub 0.
	 */
	private function get_current_post_id(): int {
		if ( is_singular() ) {
			return get_the_ID() ?: 0;
		}

		if ( is_front_page() && ! is_home() ) {
			return (int) get_option( 'page_on_front' );
		}

		if ( is_home() ) {
			return (int) get_option( 'page_for_posts' );
		}

		return 0;
	}

	/**
	 * Usuwa domyślne WordPress SEO tagi które zastępujemy.
	 *
	 * Ta metoda może być wywołana ręcznie, ale WordPress już
	 * obsługuje większość przez nasze filtry.
	 *
	 * @return void
	 */
	public function remove_default_tags(): void {
		// Usuń domyślny canonical (WordPress 4.4+).
		remove_action( 'wp_head', 'rel_canonical' );

		// Usuń shortlink.
		remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );

		// Generator.
		remove_action( 'wp_head', 'wp_generator' );
	}

	/**
	 * Sprawdza czy powinniśmy renderować SEO dla aktualnej strony.
	 *
	 * @return bool True jeśli powinniśmy renderować.
	 */
	public function should_render(): bool {
		// Nie renderuj w admin.
		if ( is_admin() ) {
			return false;
		}

		// Nie renderuj dla feedów.
		if ( is_feed() ) {
			return false;
		}

		// Nie renderuj dla REST API.
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return false;
		}

		// Nie renderuj dla AJAX.
		if ( wp_doing_ajax() ) {
			return false;
		}

		// Nie renderuj dla XML sitemaps.
		if ( is_robots() ) {
			return false;
		}

		/**
		 * Filtruje czy renderować SEO.
		 *
		 * @since 1.0.0
		 * @param bool $should Czy renderować.
		 */
		return apply_filters( 'seo_nlc_should_render', true );
	}
}
