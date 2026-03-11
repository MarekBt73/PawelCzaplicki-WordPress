<?php
/**
 * Schema template: BreadcrumbList.
 *
 * @package SeoCustomNLC
 * @since   1.0.0
 */

declare(strict_types=1);

namespace SeoCustomNLC\Schemas;

// Zabezpieczenie przed bezpośrednim dostępem.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generuje schema BreadcrumbList.
 *
 * @param int $post_id ID posta (0 dla strony głównej/archiwów).
 * @return array|null Schema BreadcrumbList lub null.
 */
function get_breadcrumb_schema( int $post_id = 0 ): ?array {
	$breadcrumbs = array();
	$position    = 1;

	// Zawsze zaczynaj od strony głównej.
	$breadcrumbs[] = array(
		'@type'    => 'ListItem',
		'position' => $position,
		'name'     => __( 'Strona główna', 'seo-costom-newlifecolor' ),
		'item'     => home_url( '/' ),
	);
	++$position;

	// Jeśli to strona główna, zakończ.
	if ( is_front_page() || 0 === $post_id ) {
		// Dla strony głównej nie generujemy breadcrumbs.
		return null;
	}

	$post = get_post( $post_id );

	if ( ! $post ) {
		return null;
	}

	// Dla wpisów blogowych.
	if ( 'post' === $post->post_type ) {
		// Strona bloga (jeśli ustawiona).
		$blog_page_id = (int) get_option( 'page_for_posts' );

		if ( $blog_page_id ) {
			$breadcrumbs[] = array(
				'@type'    => 'ListItem',
				'position' => $position,
				'name'     => get_the_title( $blog_page_id ),
				'item'     => get_permalink( $blog_page_id ),
			);
			++$position;
		}

		// Kategorie.
		$categories = get_the_category( $post_id );

		if ( ! empty( $categories ) ) {
			// Użyj głównej kategorii.
			$primary_category = $categories[0];

			// Pobierz hierarchię kategorii.
			$category_hierarchy = get_category_hierarchy( $primary_category );

			foreach ( $category_hierarchy as $cat ) {
				$breadcrumbs[] = array(
					'@type'    => 'ListItem',
					'position' => $position,
					'name'     => $cat->name,
					'item'     => get_category_link( $cat->term_id ),
				);
				++$position;
			}
		}
	}

	// Dla stron.
	if ( 'page' === $post->post_type ) {
		// Pobierz hierarchię rodziców.
		$ancestors = get_post_ancestors( $post_id );

		if ( ! empty( $ancestors ) ) {
			// Odwróć kolejność (od najwyższego rodzica).
			$ancestors = array_reverse( $ancestors );

			foreach ( $ancestors as $ancestor_id ) {
				$breadcrumbs[] = array(
					'@type'    => 'ListItem',
					'position' => $position,
					'name'     => get_the_title( $ancestor_id ),
					'item'     => get_permalink( $ancestor_id ),
				);
				++$position;
			}
		}
	}

	// Dla custom post types.
	if ( ! in_array( $post->post_type, array( 'post', 'page' ), true ) ) {
		$post_type_obj = get_post_type_object( $post->post_type );

		if ( $post_type_obj && $post_type_obj->has_archive ) {
			$breadcrumbs[] = array(
				'@type'    => 'ListItem',
				'position' => $position,
				'name'     => $post_type_obj->labels->name,
				'item'     => get_post_type_archive_link( $post->post_type ),
			);
			++$position;
		}
	}

	// Aktualny wpis/strona.
	$breadcrumbs[] = array(
		'@type'    => 'ListItem',
		'position' => $position,
		'name'     => get_the_title( $post_id ),
		'item'     => get_permalink( $post_id ),
	);

	// Jeśli tylko strona główna, nie generuj schema.
	if ( count( $breadcrumbs ) < 2 ) {
		return null;
	}

	$schema = array(
		'@type'           => 'BreadcrumbList',
		'@id'             => get_permalink( $post_id ) . '#breadcrumb',
		'itemListElement' => $breadcrumbs,
	);

	/**
	 * Filtruje schema BreadcrumbList.
	 *
	 * @since 1.0.0
	 * @param array $schema  Schema BreadcrumbList.
	 * @param int   $post_id ID posta.
	 */
	return apply_filters( 'seo_nlc_breadcrumb_schema', $schema, $post_id );
}

/**
 * Pobiera hierarchię kategorii (od głównego rodzica).
 *
 * @param \WP_Term $category Kategoria.
 * @return array Tablica kategorii od rodzica do danej kategorii.
 */
function get_category_hierarchy( \WP_Term $category ): array {
	$hierarchy = array( $category );

	// Pobierz rodziców.
	$parent_id = $category->parent;

	while ( $parent_id > 0 ) {
		$parent = get_term( $parent_id, 'category' );

		if ( ! $parent || is_wp_error( $parent ) ) {
			break;
		}

		array_unshift( $hierarchy, $parent );
		$parent_id = $parent->parent;
	}

	return $hierarchy;
}

/**
 * Generuje breadcrumbs dla archiwum.
 *
 * @return array|null Schema BreadcrumbList lub null.
 */
function get_archive_breadcrumb_schema(): ?array {
	$breadcrumbs = array();
	$position    = 1;

	// Strona główna.
	$breadcrumbs[] = array(
		'@type'    => 'ListItem',
		'position' => $position,
		'name'     => __( 'Strona główna', 'seo-costom-newlifecolor' ),
		'item'     => home_url( '/' ),
	);
	++$position;

	$current_url  = '';
	$current_name = '';

	// Archiwum kategorii.
	if ( is_category() ) {
		$category     = get_queried_object();
		$current_name = $category->name;
		$current_url  = get_category_link( $category->term_id );

		// Hierarchia kategorii.
		if ( $category->parent ) {
			$hierarchy = get_category_hierarchy( $category );
			array_pop( $hierarchy ); // Usuń aktualną kategorię.

			foreach ( $hierarchy as $cat ) {
				$breadcrumbs[] = array(
					'@type'    => 'ListItem',
					'position' => $position,
					'name'     => $cat->name,
					'item'     => get_category_link( $cat->term_id ),
				);
				++$position;
			}
		}
	}

	// Archiwum tagów.
	if ( is_tag() ) {
		$tag          = get_queried_object();
		$current_name = $tag->name;
		$current_url  = get_tag_link( $tag->term_id );
	}

	// Archiwum autora.
	if ( is_author() ) {
		$author       = get_queried_object();
		$current_name = $author->display_name;
		$current_url  = get_author_posts_url( $author->ID );
	}

	// Archiwum daty.
	if ( is_date() ) {
		if ( is_year() ) {
			$current_name = get_the_date( 'Y' );
			$current_url  = get_year_link( get_the_date( 'Y' ) );
		} elseif ( is_month() ) {
			$current_name = get_the_date( 'F Y' );
			$current_url  = get_month_link( get_the_date( 'Y' ), get_the_date( 'm' ) );
		} elseif ( is_day() ) {
			$current_name = get_the_date();
			$current_url  = get_day_link( get_the_date( 'Y' ), get_the_date( 'm' ), get_the_date( 'd' ) );
		}
	}

	// Archiwum CPT.
	if ( is_post_type_archive() ) {
		$post_type    = get_queried_object();
		$current_name = $post_type->labels->name;
		$current_url  = get_post_type_archive_link( $post_type->name );
	}

	// Dodaj aktualny element.
	if ( ! empty( $current_name ) && ! empty( $current_url ) ) {
		$breadcrumbs[] = array(
			'@type'    => 'ListItem',
			'position' => $position,
			'name'     => $current_name,
			'item'     => $current_url,
		);
	}

	// Jeśli tylko strona główna, nie generuj schema.
	if ( count( $breadcrumbs ) < 2 ) {
		return null;
	}

	$schema = array(
		'@type'           => 'BreadcrumbList',
		'@id'             => $current_url . '#breadcrumb',
		'itemListElement' => $breadcrumbs,
	);

	/**
	 * Filtruje schema BreadcrumbList dla archiwów.
	 *
	 * @since 1.0.0
	 * @param array $schema Schema BreadcrumbList.
	 */
	return apply_filters( 'seo_nlc_archive_breadcrumb_schema', $schema );
}
