<?php
/**
 * Klasa generatora Schema.org JSON-LD.
 *
 * Orkiestruje generowanie wszystkich schematów i łączy je w @graph.
 *
 * @package SeoCustomNLC
 * @since   1.0.0
 */

declare(strict_types=1);

namespace SeoCustomNLC;

// Załaduj schema templates.
require_once SEO_NLC_PATH . 'schemas/local-business.php';
require_once SEO_NLC_PATH . 'schemas/service.php';
require_once SEO_NLC_PATH . 'schemas/image-gallery.php';
require_once SEO_NLC_PATH . 'schemas/video-object.php';
require_once SEO_NLC_PATH . 'schemas/article.php';
require_once SEO_NLC_PATH . 'schemas/faq-page.php';
require_once SEO_NLC_PATH . 'schemas/breadcrumb.php';

// Zabezpieczenie przed bezpośrednim dostępem.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Klasa Schema_Generator.
 *
 * Generuje Schema.org JSON-LD dla różnych typów treści.
 *
 * @since 1.0.0
 */
class Schema_Generator {

	/**
	 * Konstruktor.
	 */
	public function __construct() {}

	/**
	 * Generuje pełny @graph Schema.org dla aktualnej strony.
	 *
	 * @param int|null $post_id ID posta (null dla auto-detekcji).
	 * @return array Tablica z @context i @graph.
	 */
	public function generate_graph( ?int $post_id = null ): array {
		// Auto-detekcja post ID.
		if ( null === $post_id ) {
			$post_id = $this->get_current_post_id();
		}

		$graph = array();

		// 1. LocalBusiness (zawsze).
		$local_business = \SeoCustomNLC\Schemas\get_local_business_schema();
		if ( $local_business ) {
			$graph[] = $local_business;
		}

		// 2. WebSite schema.
		$website = $this->get_website_schema();
		if ( $website ) {
			$graph[] = $website;
		}

		// Jeśli mamy konkretny post/stronę.
		if ( $post_id > 0 ) {
			// 3. WebPage schema.
			$webpage = $this->get_webpage_schema( $post_id );
			if ( $webpage ) {
				$graph[] = $webpage;
			}

			// 4. BreadcrumbList.
			$breadcrumbs = \SeoCustomNLC\Schemas\get_breadcrumb_schema( $post_id );
			if ( $breadcrumbs ) {
				$graph[] = $breadcrumbs;
			}

			// 5. Schema zależne od typu wybranego w meta box.
			$schema_type = Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'schema_type', 'WebPage' );
			$type_schema = $this->get_schema_by_type( $post_id, $schema_type );

			if ( $type_schema ) {
				// Jeśli to tablica schematów (np. z video gallery).
				if ( isset( $type_schema['@type'] ) ) {
					$graph[] = $type_schema;
				} else {
					$graph = array_merge( $graph, $type_schema );
				}
			}

			// 6. Auto-generowane schematy obrazów.
			$auto_images = Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'auto_images', '1' );
			if ( '1' === $auto_images && 'ImageGallery' !== $schema_type ) {
				$images = $this->get_auto_image_schemas( $post_id );
				$graph  = array_merge( $graph, $images );
			}

			// 7. Auto-generowane schematy wideo.
			$auto_videos = Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'auto_videos', '1' );
			if ( '1' === $auto_videos && 'VideoGallery' !== $schema_type ) {
				$videos = $this->get_auto_video_schemas( $post_id );
				$graph  = array_merge( $graph, $videos );
			}
		} elseif ( is_archive() ) {
			// Dla archiwów.
			$archive_breadcrumbs = \SeoCustomNLC\Schemas\get_archive_breadcrumb_schema();
			if ( $archive_breadcrumbs ) {
				$graph[] = $archive_breadcrumbs;
			}
		}

		// Usuń duplikaty i puste elementy.
		$graph = array_filter( $graph );
		$graph = $this->deduplicate_graph( $graph );

		/**
		 * Filtruje pełny @graph.
		 *
		 * @since 1.0.0
		 * @param array    $graph   Tablica schematów.
		 * @param int|null $post_id ID posta.
		 */
		$graph = apply_filters( 'seo_nlc_schema_graph', $graph, $post_id );

		return array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		);
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
	 * Generuje schema WebSite.
	 *
	 * @return array Schema WebSite.
	 */
	public function get_website_schema(): array {
		$company_name = get_option( 'seo_nlc_company_name', get_bloginfo( 'name' ) );

		$schema = array(
			'@type'       => 'WebSite',
			'@id'         => home_url( '/#website' ),
			'url'         => home_url( '/' ),
			'name'        => $company_name,
			'description' => get_bloginfo( 'description' ),
			'publisher'   => array(
				'@id' => home_url( '/#organization' ),
			),
			'inLanguage'  => get_bloginfo( 'language' ),
		);

		// SearchAction dla wyszukiwarki.
		$schema['potentialAction'] = array(
			'@type'       => 'SearchAction',
			'target'      => array(
				'@type'       => 'EntryPoint',
				'urlTemplate' => home_url( '/?s={search_term_string}' ),
			),
			'query-input' => 'required name=search_term_string',
		);

		/**
		 * Filtruje schema WebSite.
		 *
		 * @since 1.0.0
		 * @param array $schema Schema WebSite.
		 */
		return apply_filters( 'seo_nlc_website_schema', $schema );
	}

	/**
	 * Generuje schema WebPage.
	 *
	 * @param int $post_id ID posta.
	 * @return array|null Schema WebPage lub null.
	 */
	public function get_webpage_schema( int $post_id ): ?array {
		$post = get_post( $post_id );

		if ( ! $post ) {
			return null;
		}

		$seo_title       = Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'title', '' );
		$seo_description = Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'description', '' );

		$schema = array(
			'@type'         => 'WebPage',
			'@id'           => get_permalink( $post_id ) . '#webpage',
			'url'           => get_permalink( $post_id ),
			'name'          => ! empty( $seo_title ) ? $seo_title : get_the_title( $post_id ),
			'isPartOf'      => array(
				'@id' => home_url( '/#website' ),
			),
			'datePublished' => gmdate( 'c', strtotime( $post->post_date_gmt ) ),
			'dateModified'  => gmdate( 'c', strtotime( $post->post_modified_gmt ) ),
			'inLanguage'    => get_bloginfo( 'language' ),
		);

		// Opis.
		if ( ! empty( $seo_description ) ) {
			$schema['description'] = $seo_description;
		} else {
			$excerpt = get_the_excerpt( $post_id );
			if ( ! empty( $excerpt ) ) {
				$schema['description'] = $excerpt;
			}
		}

		// Featured image.
		$featured_image_id = get_post_thumbnail_id( $post_id );
		if ( $featured_image_id ) {
			$image_url = wp_get_attachment_url( $featured_image_id );
			if ( $image_url ) {
				$schema['primaryImageOfPage'] = array(
					'@type' => 'ImageObject',
					'url'   => $image_url,
				);
			}
		}

		// Breadcrumb reference.
		$schema['breadcrumb'] = array(
			'@id' => get_permalink( $post_id ) . '#breadcrumb',
		);

		// Potential action (read).
		$schema['potentialAction'] = array(
			'@type'  => 'ReadAction',
			'target' => array( get_permalink( $post_id ) ),
		);

		/**
		 * Filtruje schema WebPage.
		 *
		 * @since 1.0.0
		 * @param array $schema  Schema WebPage.
		 * @param int   $post_id ID posta.
		 */
		return apply_filters( 'seo_nlc_webpage_schema', $schema, $post_id );
	}

	/**
	 * Pobiera schema zależnie od wybranego typu.
	 *
	 * @param int    $post_id     ID posta.
	 * @param string $schema_type Typ schema.
	 * @return array|null Schema lub null.
	 */
	private function get_schema_by_type( int $post_id, string $schema_type ): ?array {
		switch ( $schema_type ) {
			case 'Service':
				$service_id = Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'service_id', '' );
				return \SeoCustomNLC\Schemas\get_service_schema(
					$post_id,
					'' !== $service_id ? (int) $service_id : null
				);

			case 'ImageGallery':
				return \SeoCustomNLC\Schemas\get_image_gallery_schema( $post_id );

			case 'VideoGallery':
				$video_urls = Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'video_urls', '' );
				return \SeoCustomNLC\Schemas\get_video_gallery_schema( $post_id, $video_urls );

			case 'Article':
				$post = get_post( $post_id );
				if ( $post && 'post' === $post->post_type ) {
					return \SeoCustomNLC\Schemas\get_blog_posting_schema( $post_id );
				}
				return \SeoCustomNLC\Schemas\get_article_schema( $post_id );

			case 'FAQPage':
				$faq_items = Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'faq_items', array() );
				return \SeoCustomNLC\Schemas\get_faq_page_schema( $post_id, $faq_items );

			case 'WebPage':
			default:
				// WebPage jest już generowany w głównym flow.
				return null;
		}
	}

	/**
	 * Pobiera auto-generowane schematy obrazów.
	 *
	 * @param int $post_id ID posta.
	 * @return array Tablica ImageObject schemas.
	 */
	private function get_auto_image_schemas( int $post_id ): array {
		$images = \SeoCustomNLC\Schemas\get_images_from_content( $post_id );

		// Ogranicz do max 10 obrazów.
		return array_slice( $images, 0, 10 );
	}

	/**
	 * Pobiera auto-generowane schematy wideo.
	 *
	 * @param int $post_id ID posta.
	 * @return array Tablica VideoObject schemas.
	 */
	private function get_auto_video_schemas( int $post_id ): array {
		$videos = \SeoCustomNLC\Schemas\get_videos_from_content( $post_id );

		// Ogranicz do max 5 filmów.
		return array_slice( $videos, 0, 5 );
	}

	/**
	 * Usuwa duplikaty z @graph na podstawie @id.
	 *
	 * @param array $graph Tablica schematów.
	 * @return array Tablica bez duplikatów.
	 */
	private function deduplicate_graph( array $graph ): array {
		$ids  = array();
		$unique = array();

		foreach ( $graph as $schema ) {
			if ( ! is_array( $schema ) ) {
				continue;
			}

			// Jeśli ma @id, sprawdź duplikaty.
			if ( isset( $schema['@id'] ) ) {
				if ( in_array( $schema['@id'], $ids, true ) ) {
					continue; // Pomiń duplikat.
				}
				$ids[] = $schema['@id'];
			}

			$unique[] = $schema;
		}

		return $unique;
	}

	/**
	 * Renderuje JSON-LD jako string.
	 *
	 * @param int|null $post_id ID posta.
	 * @return string JSON-LD string.
	 */
	public function render_json_ld( ?int $post_id = null ): string {
		$schema = $this->generate_graph( $post_id );

		// Usuń pusty @graph.
		if ( empty( $schema['@graph'] ) ) {
			return '';
		}

		$json = wp_json_encode(
			$schema,
			JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
		);

		if ( ! $json ) {
			return '';
		}

		return sprintf(
			'<script type="application/ld+json">%s</script>',
			$json
		);
	}

	/**
	 * Sprawdza czy dla danego posta powinna być generowana schema.
	 *
	 * @param int $post_id ID posta.
	 * @return bool True jeśli schema powinna być generowana.
	 */
	public function should_generate_schema( int $post_id ): bool {
		$post = get_post( $post_id );

		if ( ! $post ) {
			return false;
		}

		// Tylko opublikowane treści.
		if ( 'publish' !== $post->post_status ) {
			return false;
		}

		// Nie dla stron chronionych hasłem.
		if ( ! empty( $post->post_password ) ) {
			return false;
		}

		/**
		 * Filtruje czy generować schema.
		 *
		 * @since 1.0.0
		 * @param bool $should  Czy generować.
		 * @param int  $post_id ID posta.
		 */
		return apply_filters( 'seo_nlc_should_generate_schema', true, $post_id );
	}
}
