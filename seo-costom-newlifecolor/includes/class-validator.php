<?php
/**
 * Klasa walidacji i sanityzacji danych.
 *
 * Odpowiada za walidację i czyszczenie wszystkich danych wejściowych pluginu.
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
 * Klasa Validator.
 *
 * Zapewnia metody do walidacji i sanityzacji danych wejściowych.
 *
 * @since 1.0.0
 */
class Validator {

	/**
	 * Maksymalna długość tytułu SEO.
	 *
	 * @var int
	 */
	public const MAX_TITLE_LENGTH = 60;

	/**
	 * Maksymalna długość opisu meta.
	 *
	 * @var int
	 */
	public const MAX_DESCRIPTION_LENGTH = 160;

	/**
	 * Wyrażenie regularne dla polskich numerów telefonu.
	 *
	 * @var string
	 */
	private const PHONE_PATTERN_PL = '/^(?:\+48\s?)?(?:\d{3}[\s-]?\d{3}[\s-]?\d{3}|\d{2}[\s-]?\d{3}[\s-]?\d{2}[\s-]?\d{2})$/';

	/**
	 * Konstruktor klasy.
	 */
	public function __construct() {}

	/**
	 * Waliduje adres email.
	 *
	 * @param string $email Adres email do walidacji.
	 * @return bool True jeśli email jest poprawny.
	 */
	public function validate_email( string $email ): bool {
		$email = trim( $email );

		if ( empty( $email ) ) {
			return false;
		}

		return (bool) is_email( $email );
	}

	/**
	 * Sanityzuje adres email.
	 *
	 * @param string $email Adres email do sanityzacji.
	 * @return string Oczyszczony email lub pusty string.
	 */
	public function sanitize_email( string $email ): string {
		$email = sanitize_email( $email );

		return $this->validate_email( $email ) ? $email : '';
	}

	/**
	 * Waliduje URL.
	 *
	 * @param string $url     URL do walidacji.
	 * @param array  $schemes Dozwolone schematy (domyślnie http, https).
	 * @return bool True jeśli URL jest poprawny.
	 */
	public function validate_url( string $url, array $schemes = array( 'http', 'https' ) ): bool {
		$url = trim( $url );

		if ( empty( $url ) ) {
			return false;
		}

		$sanitized = esc_url_raw( $url, $schemes );

		return ! empty( $sanitized ) && filter_var( $sanitized, FILTER_VALIDATE_URL ) !== false;
	}

	/**
	 * Sanityzuje URL.
	 *
	 * @param string $url     URL do sanityzacji.
	 * @param array  $schemes Dozwolone schematy.
	 * @return string Oczyszczony URL lub pusty string.
	 */
	public function sanitize_url( string $url, array $schemes = array( 'http', 'https' ) ): string {
		$url = trim( $url );

		if ( empty( $url ) ) {
			return '';
		}

		// Dodaj https:// jeśli brak schematu.
		if ( ! preg_match( '/^https?:\/\//i', $url ) ) {
			$url = 'https://' . $url;
		}

		return esc_url_raw( $url, $schemes );
	}

	/**
	 * Waliduje polski numer telefonu.
	 *
	 * Akceptuje formaty:
	 * - +48 123 456 789
	 * - 123 456 789
	 * - 123-456-789
	 * - 12 345 67 89
	 *
	 * @param string $phone Numer telefonu do walidacji.
	 * @return bool True jeśli numer jest poprawny.
	 */
	public function validate_phone( string $phone ): bool {
		$phone = trim( $phone );

		if ( empty( $phone ) ) {
			return false;
		}

		// Usuń spacje, myślniki, nawiasy dla walidacji.
		$phone_clean = preg_replace( '/[\s\-\(\)]/u', '', $phone );

		// Sprawdź czy to numer polski.
		if ( preg_match( '/^\+?48?\d{9}$/', $phone_clean ) ) {
			return true;
		}

		// Sprawdź oryginalny format.
		return (bool) preg_match( self::PHONE_PATTERN_PL, $phone );
	}

	/**
	 * Sanityzuje numer telefonu.
	 *
	 * @param string $phone Numer telefonu do sanityzacji.
	 * @return string Oczyszczony numer telefonu.
	 */
	public function sanitize_phone( string $phone ): string {
		$phone = sanitize_text_field( $phone );

		// Zachowaj tylko cyfry, plus, spacje i myślniki.
		$phone = preg_replace( '/[^\d\+\s\-]/u', '', $phone );

		return trim( $phone );
	}

	/**
	 * Sanityzuje tytuł SEO.
	 *
	 * @param string $title Tytuł do sanityzacji.
	 * @return string Oczyszczony tytuł (max 60 znaków).
	 */
	public function sanitize_meta_title( string $title ): string {
		$title = sanitize_text_field( $title );
		$title = wp_strip_all_tags( $title );

		// Ogranicz do maksymalnej długości.
		if ( mb_strlen( $title ) > self::MAX_TITLE_LENGTH ) {
			$title = mb_substr( $title, 0, self::MAX_TITLE_LENGTH );
		}

		return trim( $title );
	}

	/**
	 * Sanityzuje opis meta.
	 *
	 * @param string $description Opis do sanityzacji.
	 * @return string Oczyszczony opis (max 160 znaków).
	 */
	public function sanitize_meta_description( string $description ): string {
		$description = sanitize_textarea_field( $description );
		$description = wp_strip_all_tags( $description );

		// Zamień wielokrotne spacje na pojedyncze.
		$description = preg_replace( '/\s+/u', ' ', $description );

		// Ogranicz do maksymalnej długości.
		if ( mb_strlen( $description ) > self::MAX_DESCRIPTION_LENGTH ) {
			$description = mb_substr( $description, 0, self::MAX_DESCRIPTION_LENGTH );
		}

		return trim( $description );
	}

	/**
	 * Waliduje JSON.
	 *
	 * @param string $json String JSON do walidacji.
	 * @return bool True jeśli JSON jest poprawny.
	 */
	public function validate_json( string $json ): bool {
		$json = trim( $json );

		if ( empty( $json ) ) {
			return true; // Pusty string jest akceptowalny.
		}

		json_decode( $json );

		return json_last_error() === JSON_ERROR_NONE;
	}

	/**
	 * Sanityzuje i parsuje JSON.
	 *
	 * @param string $json String JSON do sanityzacji.
	 * @return array Sparsowany JSON jako tablica lub pusta tablica.
	 */
	public function sanitize_json( string $json ): array {
		$json = trim( $json );

		if ( empty( $json ) ) {
			return array();
		}

		$decoded = json_decode( $json, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return array();
		}

		return is_array( $decoded ) ? $decoded : array();
	}

	/**
	 * Waliduje współrzędne geograficzne.
	 *
	 * @param string|float $lat Szerokość geograficzna.
	 * @param string|float $lng Długość geograficzna.
	 * @return bool True jeśli współrzędne są poprawne.
	 */
	public function validate_coordinates( string|float $lat, string|float $lng ): bool {
		$lat = (float) $lat;
		$lng = (float) $lng;

		// Szerokość: -90 do 90.
		if ( $lat < -90 || $lat > 90 ) {
			return false;
		}

		// Długość: -180 do 180.
		if ( $lng < -180 || $lng > 180 ) {
			return false;
		}

		return true;
	}

	/**
	 * Sanityzuje współrzędną geograficzną.
	 *
	 * @param string $coordinate Współrzędna do sanityzacji.
	 * @param string $type       Typ: 'lat' lub 'lng'.
	 * @return string Oczyszczona współrzędna.
	 */
	public function sanitize_coordinate( string $coordinate, string $type = 'lat' ): string {
		$coordinate = trim( $coordinate );

		// Zamień przecinek na kropkę (europejski format).
		$coordinate = str_replace( ',', '.', $coordinate );

		// Zachowaj tylko cyfry, minus i kropkę.
		$coordinate = preg_replace( '/[^\d\.\-]/u', '', $coordinate );

		$value = (float) $coordinate;

		// Walidacja zakresów.
		if ( 'lat' === $type ) {
			$value = max( -90, min( 90, $value ) );
		} else {
			$value = max( -180, min( 180, $value ) );
		}

		return (string) $value;
	}

	/**
	 * Sanityzuje kod pocztowy (format polski XX-XXX).
	 *
	 * @param string $postal_code Kod pocztowy do sanityzacji.
	 * @return string Oczyszczony kod pocztowy.
	 */
	public function sanitize_postal_code( string $postal_code ): string {
		$postal_code = sanitize_text_field( $postal_code );

		// Usuń wszystko poza cyframi.
		$digits = preg_replace( '/\D/', '', $postal_code );

		// Jeśli mamy 5 cyfr, sformatuj jako XX-XXX.
		if ( strlen( $digits ) === 5 ) {
			return substr( $digits, 0, 2 ) . '-' . substr( $digits, 2 );
		}

		return $postal_code;
	}

	/**
	 * Waliduje polski kod pocztowy.
	 *
	 * @param string $postal_code Kod pocztowy do walidacji.
	 * @return bool True jeśli kod jest poprawny.
	 */
	public function validate_postal_code( string $postal_code ): bool {
		$postal_code = trim( $postal_code );

		return (bool) preg_match( '/^\d{2}-\d{3}$/', $postal_code );
	}

	/**
	 * Sanityzuje tekst dla atrybutu HTML.
	 *
	 * @param string $text Tekst do sanityzacji.
	 * @return string Oczyszczony tekst.
	 */
	public function sanitize_attribute( string $text ): string {
		return esc_attr( sanitize_text_field( $text ) );
	}

	/**
	 * Sanityzuje tekst wieloliniowy.
	 *
	 * @param string $text Tekst do sanityzacji.
	 * @return string Oczyszczony tekst.
	 */
	public function sanitize_textarea( string $text ): string {
		return sanitize_textarea_field( $text );
	}

	/**
	 * Sanityzuje identyfikator numeryczny.
	 *
	 * @param mixed $id Wartość do sanityzacji.
	 * @return int Oczyszczony identyfikator.
	 */
	public function sanitize_id( mixed $id ): int {
		return absint( $id );
	}

	/**
	 * Sanityzuje tablicę usług.
	 *
	 * @param array|null $services Tablica usług do sanityzacji.
	 * @return array Oczyszczona tablica usług.
	 */
	public function sanitize_services( ?array $services ): array {
		if ( $services === null ) {
			return array();
		}

		$sanitized = array();

		foreach ( $services as $index => $service ) {
			if ( ! is_array( $service ) ) {
				continue;
			}

			$sanitized_service = array(
				'name'        => isset( $service['name'] )
					? sanitize_text_field( $service['name'] )
					: '',
				'description' => isset( $service['description'] )
					? sanitize_textarea_field( $service['description'] )
					: '',
				'active'      => isset( $service['active'] )
					? (bool) $service['active']
					: true,
			);

			// Dodaj tylko jeśli ma nazwę.
			if ( ! empty( $sanitized_service['name'] ) ) {
				$sanitized[] = $sanitized_service;
			}
		}

		return $sanitized;
	}

	/**
	 * Sanityzuje tablicę FAQ.
	 *
	 * @param array|null $faq_items Tablica FAQ do sanityzacji.
	 * @return array Oczyszczona tablica FAQ.
	 */
	public function sanitize_faq_items( ?array $faq_items ): array {
		if ( $faq_items === null ) {
			return array();
		}

		$sanitized = array();

		foreach ( $faq_items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$question = isset( $item['question'] )
				? sanitize_text_field( $item['question'] )
				: '';

			$answer = isset( $item['answer'] )
				? wp_kses_post( $item['answer'] )
				: '';

			// Dodaj tylko jeśli ma pytanie i odpowiedź.
			if ( ! empty( $question ) && ! empty( $answer ) ) {
				$sanitized[] = array(
					'question' => $question,
					'answer'   => $answer,
				);
			}
		}

		return $sanitized;
	}

	/**
	 * Waliduje i parsuje URL YouTube.
	 *
	 * @param string $url URL YouTube.
	 * @return array|false Dane wideo lub false.
	 */
	public function parse_youtube_url( string $url ): array|false {
		$url = trim( $url );

		// Wzorce YouTube.
		$patterns = array(
			'/youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/',
			'/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/',
			'/youtu\.be\/([a-zA-Z0-9_-]{11})/',
			'/youtube\.com\/v\/([a-zA-Z0-9_-]{11})/',
		);

		foreach ( $patterns as $pattern ) {
			if ( preg_match( $pattern, $url, $matches ) ) {
				$video_id = $matches[1];

				return array(
					'id'         => $video_id,
					'url'        => 'https://www.youtube.com/watch?v=' . $video_id,
					'embed_url'  => 'https://www.youtube.com/embed/' . $video_id,
					'thumbnail'  => 'https://img.youtube.com/vi/' . $video_id . '/maxresdefault.jpg',
					'provider'   => 'YouTube',
				);
			}
		}

		return false;
	}

	/**
	 * Waliduje i parsuje URL Vimeo.
	 *
	 * @param string $url URL Vimeo.
	 * @return array|false Dane wideo lub false.
	 */
	public function parse_vimeo_url( string $url ): array|false {
		$url = trim( $url );

		// Wzorce Vimeo.
		$patterns = array(
			'/vimeo\.com\/(\d+)/',
			'/player\.vimeo\.com\/video\/(\d+)/',
		);

		foreach ( $patterns as $pattern ) {
			if ( preg_match( $pattern, $url, $matches ) ) {
				$video_id = $matches[1];

				return array(
					'id'         => $video_id,
					'url'        => 'https://vimeo.com/' . $video_id,
					'embed_url'  => 'https://player.vimeo.com/video/' . $video_id,
					'thumbnail'  => '', // Vimeo wymaga API dla thumbnail.
					'provider'   => 'Vimeo',
				);
			}
		}

		return false;
	}

	/**
	 * Parsuje URL wideo (YouTube lub Vimeo).
	 *
	 * @param string $url URL wideo.
	 * @return array|false Dane wideo lub false.
	 */
	public function parse_video_url( string $url ): array|false {
		$youtube = $this->parse_youtube_url( $url );
		if ( $youtube ) {
			return $youtube;
		}

		$vimeo = $this->parse_vimeo_url( $url );
		if ( $vimeo ) {
			return $vimeo;
		}

		return false;
	}

	/**
	 * Sanityzuje listę miast (jedna linia = jedno miasto).
	 *
	 * @param string $cities Lista miast.
	 * @return string Oczyszczona lista miast.
	 */
	public function sanitize_cities_list( string $cities ): string {
		$lines = explode( "\n", $cities );
		$sanitized = array();

		foreach ( $lines as $line ) {
			$city = trim( sanitize_text_field( $line ) );
			if ( ! empty( $city ) ) {
				$sanitized[] = $city;
			}
		}

		return implode( "\n", $sanitized );
	}

	/**
	 * Pobiera listę miast jako tablicę.
	 *
	 * @param string $cities Lista miast (jedna linia = jedno miasto).
	 * @return array Tablica miast.
	 */
	public function get_cities_array( string $cities ): array {
		$cities = $this->sanitize_cities_list( $cities );

		if ( empty( $cities ) ) {
			return array();
		}

		return array_filter( array_map( 'trim', explode( "\n", $cities ) ) );
	}

	/**
	 * Waliduje select option.
	 *
	 * @param string $value   Wartość do walidacji.
	 * @param array  $allowed Dozwolone wartości.
	 * @param string $default Wartość domyślna.
	 * @return string Zwalidowana wartość.
	 */
	public function validate_select( string $value, array $allowed, string $default = '' ): string {
		return in_array( $value, $allowed, true ) ? $value : $default;
	}

	/**
	 * Sanityzuje checkbox.
	 *
	 * @param mixed $value Wartość checkboxa.
	 * @return bool True jeśli zaznaczony.
	 */
	public function sanitize_checkbox( mixed $value ): bool {
		return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
	}
}
