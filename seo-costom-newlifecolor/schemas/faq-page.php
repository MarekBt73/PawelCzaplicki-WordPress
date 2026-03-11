<?php
/**
 * Schema template: FAQPage.
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
 * Generuje schema FAQPage.
 *
 * @param int   $post_id   ID posta.
 * @param array $faq_items Tablica pytań i odpowiedzi.
 * @return array|null Schema FAQPage lub null.
 */
function get_faq_page_schema( int $post_id, array $faq_items = array() ): ?array {
	// Jeśli nie podano FAQ, pobierz z meta.
	if ( empty( $faq_items ) ) {
		$faq_items = get_post_meta( $post_id, '_seo_nlc_faq_items', true );
	}

	if ( ! is_array( $faq_items ) || empty( $faq_items ) ) {
		return null;
	}

	// Przygotuj pytania.
	$main_entity = array();

	foreach ( $faq_items as $item ) {
		if ( empty( $item['question'] ) || empty( $item['answer'] ) ) {
			continue;
		}

		$main_entity[] = array(
			'@type'          => 'Question',
			'name'           => wp_strip_all_tags( $item['question'] ),
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => wp_kses_post( $item['answer'] ),
			),
		);
	}

	if ( empty( $main_entity ) ) {
		return null;
	}

	$schema = array(
		'@type'      => 'FAQPage',
		'@id'        => get_permalink( $post_id ) . '#faqpage',
		'mainEntity' => $main_entity,
	);

	/**
	 * Filtruje schema FAQPage.
	 *
	 * @since 1.0.0
	 * @param array $schema  Schema FAQPage.
	 * @param int   $post_id ID posta.
	 */
	return apply_filters( 'seo_nlc_faq_page_schema', $schema, $post_id );
}

/**
 * Automatycznie wykrywa FAQ z treści posta.
 *
 * Szuka wzorców pytań i odpowiedzi w treści (nagłówki H2/H3 z ? na końcu).
 *
 * @param int $post_id ID posta.
 * @return array Tablica FAQ wykrytych z treści.
 */
function detect_faq_from_content( int $post_id ): array {
	$post    = get_post( $post_id );
	$content = $post->post_content;
	$faq     = array();

	// Parsuj bloki Gutenberg.
	$blocks = parse_blocks( $content );

	foreach ( $blocks as $index => $block ) {
		// Szukaj nagłówków kończących się znakiem zapytania.
		if ( in_array( $block['blockName'], array( 'core/heading' ), true ) ) {
			$heading_html = $block['innerHTML'] ?? '';
			$heading_text = wp_strip_all_tags( $heading_html );

			// Sprawdź czy nagłówek kończy się znakiem zapytania.
			if ( preg_match( '/\?\s*$/', $heading_text ) ) {
				// Następny blok powinien być odpowiedzią (paragraph).
				$answer_text = '';

				for ( $i = $index + 1; $i < count( $blocks ); $i++ ) {
					$next_block = $blocks[ $i ];

					// Stop jeśli następny nagłówek.
					if ( 'core/heading' === $next_block['blockName'] ) {
						break;
					}

					// Zbieraj tekst z paragrafów i list.
					if ( in_array( $next_block['blockName'], array( 'core/paragraph', 'core/list' ), true ) ) {
						$block_html  = $next_block['innerHTML'] ?? '';
						$answer_text .= wp_kses_post( $block_html ) . ' ';
					}
				}

				$answer_text = trim( $answer_text );

				if ( ! empty( $answer_text ) ) {
					$faq[] = array(
						'question' => $heading_text,
						'answer'   => $answer_text,
					);
				}
			}
		}
	}

	/**
	 * Filtruje automatycznie wykryte FAQ.
	 *
	 * @since 1.0.0
	 * @param array $faq     Wykryte FAQ.
	 * @param int   $post_id ID posta.
	 */
	return apply_filters( 'seo_nlc_detected_faq', $faq, $post_id );
}

/**
 * Sprawdza czy post ma FAQ do wyświetlenia.
 *
 * @param int $post_id ID posta.
 * @return bool True jeśli post ma FAQ.
 */
function has_faq( int $post_id ): bool {
	$faq_items = get_post_meta( $post_id, '_seo_nlc_faq_items', true );

	return is_array( $faq_items ) && ! empty( $faq_items );
}
