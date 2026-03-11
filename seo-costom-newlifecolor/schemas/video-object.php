<?php
/**
 * Schema template: VideoObject / VideoGallery.
 *
 * @package SeoCustomNLC
 * @since   1.0.0
 */

declare(strict_types=1);

namespace SeoCustomNLC\Schemas;

use SeoCustomNLC\Validator;

// Zabezpieczenie przed bezpośrednim dostępem.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generuje schema VideoGallery.
 *
 * @param int    $post_id    ID posta.
 * @param string $video_urls Lista URL-i wideo (jeden na linię).
 * @return array|null Schema ItemList z VideoObject lub null.
 */
function get_video_gallery_schema( int $post_id, string $video_urls = '' ): ?array {
	$videos = array();

	// Parsuj URL-e z meta.
	if ( ! empty( $video_urls ) ) {
		$urls = array_filter( array_map( 'trim', explode( "\n", $video_urls ) ) );

		foreach ( $urls as $url ) {
			$video_schema = get_video_object_from_url( $url, $post_id );
			if ( $video_schema ) {
				$videos[] = $video_schema;
			}
		}
	}

	// Szukaj wideo w treści posta.
	$content_videos = get_videos_from_content( $post_id );
	foreach ( $content_videos as $video ) {
		// Sprawdź czy nie ma duplikatu.
		$exists = false;
		foreach ( $videos as $v ) {
			if ( isset( $v['embedUrl'] ) && $v['embedUrl'] === $video['embedUrl'] ) {
				$exists = true;
				break;
			}
		}
		if ( ! $exists ) {
			$videos[] = $video;
		}
	}

	if ( empty( $videos ) ) {
		return null;
	}

	// Jeśli tylko jedno wideo, zwróć pojedynczy VideoObject.
	if ( count( $videos ) === 1 ) {
		return $videos[0];
	}

	// Wiele wideo - zwróć jako ItemList.
	$item_list = array();
	$position  = 1;

	foreach ( $videos as $video ) {
		$item_list[] = array(
			'@type'    => 'ListItem',
			'position' => $position,
			'item'     => $video,
		);
		++$position;
	}

	$schema = array(
		'@type'           => 'ItemList',
		'@id'             => get_permalink( $post_id ) . '#videogallery',
		'name'            => sprintf(
			/* translators: %s: Tytuł posta */
			__( 'Filmy - %s', 'seo-costom-newlifecolor' ),
			get_the_title( $post_id )
		),
		'numberOfItems'   => count( $videos ),
		'itemListElement' => $item_list,
	);

	/**
	 * Filtruje schema VideoGallery.
	 *
	 * @since 1.0.0
	 * @param array $schema  Schema VideoGallery.
	 * @param int   $post_id ID posta.
	 */
	return apply_filters( 'seo_nlc_video_gallery_schema', $schema, $post_id );
}

/**
 * Generuje schema VideoObject z URL.
 *
 * @param string $url     URL wideo.
 * @param int    $post_id ID posta dla kontekstu.
 * @return array|null Schema VideoObject lub null.
 */
function get_video_object_from_url( string $url, int $post_id = 0 ): ?array {
	$validator  = new Validator();
	$video_data = $validator->parse_video_url( $url );

	if ( ! $video_data ) {
		return null;
	}

	$post = $post_id ? get_post( $post_id ) : null;

	$schema = array(
		'@type'       => 'VideoObject',
		'@id'         => $video_data['url'] . '#video',
		'contentUrl'  => $video_data['url'],
		'embedUrl'    => $video_data['embed_url'],
	);

	// Nazwa wideo - użyj tytułu posta lub ID wideo.
	if ( $post ) {
		$schema['name'] = $post->post_title;
	} else {
		$schema['name'] = sprintf(
			/* translators: %s: ID wideo */
			__( 'Film %s', 'seo-costom-newlifecolor' ),
			$video_data['id']
		);
	}

	// Opis.
	if ( $post && ! empty( $post->post_excerpt ) ) {
		$schema['description'] = $post->post_excerpt;
	}

	// Thumbnail (dla YouTube).
	if ( ! empty( $video_data['thumbnail'] ) ) {
		$schema['thumbnailUrl'] = $video_data['thumbnail'];
	}

	// Data uploadu - użyj daty publikacji posta.
	if ( $post ) {
		$schema['uploadDate'] = gmdate( 'c', strtotime( $post->post_date ) );
	}

	/**
	 * Filtruje schema VideoObject.
	 *
	 * @since 1.0.0
	 * @param array  $schema     Schema VideoObject.
	 * @param array  $video_data Dane sparsowanego wideo.
	 * @param int    $post_id    ID posta.
	 */
	return apply_filters( 'seo_nlc_video_object_schema', $schema, $video_data, $post_id );
}

/**
 * Wyciąga wideo z treści posta.
 *
 * @param int $post_id ID posta.
 * @return array Tablica VideoObject schemas.
 */
function get_videos_from_content( int $post_id ): array {
	$post    = get_post( $post_id );
	$videos  = array();
	$content = $post->post_content;

	// Parsuj bloki Gutenberg.
	$blocks    = parse_blocks( $content );
	$video_urls = extract_video_urls_from_blocks( $blocks );

	// Szukaj URL-ów YouTube/Vimeo w HTML.
	$html_urls = extract_video_urls_from_html( $content );
	$video_urls = array_unique( array_merge( $video_urls, $html_urls ) );

	// Generuj VideoObject dla każdego wideo.
	foreach ( $video_urls as $url ) {
		$video_schema = get_video_object_from_url( $url, $post_id );
		if ( $video_schema ) {
			$videos[] = $video_schema;
		}
	}

	return $videos;
}

/**
 * Wyciąga URL-e wideo z bloków Gutenberg.
 *
 * @param array $blocks Tablica bloków.
 * @return array Tablica URL-ów wideo.
 */
function extract_video_urls_from_blocks( array $blocks ): array {
	$video_urls = array();

	foreach ( $blocks as $block ) {
		// Blok embed (YouTube, Vimeo).
		if ( in_array( $block['blockName'], array( 'core/embed', 'core-embed/youtube', 'core-embed/vimeo' ), true ) ) {
			if ( ! empty( $block['attrs']['url'] ) ) {
				$video_urls[] = $block['attrs']['url'];
			}
		}

		// Blok video.
		if ( 'core/video' === $block['blockName'] ) {
			if ( ! empty( $block['attrs']['src'] ) ) {
				$video_urls[] = $block['attrs']['src'];
			}
		}

		// Rekursywnie dla inner blocks.
		if ( ! empty( $block['innerBlocks'] ) ) {
			$inner_urls = extract_video_urls_from_blocks( $block['innerBlocks'] );
			$video_urls = array_merge( $video_urls, $inner_urls );
		}
	}

	return array_unique( $video_urls );
}

/**
 * Wyciąga URL-e wideo z HTML.
 *
 * @param string $content Treść HTML.
 * @return array Tablica URL-ów wideo.
 */
function extract_video_urls_from_html( string $content ): array {
	$video_urls = array();

	// YouTube.
	$youtube_patterns = array(
		'/https?:\/\/(?:www\.)?youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/i',
		'/https?:\/\/(?:www\.)?youtube\.com\/embed\/([a-zA-Z0-9_-]+)/i',
		'/https?:\/\/youtu\.be\/([a-zA-Z0-9_-]+)/i',
	);

	foreach ( $youtube_patterns as $pattern ) {
		if ( preg_match_all( $pattern, $content, $matches ) ) {
			foreach ( $matches[0] as $url ) {
				$video_urls[] = $url;
			}
		}
	}

	// Vimeo.
	$vimeo_patterns = array(
		'/https?:\/\/(?:www\.)?vimeo\.com\/(\d+)/i',
		'/https?:\/\/player\.vimeo\.com\/video\/(\d+)/i',
	);

	foreach ( $vimeo_patterns as $pattern ) {
		if ( preg_match_all( $pattern, $content, $matches ) ) {
			foreach ( $matches[0] as $url ) {
				$video_urls[] = $url;
			}
		}
	}

	return array_unique( $video_urls );
}
