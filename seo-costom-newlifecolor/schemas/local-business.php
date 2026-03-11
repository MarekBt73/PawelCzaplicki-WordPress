<?php
/**
 * Schema template: LocalBusiness.
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
 * Generuje schema LocalBusiness.
 *
 * @return array Schema LocalBusiness.
 */
function get_local_business_schema(): array {
	$company_name  = get_option( 'seo_nlc_company_name', '' );
	$business_type = get_option( 'seo_nlc_business_type', 'LocalBusiness' );
	$street        = get_option( 'seo_nlc_street', '' );
	$city          = get_option( 'seo_nlc_city', '' );
	$postal_code   = get_option( 'seo_nlc_postal_code', '' );
	$region        = get_option( 'seo_nlc_region', '' );
	$phone         = get_option( 'seo_nlc_phone', '' );
	$email         = get_option( 'seo_nlc_email', '' );
	$logo_id       = (int) get_option( 'seo_nlc_logo', 0 );
	$lat           = get_option( 'seo_nlc_geo_lat', '' );
	$lng           = get_option( 'seo_nlc_geo_lng', '' );
	$opening_hours = get_option( 'seo_nlc_opening_hours', '' );
	$cities        = get_option( 'seo_nlc_cities', '' );

	// Social media.
	$facebook  = get_option( 'seo_nlc_social_facebook', '' );
	$linkedin  = get_option( 'seo_nlc_social_linkedin', '' );
	$instagram = get_option( 'seo_nlc_social_instagram', '' );

	// Podstawowa struktura.
	$schema = array(
		'@type' => $business_type,
		'@id'   => home_url( '/#organization' ),
		'name'  => $company_name,
		'url'   => home_url( '/' ),
	);

	// Logo.
	if ( $logo_id ) {
		$logo_url = wp_get_attachment_url( $logo_id );
		if ( $logo_url ) {
			$schema['image'] = $logo_url;
			$schema['logo']  = array(
				'@type'      => 'ImageObject',
				'@id'        => home_url( '/#logo' ),
				'url'        => $logo_url,
				'contentUrl' => $logo_url,
				'caption'    => $company_name,
			);
		}
	}

	// Telefon.
	if ( ! empty( $phone ) ) {
		$schema['telephone'] = $phone;
	}

	// Email.
	if ( ! empty( $email ) ) {
		$schema['email'] = $email;
	}

	// Adres.
	if ( ! empty( $street ) || ! empty( $city ) ) {
		$address = array(
			'@type'          => 'PostalAddress',
			'addressCountry' => 'PL',
		);

		if ( ! empty( $street ) ) {
			$address['streetAddress'] = $street;
		}
		if ( ! empty( $city ) ) {
			$address['addressLocality'] = $city;
		}
		if ( ! empty( $region ) ) {
			$address['addressRegion'] = $region;
		}
		if ( ! empty( $postal_code ) ) {
			$address['postalCode'] = $postal_code;
		}

		$schema['address'] = $address;
	}

	// Współrzędne GPS.
	if ( ! empty( $lat ) && ! empty( $lng ) ) {
		$schema['geo'] = array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => (float) $lat,
			'longitude' => (float) $lng,
		);
	}

	// Obszar działania (miasta).
	if ( ! empty( $cities ) ) {
		$cities_array = array_filter( array_map( 'trim', explode( "\n", $cities ) ) );

		if ( ! empty( $cities_array ) ) {
			$area_served = array();

			foreach ( $cities_array as $city_name ) {
				$area_served[] = array(
					'@type' => 'City',
					'name'  => $city_name,
				);
			}

			$schema['areaServed'] = $area_served;
		}
	}

	// Godziny otwarcia.
	if ( ! empty( $opening_hours ) ) {
		$hours_data = json_decode( $opening_hours, true );

		if ( is_array( $hours_data ) && ! empty( $hours_data ) ) {
			$opening_specs = array();

			foreach ( $hours_data as $day => $day_data ) {
				if ( ! empty( $day_data['open'] ) ) {
					$opening_specs[] = array(
						'@type'     => 'OpeningHoursSpecification',
						'dayOfWeek' => $day,
						'opens'     => $day_data['opens'] ?? '08:00',
						'closes'    => $day_data['closes'] ?? '16:00',
					);
				}
			}

			if ( ! empty( $opening_specs ) ) {
				$schema['openingHoursSpecification'] = $opening_specs;
			}
		}
	}

	// Social media - sameAs.
	$same_as = array();
	if ( ! empty( $facebook ) ) {
		$same_as[] = $facebook;
	}
	if ( ! empty( $linkedin ) ) {
		$same_as[] = $linkedin;
	}
	if ( ! empty( $instagram ) ) {
		$same_as[] = $instagram;
	}

	if ( ! empty( $same_as ) ) {
		$schema['sameAs'] = $same_as;
	}

	/**
	 * Filtruje schema LocalBusiness.
	 *
	 * @since 1.0.0
	 * @param array $schema Schema LocalBusiness.
	 */
	return apply_filters( 'seo_nlc_local_business_schema', $schema );
}
