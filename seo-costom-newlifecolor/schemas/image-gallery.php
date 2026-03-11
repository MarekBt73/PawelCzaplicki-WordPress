<?php
/**
 * Schema template: ImageGallery / ImageObject.
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
 * Generuje schema ImageGallery.
 *
 * @param int $post_id ID posta.
 * @return array|null Schema ImageGallery lub null.
 */
function get_image_gallery_schema( int $post_id ): ?array {
	$images = get_images_from_content( $post_id );

	if ( empty( $images ) ) {
		return null;
	}

	$schema = array(
		'@type'            => 'ImageGallery',
		'@id'              => get_permalink( $post_id ) . '#imagegallery',
		'name'             => get_the_title( $post_id ),
		'description'      => get_the_excerpt( $post_id ),
		'url'              => get_permalink( $post_id ),
		'numberOfItems'    => count( $images ),
		'associatedMedia'  => $images,
	);

	/**
	 * Filtruje schema ImageGallery.
	 *
	 * @since 1.0.0
	 * @param array $schema  Schema ImageGallery.
	 * @param int   $post_id ID posta.
	 */
	return apply_filters( 'seo_nlc_image_gallery_schema', $schema, $post_id );
}

/**
 * Generuje tablicę ImageObject schema z treści posta.
 *
 * @param int $post_id ID posta.
 * @return array Tablica ImageObject schemas.
 */
function get_images_from_content( int $post_id ): array {
	$post    = get_post( $post_id );
	$images  = array();
	$content = $post->post_content;

	// Pobierz obrazy z bloków gallery i image.
	$blocks = parse_blocks( $content );
	$image_ids = extract_image_ids_from_blocks( $blocks );

	// Dodaj obrazy z klasycznego edytora (jeśli nie ma bloków).
	if ( empty( $image_ids ) ) {
		$image_ids = extract_image_ids_from_html( $content );
	}

	// Dodaj featured image.
	$featured_id = get_post_thumbnail_id( $post_id );
	if ( $featured_id && ! in_array( $featured_id, $image_ids, true ) ) {
		array_unshift( $image_ids, $featured_id );
	}

	// Pobierz załączone media.
	$attachments = get_attached_media( 'image', $post_id );
	foreach ( $attachments as $attachment ) {
		if ( ! in_array( $attachment->ID, $image_ids, true ) ) {
			$image_ids[] = $attachment->ID;
		}
	}

	// Generuj ImageObject dla każdego obrazu.
	foreach ( $image_ids as $image_id ) {
		$image_schema = get_image_object_schema( $image_id );
		if ( $image_schema ) {
			$images[] = $image_schema;
		}
	}

	return $images;
}

/**
 * Generuje schema ImageObject dla pojedynczego obrazu.
 *
 * @param int $image_id ID obrazu (attachment).
 * @return array|null Schema ImageObject lub null.
 */
function get_image_object_schema( int $image_id ): ?array {
	$image_url = wp_get_attachment_url( $image_id );

	if ( ! $image_url ) {
		return null;
	}

	$image_data = wp_get_attachment_metadata( $image_id );
	$attachment = get_post( $image_id );

	$schema = array(
		'@type'      => 'ImageObject',
		'@id'        => $image_url,
		'url'        => $image_url,
		'contentUrl' => $image_url,
	);

	// Tytuł obrazu.
	if ( $attachment && ! empty( $attachment->post_title ) ) {
		$schema['name'] = $attachment->post_title;
	}

	// Alt text / opis.
	$alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
	if ( ! empty( $alt ) ) {
		$schema['description'] = $alt;
		$schema['caption']     = $alt;
	} elseif ( $attachment && ! empty( $attachment->post_excerpt ) ) {
		$schema['description'] = $attachment->post_excerpt;
		$schema['caption']     = $attachment->post_excerpt;
	}

	// Wymiary.
	if ( isset( $image_data['width'] ) && isset( $image_data['height'] ) ) {
		$schema['width']  = $image_data['width'];
		$schema['height'] = $image_data['height'];
	}

	// Data uploadu.
	if ( $attachment && $attachment->post_date ) {
		$schema['uploadDate'] = gmdate( 'c', strtotime( $attachment->post_date ) );
	}

	// Thumbnail.
	$thumbnail = wp_get_attachment_image_src( $image_id, 'thumbnail' );
	if ( $thumbnail && isset( $thumbnail[0] ) ) {
		$schema['thumbnail'] = array(
			'@type'      => 'ImageObject',
			'url'        => $thumbnail[0],
			'width'      => $thumbnail[1] ?? 150,
			'height'     => $thumbnail[2] ?? 150,
		);
	}

	/**
	 * Filtruje schema ImageObject.
	 *
	 * @since 1.0.0
	 * @param array $schema   Schema ImageObject.
	 * @param int   $image_id ID obrazu.
	 */
	return apply_filters( 'seo_nlc_image_object_schema', $schema, $image_id );
}

/**
 * Wyciąga ID obrazów z bloków Gutenberg.
 *
 * @param array $blocks Tablica bloków.
 * @return array Tablica ID obrazów.
 */
function extract_image_ids_from_blocks( array $blocks ): array {
	$image_ids = array();

	foreach ( $blocks as $block ) {
		// Blok image.
		if ( 'core/image' === $block['blockName'] && ! empty( $block['attrs']['id'] ) ) {
			$image_ids[] = (int) $block['attrs']['id'];
		}

		// Blok gallery.
		if ( 'core/gallery' === $block['blockName'] ) {
			// Nowy format (WordPress 5.9+).
			if ( ! empty( $block['innerBlocks'] ) ) {
				foreach ( $block['innerBlocks'] as $inner_block ) {
					if ( 'core/image' === $inner_block['blockName'] && ! empty( $inner_block['attrs']['id'] ) ) {
						$image_ids[] = (int) $inner_block['attrs']['id'];
					}
				}
			}
			// Stary format.
			if ( ! empty( $block['attrs']['ids'] ) ) {
				$image_ids = array_merge( $image_ids, array_map( 'intval', $block['attrs']['ids'] ) );
			}
		}

		// Blok cover.
		if ( 'core/cover' === $block['blockName'] && ! empty( $block['attrs']['id'] ) ) {
			$image_ids[] = (int) $block['attrs']['id'];
		}

		// Blok media-text.
		if ( 'core/media-text' === $block['blockName'] && ! empty( $block['attrs']['mediaId'] ) ) {
			$image_ids[] = (int) $block['attrs']['mediaId'];
		}

		// Rekursywnie dla inner blocks.
		if ( ! empty( $block['innerBlocks'] ) ) {
			$inner_ids = extract_image_ids_from_blocks( $block['innerBlocks'] );
			$image_ids = array_merge( $image_ids, $inner_ids );
		}
	}

	return array_unique( $image_ids );
}

/**
 * Wyciąga ID obrazów z HTML (klasyczny edytor).
 *
 * @param string $content Treść HTML.
 * @return array Tablica ID obrazów.
 */
function extract_image_ids_from_html( string $content ): array {
	$image_ids = array();

	// Szukaj obrazów z klasą wp-image-XXX.
	if ( preg_match_all( '/wp-image-(\d+)/i', $content, $matches ) ) {
		$image_ids = array_map( 'intval', $matches[1] );
	}

	// Szukaj obrazów z data-id.
	if ( preg_match_all( '/data-id=["\'](\d+)["\']/i', $content, $matches ) ) {
		$image_ids = array_merge( $image_ids, array_map( 'intval', $matches[1] ) );
	}

	return array_unique( $image_ids );
}
