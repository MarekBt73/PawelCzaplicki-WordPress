<?php
/**
 * Schema template: Article.
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
 * Generuje schema Article.
 *
 * @param int $post_id ID posta.
 * @return array|null Schema Article lub null.
 */
function get_article_schema( int $post_id ): ?array {
	$post = get_post( $post_id );

	if ( ! $post || 'publish' !== $post->post_status ) {
		return null;
	}

	$author = get_userdata( $post->post_author );

	$schema = array(
		'@type'         => 'Article',
		'@id'           => get_permalink( $post_id ) . '#article',
		'headline'      => get_the_title( $post_id ),
		'url'           => get_permalink( $post_id ),
		'mainEntityOfPage' => array(
			'@type' => 'WebPage',
			'@id'   => get_permalink( $post_id ),
		),
	);

	// Opis / excerpt.
	$excerpt = get_the_excerpt( $post_id );
	if ( ! empty( $excerpt ) ) {
		$schema['description'] = $excerpt;
	}

	// Treść artykułu (skrócona).
	$content = wp_strip_all_tags( $post->post_content );
	$content = wp_trim_words( $content, 100, '...' );
	if ( ! empty( $content ) ) {
		$schema['articleBody'] = $content;
	}

	// Data publikacji i modyfikacji.
	$schema['datePublished'] = gmdate( 'c', strtotime( $post->post_date_gmt ) );
	$schema['dateModified']  = gmdate( 'c', strtotime( $post->post_modified_gmt ) );

	// Autor.
	if ( $author ) {
		$author_schema = array(
			'@type' => 'Person',
			'name'  => $author->display_name,
		);

		// URL profilu autora.
		$author_url = get_author_posts_url( $author->ID );
		if ( $author_url ) {
			$author_schema['url'] = $author_url;
		}

		// Avatar autora.
		$avatar_url = get_avatar_url( $author->ID, array( 'size' => 96 ) );
		if ( $avatar_url ) {
			$author_schema['image'] = array(
				'@type'  => 'ImageObject',
				'url'    => $avatar_url,
				'width'  => 96,
				'height' => 96,
			);
		}

		$schema['author'] = $author_schema;
	}

	// Publisher (organizacja).
	$schema['publisher'] = array(
		'@id' => home_url( '/#organization' ),
	);

	// Obrazek wyróżniający.
	$featured_image_id = get_post_thumbnail_id( $post_id );
	if ( $featured_image_id ) {
		$image_url  = wp_get_attachment_url( $featured_image_id );
		$image_data = wp_get_attachment_metadata( $featured_image_id );

		if ( $image_url ) {
			$image_schema = array(
				'@type' => 'ImageObject',
				'url'   => $image_url,
			);

			if ( isset( $image_data['width'] ) && isset( $image_data['height'] ) ) {
				$image_schema['width']  = $image_data['width'];
				$image_schema['height'] = $image_data['height'];
			}

			$schema['image'] = $image_schema;
		}
	}

	// Kategorie jako keywords.
	$categories = get_the_category( $post_id );
	if ( ! empty( $categories ) ) {
		$keywords = array();
		foreach ( $categories as $category ) {
			$keywords[] = $category->name;
		}

		// Dodaj tagi.
		$tags = get_the_tags( $post_id );
		if ( $tags ) {
			foreach ( $tags as $tag ) {
				$keywords[] = $tag->name;
			}
		}

		$schema['keywords'] = implode( ', ', array_unique( $keywords ) );
	}

	// Sekcja artykułu (kategoria główna).
	if ( ! empty( $categories ) ) {
		$schema['articleSection'] = $categories[0]->name;
	}

	// Liczba słów.
	$word_count = str_word_count( wp_strip_all_tags( $post->post_content ) );
	if ( $word_count > 0 ) {
		$schema['wordCount'] = $word_count;
	}

	// Język.
	$schema['inLanguage'] = get_bloginfo( 'language' );

	/**
	 * Filtruje schema Article.
	 *
	 * @since 1.0.0
	 * @param array $schema  Schema Article.
	 * @param int   $post_id ID posta.
	 */
	return apply_filters( 'seo_nlc_article_schema', $schema, $post_id );
}

/**
 * Generuje schema BlogPosting (alternatywa dla Article).
 *
 * @param int $post_id ID posta.
 * @return array|null Schema BlogPosting lub null.
 */
function get_blog_posting_schema( int $post_id ): ?array {
	$schema = get_article_schema( $post_id );

	if ( ! $schema ) {
		return null;
	}

	// Zmień typ na BlogPosting.
	$schema['@type'] = 'BlogPosting';
	$schema['@id']   = get_permalink( $post_id ) . '#blogposting';

	/**
	 * Filtruje schema BlogPosting.
	 *
	 * @since 1.0.0
	 * @param array $schema  Schema BlogPosting.
	 * @param int   $post_id ID posta.
	 */
	return apply_filters( 'seo_nlc_blog_posting_schema', $schema, $post_id );
}
