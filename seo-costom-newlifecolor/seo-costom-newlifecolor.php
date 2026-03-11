<?php
/**
 * Plugin Name:       SEO Custom NewLifeColor
 * Plugin URI:        https://newlifecolor.pl/
 * Description:       Zaawansowany plugin SEO z pełnym wsparciem Schema.org dla usług lokalnych. Generuje meta tagi, Open Graph, Twitter Cards oraz strukturalne dane JSON-LD.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Marek Becht
 * Author URI:        https://newlifecolor.pl/
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       seo-costom-newlifecolor
 * Domain Path:       /languages
 *
 * @package SeoCustomNLC
 */

declare(strict_types=1);

namespace SeoCustomNLC;

// Zabezpieczenie przed bezpośrednim dostępem.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Aktualna wersja pluginu.
 */
define( 'SEO_NLC_VERSION', '1.0.0' );

/**
 * Ścieżka do katalogu pluginu.
 */
define( 'SEO_NLC_PATH', plugin_dir_path( __FILE__ ) );

/**
 * URL katalogu pluginu.
 */
define( 'SEO_NLC_URL', plugin_dir_url( __FILE__ ) );

/**
 * Nazwa pliku pluginu.
 */
define( 'SEO_NLC_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Minimalna wymagana wersja PHP.
 */
define( 'SEO_NLC_MIN_PHP', '8.0' );

/**
 * Minimalna wymagana wersja WordPress.
 */
define( 'SEO_NLC_MIN_WP', '6.0' );

/**
 * Sprawdza wymagania systemowe.
 *
 * @return bool True jeśli wymagania są spełnione.
 */
function seo_nlc_check_requirements(): bool {
	$php_version_ok = version_compare( PHP_VERSION, SEO_NLC_MIN_PHP, '>=' );
	$wp_version_ok  = version_compare( get_bloginfo( 'version' ), SEO_NLC_MIN_WP, '>=' );

	return $php_version_ok && $wp_version_ok;
}

/**
 * Wyświetla komunikat o błędzie wymagań.
 *
 * @return void
 */
function seo_nlc_requirements_notice(): void {
	$message = sprintf(
		/* translators: 1: Minimalna wersja PHP, 2: Minimalna wersja WordPress */
		esc_html__(
			'SEO Custom NewLifeColor wymaga PHP %1$s+ i WordPress %2$s+. Zaktualizuj swoje środowisko.',
			'seo-costom-newlifecolor'
		),
		SEO_NLC_MIN_PHP,
		SEO_NLC_MIN_WP
	);

	printf(
		'<div class="notice notice-error"><p>%s</p></div>',
		esc_html( $message )
	);
}

/**
 * Autoloader klas pluginu.
 *
 * @param string $class_name Pełna nazwa klasy z namespace.
 * @return void
 */
function seo_nlc_autoloader( string $class_name ): void {
	// Sprawdź czy klasa należy do naszego namespace.
	$namespace = 'SeoCustomNLC\\';
	if ( strpos( $class_name, $namespace ) !== 0 ) {
		return;
	}

	// Usuń namespace prefix.
	$relative_class = substr( $class_name, strlen( $namespace ) );

	// Zamień separatory namespace na separatory katalogów.
	$relative_class = strtolower( str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class ) );

	// Zamień podkreślenia na myślniki (WordPress style).
	$relative_class = str_replace( '_', '-', $relative_class );

	// Mapowanie katalogów.
	$directories = array(
		'includes',
		'admin',
		'public',
		'schemas',
	);

	foreach ( $directories as $dir ) {
		$file = SEO_NLC_PATH . $dir . DIRECTORY_SEPARATOR . 'class-' . $relative_class . '.php';

		if ( file_exists( $file ) ) {
			require_once $file;
			return;
		}
	}

	// Próba bezpośredniego ładowania (dla klas bez prefixu 'class-').
	foreach ( $directories as $dir ) {
		$file = SEO_NLC_PATH . $dir . DIRECTORY_SEPARATOR . $relative_class . '.php';

		if ( file_exists( $file ) ) {
			require_once $file;
			return;
		}
	}
}

// Rejestracja autoloadera.
spl_autoload_register( __NAMESPACE__ . '\\seo_nlc_autoloader' );

/**
 * Inicjalizacja pluginu.
 *
 * @return void
 */
function seo_nlc_init(): void {
	// Sprawdź wymagania.
	if ( ! seo_nlc_check_requirements() ) {
		add_action( 'admin_notices', __NAMESPACE__ . '\\seo_nlc_requirements_notice' );
		return;
	}

	// Załaduj text domain.
	load_plugin_textdomain(
		'seo-costom-newlifecolor',
		false,
		dirname( SEO_NLC_BASENAME ) . '/languages'
	);

	// Uruchom główną klasę pluginu.
	$plugin = Seo_Costom_Newlifecolor::get_instance();
	$plugin->run();
}

/**
 * Hook aktywacji pluginu.
 *
 * @return void
 */
function seo_nlc_activate(): void {
	// Sprawdź wymagania przed aktywacją.
	if ( ! seo_nlc_check_requirements() ) {
		deactivate_plugins( SEO_NLC_BASENAME );
		wp_die(
			esc_html__(
				'SEO Custom NewLifeColor wymaga PHP 8.0+ i WordPress 6.0+.',
				'seo-costom-newlifecolor'
			),
			'Plugin Activation Error',
			array( 'back_link' => true )
		);
	}

	// Ustaw domyślne opcje.
	$default_options = array(
		'seo_nlc_title_separator'      => '|',
		'seo_nlc_title_format'         => '{{title}} {{separator}} {{site_name}}',
		'seo_nlc_radius'               => 50,
		'seo_nlc_services'             => array(),
		'seo_nlc_cities'               => '',
		'seo_nlc_opening_hours'        => '',
	);

	foreach ( $default_options as $option_name => $default_value ) {
		if ( get_option( $option_name ) === false ) {
			add_option( $option_name, $default_value );
		}
	}

	// Wyczyść cache rewrite rules.
	flush_rewrite_rules();
}

/**
 * Hook deaktywacji pluginu.
 *
 * @return void
 */
function seo_nlc_deactivate(): void {
	// Wyczyść scheduled events jeśli jakieś są.
	wp_clear_scheduled_hook( 'seo_nlc_daily_cleanup' );

	// Wyczyść cache rewrite rules.
	flush_rewrite_rules();
}

/**
 * Hook odinstalowania pluginu.
 * Uwaga: Ta funkcja jest wywoływana przez uninstall.php lub bezpośrednio.
 *
 * @return void
 */
function seo_nlc_uninstall(): void {
	// Usuń wszystkie opcje pluginu.
	$options = array(
		'seo_nlc_company_name',
		'seo_nlc_business_type',
		'seo_nlc_street',
		'seo_nlc_city',
		'seo_nlc_postal_code',
		'seo_nlc_region',
		'seo_nlc_phone',
		'seo_nlc_email',
		'seo_nlc_opening_hours',
		'seo_nlc_logo',
		'seo_nlc_social_facebook',
		'seo_nlc_social_linkedin',
		'seo_nlc_social_instagram',
		'seo_nlc_cities',
		'seo_nlc_radius',
		'seo_nlc_geo_lat',
		'seo_nlc_geo_lng',
		'seo_nlc_services',
		'seo_nlc_title_separator',
		'seo_nlc_title_format',
		'seo_nlc_default_description',
		'seo_nlc_default_og_image',
	);

	foreach ( $options as $option ) {
		delete_option( $option );
	}

	// Usuń wszystkie post meta.
	global $wpdb;

	$meta_keys = array(
		'_seo_nlc_title',
		'_seo_nlc_description',
		'_seo_nlc_focus_keyword',
		'_seo_nlc_canonical_url',
		'_seo_nlc_schema_type',
		'_seo_nlc_auto_images',
		'_seo_nlc_auto_videos',
		'_seo_nlc_service_id',
		'_seo_nlc_video_urls',
		'_seo_nlc_faq_items',
		'_seo_nlc_og_title',
		'_seo_nlc_og_description',
		'_seo_nlc_og_image',
		'_seo_nlc_twitter_card',
		'_seo_nlc_twitter_title',
		'_seo_nlc_twitter_description',
		'_seo_nlc_twitter_image',
	);

	foreach ( $meta_keys as $meta_key ) {
		$wpdb->delete(
			$wpdb->postmeta,
			array( 'meta_key' => $meta_key ),
			array( '%s' )
		);
	}
}

// Rejestracja hooków aktywacji/deaktywacji.
register_activation_hook( __FILE__, __NAMESPACE__ . '\\seo_nlc_activate' );
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\\seo_nlc_deactivate' );

// Inicjalizacja pluginu po załadowaniu WordPress.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\seo_nlc_init' );
