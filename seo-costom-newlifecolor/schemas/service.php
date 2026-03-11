<?php
/**
 * Schema template: Service.
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
 * Generuje schema Service.
 *
 * @param int         $post_id    ID posta.
 * @param int|null    $service_id ID usługi z ustawień globalnych.
 * @return array|null Schema Service lub null.
 */
function get_service_schema( int $post_id, ?int $service_id = null ): ?array {
	$services = get_option( 'seo_nlc_services', array() );

	if ( ! is_array( $services ) || empty( $services ) ) {
		return null;
	}

	// Jeśli podano konkretną usługę.
	if ( null !== $service_id && isset( $services[ $service_id ] ) ) {
		$service = $services[ $service_id ];
	} else {
		// Użyj pierwszej aktywnej usługi.
		$service = null;
		foreach ( $services as $s ) {
			if ( ! empty( $s['active'] ) && ! empty( $s['name'] ) ) {
				$service = $s;
				break;
			}
		}
	}

	if ( empty( $service ) || empty( $service['name'] ) ) {
		return null;
	}

	$company_name = get_option( 'seo_nlc_company_name', get_bloginfo( 'name' ) );
	$lat          = get_option( 'seo_nlc_geo_lat', '' );
	$lng          = get_option( 'seo_nlc_geo_lng', '' );
	$radius       = (int) get_option( 'seo_nlc_radius', 50 );

	$schema = array(
		'@type'       => 'Service',
		'@id'         => get_permalink( $post_id ) . '#service',
		'serviceType' => $service['name'],
		'provider'    => array(
			'@id' => home_url( '/#organization' ),
		),
	);

	// Opis usługi.
	if ( ! empty( $service['description'] ) ) {
		$schema['description'] = $service['description'];
	}

	// Nazwa usługi.
	$schema['name'] = $service['name'];

	// Obszar działania jako GeoCircle.
	if ( ! empty( $lat ) && ! empty( $lng ) ) {
		$schema['areaServed'] = array(
			'@type'       => 'GeoCircle',
			'geoMidpoint' => array(
				'@type'     => 'GeoCoordinates',
				'latitude'  => (float) $lat,
				'longitude' => (float) $lng,
			),
			'geoRadius'   => $radius * 1000, // km to meters.
		);
	}

	// Katalog usług (wszystkie aktywne usługi).
	$active_services = array_filter(
		$services,
		function ( $s ) {
			return ! empty( $s['active'] ) && ! empty( $s['name'] );
		}
	);

	if ( count( $active_services ) > 1 ) {
		$catalog_items = array();
		$position      = 1;

		foreach ( $active_services as $s ) {
			$item = array(
				'@type'    => 'Offer',
				'position' => $position,
				'itemOffered' => array(
					'@type' => 'Service',
					'name'  => $s['name'],
				),
			);

			if ( ! empty( $s['description'] ) ) {
				$item['itemOffered']['description'] = $s['description'];
			}

			$catalog_items[] = $item;
			++$position;
		}

		$schema['hasOfferCatalog'] = array(
			'@type'           => 'OfferCatalog',
			'name'            => sprintf(
				/* translators: %s: Nazwa firmy */
				__( 'Usługi %s', 'seo-costom-newlifecolor' ),
				$company_name
			),
			'itemListElement' => $catalog_items,
		);
	}

	/**
	 * Filtruje schema Service.
	 *
	 * @since 1.0.0
	 * @param array $schema  Schema Service.
	 * @param int   $post_id ID posta.
	 */
	return apply_filters( 'seo_nlc_service_schema', $schema, $post_id );
}
