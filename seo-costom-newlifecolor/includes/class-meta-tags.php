<?php
/**
 * Klasa generatora meta tagów.
 *
 * Generuje meta tagi SEO, Open Graph i Twitter Cards.
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
 * Klasa Meta_Tags.
 *
 * Generuje i zarządza meta tagami dla SEO.
 *
 * @since 1.0.0
 */
class Meta_Tags {

	/**
	 * Konstruktor.
	 */
	public function __construct() {}

	/**
	 * Pobiera tytuł SEO dla posta/strony.
	 *
	 * @param int|null $post_id ID posta (null dla auto-detekcji).
	 * @return string Tytuł SEO.
	 */
	public function get_title( ?int $post_id = null ): string {
		$post_id = $this->get_post_id( $post_id );

		// Pobierz niestandardowy tytuł z meta.
		$custom_title = '';
		if ( $post_id > 0 ) {
			$custom_title = Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'title', '' );
		}

		// Jeśli jest niestandardowy tytuł, użyj go.
		if ( ! empty( $custom_title ) ) {
			$title = $custom_title;
		} else {
			// Użyj domyślnego tytułu WordPress.
			$title = $this->get_default_title( $post_id );
		}

		// Zastosuj format tytułu.
		$title = $this->apply_title_format( $title, $post_id );

		/**
		 * Filtruje tytuł SEO.
		 *
		 * @since 1.0.0
		 * @param string $title   Tytuł SEO.
		 * @param int    $post_id ID posta.
		 */
		return apply_filters( 'seo_nlc_meta_title', $title, $post_id );
	}

	/**
	 * Pobiera domyślny tytuł WordPress.
	 *
	 * @param int $post_id ID posta.
	 * @return string Domyślny tytuł.
	 */
	private function get_default_title( int $post_id ): string {
		if ( is_singular() && $post_id > 0 ) {
			return get_the_title( $post_id );
		}

		if ( is_front_page() ) {
			return get_bloginfo( 'name' );
		}

		if ( is_home() ) {
			$blog_page = (int) get_option( 'page_for_posts' );
			if ( $blog_page ) {
				return get_the_title( $blog_page );
			}
			return __( 'Blog', 'seo-costom-newlifecolor' );
		}

		if ( is_category() ) {
			return single_cat_title( '', false );
		}

		if ( is_tag() ) {
			return single_tag_title( '', false );
		}

		if ( is_author() ) {
			return get_the_author();
		}

		if ( is_post_type_archive() ) {
			return post_type_archive_title( '', false );
		}

		if ( is_archive() ) {
			return get_the_archive_title();
		}

		if ( is_search() ) {
			/* translators: %s: Fraza wyszukiwania */
			return sprintf( __( 'Wyniki wyszukiwania: %s', 'seo-costom-newlifecolor' ), get_search_query() );
		}

		if ( is_404() ) {
			return __( 'Strona nie znaleziona', 'seo-costom-newlifecolor' );
		}

		return get_bloginfo( 'name' );
	}

	/**
	 * Stosuje format tytułu z ustawień.
	 *
	 * @param string $title   Tytuł bazowy.
	 * @param int    $post_id ID posta.
	 * @return string Sformatowany tytuł.
	 */
	private function apply_title_format( string $title, int $post_id ): string {
		// Dla strony głównej - bez formatowania.
		if ( is_front_page() && ! is_home() ) {
			return $title;
		}

		$format    = get_option( 'seo_nlc_title_format', '{{title}} {{separator}} {{site_name}}' );
		$separator = get_option( 'seo_nlc_title_separator', '|' );
		$site_name = get_bloginfo( 'name' );

		// Zamień placeholders.
		$formatted = str_replace(
			array( '{{title}}', '{{separator}}', '{{site_name}}' ),
			array( $title, $separator, $site_name ),
			$format
		);

		// Usuń wielokrotne spacje.
		$formatted = preg_replace( '/\s+/', ' ', $formatted );

		return trim( $formatted );
	}

	/**
	 * Pobiera opis meta dla posta/strony.
	 *
	 * @param int|null $post_id ID posta.
	 * @return string Opis meta.
	 */
	public function get_description( ?int $post_id = null ): string {
		$post_id = $this->get_post_id( $post_id );

		// Niestandardowy opis z meta.
		$description = '';
		if ( $post_id > 0 ) {
			$description = Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'description', '' );
		}

		// Fallback na excerpt.
		if ( empty( $description ) && $post_id > 0 ) {
			$description = get_the_excerpt( $post_id );
		}

		// Fallback na domyślny opis.
		if ( empty( $description ) ) {
			$description = get_option( 'seo_nlc_default_description', '' );
		}

		// Fallback na opis witryny.
		if ( empty( $description ) ) {
			$description = get_bloginfo( 'description' );
		}

		// Ogranicz do 160 znaków.
		if ( mb_strlen( $description ) > 160 ) {
			$description = mb_substr( $description, 0, 157 ) . '...';
		}

		/**
		 * Filtruje opis meta.
		 *
		 * @since 1.0.0
		 * @param string $description Opis meta.
		 * @param int    $post_id     ID posta.
		 */
		return apply_filters( 'seo_nlc_meta_description', $description, $post_id );
	}

	/**
	 * Pobiera canonical URL.
	 *
	 * @param int|null $post_id ID posta.
	 * @return string Canonical URL.
	 */
	public function get_canonical( ?int $post_id = null ): string {
		$post_id = $this->get_post_id( $post_id );

		// Niestandardowy canonical z meta.
		if ( $post_id > 0 ) {
			$custom_canonical = Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'canonical_url', '' );
			if ( ! empty( $custom_canonical ) ) {
				return $custom_canonical;
			}
		}

		// Domyślny canonical.
		if ( is_singular() && $post_id > 0 ) {
			return get_permalink( $post_id );
		}

		if ( is_front_page() ) {
			return home_url( '/' );
		}

		if ( is_home() ) {
			$blog_page = (int) get_option( 'page_for_posts' );
			if ( $blog_page ) {
				return get_permalink( $blog_page );
			}
		}

		if ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
			if ( $term ) {
				return get_term_link( $term );
			}
		}

		if ( is_post_type_archive() ) {
			return get_post_type_archive_link( get_queried_object()->name );
		}

		// Fallback.
		global $wp;
		return home_url( $wp->request );
	}

	/**
	 * Pobiera robots meta.
	 *
	 * @param int|null $post_id ID posta.
	 * @return string Robots meta value.
	 */
	public function get_robots( ?int $post_id = null ): string {
		$robots = array();

		// Domyślnie index, follow.
		$robots[] = 'index';
		$robots[] = 'follow';

		// Noindex dla archiwów dat, wyszukiwania, 404.
		if ( is_search() || is_404() ) {
			$robots = array( 'noindex', 'follow' );
		}

		// Paginacja.
		if ( is_paged() ) {
			$robots[] = 'noarchive';
		}

		/**
		 * Filtruje robots meta.
		 *
		 * @since 1.0.0
		 * @param array    $robots  Tablica wartości robots.
		 * @param int|null $post_id ID posta.
		 */
		$robots = apply_filters( 'seo_nlc_robots', $robots, $post_id );

		return implode( ', ', array_unique( $robots ) );
	}

	/**
	 * Pobiera tagi Open Graph.
	 *
	 * @param int|null $post_id ID posta.
	 * @return array Tablica tagów OG.
	 */
	public function get_og_tags( ?int $post_id = null ): array {
		$post_id = $this->get_post_id( $post_id );

		$og = array(
			'og:locale'    => get_locale(),
			'og:type'      => $this->get_og_type( $post_id ),
			'og:site_name' => get_bloginfo( 'name' ),
		);

		// Title.
		$og_title = '';
		if ( $post_id > 0 ) {
			$og_title = Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'og_title', '' );
		}
		if ( empty( $og_title ) ) {
			$og_title = $this->get_title( $post_id );
		}
		$og['og:title'] = $og_title;

		// Description.
		$og_description = '';
		if ( $post_id > 0 ) {
			$og_description = Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'og_description', '' );
		}
		if ( empty( $og_description ) ) {
			$og_description = $this->get_description( $post_id );
		}
		$og['og:description'] = $og_description;

		// URL.
		$og['og:url'] = $this->get_canonical( $post_id );

		// Image.
		$og_image = $this->get_og_image( $post_id );
		if ( $og_image ) {
			$og['og:image']        = $og_image['url'];
			$og['og:image:width']  = $og_image['width'];
			$og['og:image:height'] = $og_image['height'];
			$og['og:image:alt']    = $og_image['alt'];
		}

		// Article specific.
		if ( 'article' === $og['og:type'] && $post_id > 0 ) {
			$post = get_post( $post_id );
			if ( $post ) {
				$og['article:published_time'] = gmdate( 'c', strtotime( $post->post_date_gmt ) );
				$og['article:modified_time']  = gmdate( 'c', strtotime( $post->post_modified_gmt ) );

				$author = get_userdata( $post->post_author );
				if ( $author ) {
					$og['article:author'] = $author->display_name;
				}

				$categories = get_the_category( $post_id );
				if ( ! empty( $categories ) ) {
					$og['article:section'] = $categories[0]->name;
				}

				$tags = get_the_tags( $post_id );
				if ( $tags ) {
					$og['article:tag'] = array_map(
						function ( $tag ) {
							return $tag->name;
						},
						$tags
					);
				}
			}
		}

		/**
		 * Filtruje tagi Open Graph.
		 *
		 * @since 1.0.0
		 * @param array $og      Tablica tagów OG.
		 * @param int   $post_id ID posta.
		 */
		return apply_filters( 'seo_nlc_og_tags', $og, $post_id );
	}

	/**
	 * Pobiera typ OG.
	 *
	 * @param int $post_id ID posta.
	 * @return string Typ OG.
	 */
	private function get_og_type( int $post_id ): string {
		if ( is_front_page() ) {
			return 'website';
		}

		if ( is_singular( 'post' ) ) {
			return 'article';
		}

		return 'website';
	}

	/**
	 * Pobiera obraz OG.
	 *
	 * @param int $post_id ID posta.
	 * @return array|null Dane obrazu lub null.
	 */
	private function get_og_image( int $post_id ): ?array {
		$image_id = 0;

		// Niestandardowy obraz z meta.
		if ( $post_id > 0 ) {
			$image_id = (int) Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'og_image', 0 );
		}

		// Featured image.
		if ( ! $image_id && $post_id > 0 ) {
			$image_id = (int) get_post_thumbnail_id( $post_id );
		}

		// Domyślny obraz OG.
		if ( ! $image_id ) {
			$image_id = (int) get_option( 'seo_nlc_default_og_image', 0 );
		}

		// Logo firmy jako fallback.
		if ( ! $image_id ) {
			$image_id = (int) get_option( 'seo_nlc_logo', 0 );
		}

		if ( ! $image_id ) {
			return null;
		}

		$image_url  = wp_get_attachment_url( $image_id );
		$image_data = wp_get_attachment_metadata( $image_id );
		$image_alt  = get_post_meta( $image_id, '_wp_attachment_image_alt', true );

		if ( ! $image_url ) {
			return null;
		}

		return array(
			'url'    => $image_url,
			'width'  => $image_data['width'] ?? 1200,
			'height' => $image_data['height'] ?? 630,
			'alt'    => $image_alt ?: get_the_title( $post_id ),
		);
	}

	/**
	 * Pobiera tagi Twitter Card.
	 *
	 * @param int|null $post_id ID posta.
	 * @return array Tablica tagów Twitter.
	 */
	public function get_twitter_tags( ?int $post_id = null ): array {
		$post_id = $this->get_post_id( $post_id );

		// Typ karty.
		$card_type = 'summary_large_image';
		if ( $post_id > 0 ) {
			$card_type = Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'twitter_card', 'summary_large_image' );
		}

		$twitter = array(
			'twitter:card' => $card_type,
		);

		// Title.
		$twitter_title = '';
		if ( $post_id > 0 ) {
			$twitter_title = Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'twitter_title', '' );
		}
		if ( empty( $twitter_title ) ) {
			// Fallback na OG title.
			$og_tags       = $this->get_og_tags( $post_id );
			$twitter_title = $og_tags['og:title'] ?? $this->get_title( $post_id );
		}
		$twitter['twitter:title'] = $twitter_title;

		// Description.
		$twitter_description = '';
		if ( $post_id > 0 ) {
			$twitter_description = Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'twitter_description', '' );
		}
		if ( empty( $twitter_description ) ) {
			$og_tags             = $this->get_og_tags( $post_id );
			$twitter_description = $og_tags['og:description'] ?? $this->get_description( $post_id );
		}
		$twitter['twitter:description'] = $twitter_description;

		// Image.
		$twitter_image_id = 0;
		if ( $post_id > 0 ) {
			$twitter_image_id = (int) Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'twitter_image', 0 );
		}

		if ( $twitter_image_id ) {
			$twitter_image_url = wp_get_attachment_url( $twitter_image_id );
			if ( $twitter_image_url ) {
				$twitter['twitter:image'] = $twitter_image_url;
			}
		} else {
			// Fallback na OG image.
			$og_image = $this->get_og_image( $post_id );
			if ( $og_image ) {
				$twitter['twitter:image'] = $og_image['url'];
			}
		}

		/**
		 * Filtruje tagi Twitter Card.
		 *
		 * @since 1.0.0
		 * @param array $twitter Tablica tagów Twitter.
		 * @param int   $post_id ID posta.
		 */
		return apply_filters( 'seo_nlc_twitter_tags', $twitter, $post_id );
	}

	/**
	 * Pobiera post ID (z auto-detekcją).
	 *
	 * @param int|null $post_id ID posta.
	 * @return int ID posta.
	 */
	private function get_post_id( ?int $post_id ): int {
		if ( null !== $post_id ) {
			return $post_id;
		}

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
}
