<?php
/**
 * Główna klasa pluginu SEO Custom NewLifeColor.
 *
 * Orkiestruje wszystkie komponenty pluginu i zarządza ich inicjalizacją.
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
 * Główna klasa pluginu.
 *
 * Implementuje wzorzec Singleton dla zapewnienia jednej instancji.
 * Zarządza inicjalizacją komponentów Admin, Frontend i Schema Generator.
 *
 * @since 1.0.0
 */
class Seo_Costom_Newlifecolor {

	/**
	 * Instancja klasy (Singleton).
	 *
	 * @var Seo_Costom_Newlifecolor|null
	 */
	private static ?Seo_Costom_Newlifecolor $instance = null;

	/**
	 * Instancja klasy Admin.
	 *
	 * @var Admin|null
	 */
	private ?Admin $admin = null;

	/**
	 * Instancja klasy Meta Box.
	 *
	 * @var Meta_Box|null
	 */
	private ?Meta_Box $meta_box = null;

	/**
	 * Instancja klasy Frontend.
	 *
	 * @var Frontend|null
	 */
	private ?Frontend $frontend = null;

	/**
	 * Instancja klasy Schema Generator.
	 *
	 * @var Schema_Generator|null
	 */
	private ?Schema_Generator $schema_generator = null;

	/**
	 * Instancja klasy Meta Tags.
	 *
	 * @var Meta_Tags|null
	 */
	private ?Meta_Tags $meta_tags = null;

	/**
	 * Instancja klasy Validator.
	 *
	 * @var Validator|null
	 */
	private ?Validator $validator = null;

	/**
	 * Prywatny konstruktor (Singleton).
	 */
	private function __construct() {
		// Inicjalizacja komponentów.
		$this->load_dependencies();
	}

	/**
	 * Zapobiega klonowaniu instancji (Singleton).
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Zapobiega deserializacji instancji (Singleton).
	 *
	 * @return void
	 * @throws \Exception Gdy próbowano deserializować.
	 */
	public function __wakeup() {
		throw new \Exception( 'Cannot unserialize a singleton.' );
	}

	/**
	 * Zwraca instancję klasy (Singleton).
	 *
	 * @return Seo_Costom_Newlifecolor Instancja klasy.
	 */
	public static function get_instance(): Seo_Costom_Newlifecolor {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Ładuje zależności pluginu.
	 *
	 * @return void
	 */
	private function load_dependencies(): void {
		// Klasa Validator jest podstawą dla innych.
		$this->validator = new Validator();

		// Schema Generator potrzebuje być dostępny dla Frontend.
		$this->schema_generator = new Schema_Generator();

		// Meta Tags generator.
		$this->meta_tags = new Meta_Tags();
	}

	/**
	 * Uruchamia plugin.
	 *
	 * Rejestruje wszystkie hooki i filtry WordPress.
	 *
	 * @return void
	 */
	public function run(): void {
		// Inicjalizacja komponentów admin.
		if ( is_admin() ) {
			$this->init_admin();
		}

		// Inicjalizacja komponentów frontend.
		if ( ! is_admin() || wp_doing_ajax() ) {
			$this->init_frontend();
		}

		// Rejestracja wspólnych hooków.
		$this->register_common_hooks();
	}

	/**
	 * Inicjalizuje komponenty panelu administracyjnego.
	 *
	 * @return void
	 */
	private function init_admin(): void {
		$this->admin    = new Admin( $this->validator );
		$this->meta_box = new Meta_Box( $this->validator );

		// Rejestracja hooków Admin.
		add_action( 'admin_menu', array( $this->admin, 'add_menu_page' ) );
		add_action( 'admin_init', array( $this->admin, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this->admin, 'enqueue_assets' ) );

		// Rejestracja hooków Meta Box.
		add_action( 'add_meta_boxes', array( $this->meta_box, 'add_meta_box' ) );
		add_action( 'save_post', array( $this->meta_box, 'save_meta_box' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this->meta_box, 'enqueue_assets' ) );

		// Dodaj link do ustawień na liście pluginów.
		add_filter(
			'plugin_action_links_' . SEO_NLC_BASENAME,
			array( $this, 'add_settings_link' )
		);
	}

	/**
	 * Inicjalizuje komponenty frontendowe.
	 *
	 * @return void
	 */
	private function init_frontend(): void {
		$this->frontend = new Frontend(
			$this->meta_tags,
			$this->schema_generator
		);

		// Rejestracja hooków Frontend - wysoki priorytet (1) dla SEO.
		add_action( 'wp_head', array( $this->frontend, 'output_meta_tags' ), 1 );
		add_action( 'wp_head', array( $this->frontend, 'output_schema' ), 2 );

		// Usuń domyślne tagi WordPress które zastępujemy.
		remove_action( 'wp_head', 'wp_generator' );

		// Filtr dla tytułu strony.
		add_filter( 'pre_get_document_title', array( $this->frontend, 'filter_document_title' ), 10 );
		add_filter( 'wp_title', array( $this->frontend, 'filter_wp_title' ), 10, 2 );
	}

	/**
	 * Rejestruje wspólne hooki dla admin i frontend.
	 *
	 * @return void
	 */
	private function register_common_hooks(): void {
		// Rejestracja filtrów dla deweloperów.
		add_filter( 'seo_nlc_meta_title', array( $this, 'apply_title_filters' ), 10, 2 );
		add_filter( 'seo_nlc_meta_description', array( $this, 'apply_description_filters' ), 10, 2 );
		add_filter( 'seo_nlc_schema_output', array( $this, 'apply_schema_filters' ), 10, 2 );
		add_filter( 'seo_nlc_og_tags', array( $this, 'apply_og_filters' ), 10, 2 );
	}

	/**
	 * Dodaje link do ustawień na liście pluginów.
	 *
	 * @param array $links Istniejące linki.
	 * @return array Zmodyfikowane linki.
	 */
	public function add_settings_link( array $links ): array {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'options-general.php?page=seo-nlc-settings' ) ),
			esc_html__( 'Ustawienia', 'seo-costom-newlifecolor' )
		);

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Filtr dla tytułu SEO.
	 *
	 * @param string $title   Tytuł.
	 * @param int    $post_id ID posta.
	 * @return string Przefiltrowany tytuł.
	 */
	public function apply_title_filters( string $title, int $post_id ): string {
		/**
		 * Filtruje tytuł SEO.
		 *
		 * @since 1.0.0
		 * @param string $title   Tytuł SEO.
		 * @param int    $post_id ID posta.
		 */
		return apply_filters( 'seo_nlc_title_output', $title, $post_id );
	}

	/**
	 * Filtr dla opisu SEO.
	 *
	 * @param string $description Opis.
	 * @param int    $post_id     ID posta.
	 * @return string Przefiltrowany opis.
	 */
	public function apply_description_filters( string $description, int $post_id ): string {
		/**
		 * Filtruje opis SEO.
		 *
		 * @since 1.0.0
		 * @param string $description Opis SEO.
		 * @param int    $post_id     ID posta.
		 */
		return apply_filters( 'seo_nlc_description_output', $description, $post_id );
	}

	/**
	 * Filtr dla schema JSON-LD.
	 *
	 * @param array $schema  Dane schema.
	 * @param int   $post_id ID posta.
	 * @return array Przefiltrowane dane schema.
	 */
	public function apply_schema_filters( array $schema, int $post_id ): array {
		/**
		 * Filtruje dane Schema.org JSON-LD.
		 *
		 * @since 1.0.0
		 * @param array $schema  Dane schema.
		 * @param int   $post_id ID posta.
		 */
		return apply_filters( 'seo_nlc_schema_json', $schema, $post_id );
	}

	/**
	 * Filtr dla tagów Open Graph.
	 *
	 * @param array $og_tags Tagi OG.
	 * @param int   $post_id ID posta.
	 * @return array Przefiltrowane tagi OG.
	 */
	public function apply_og_filters( array $og_tags, int $post_id ): array {
		/**
		 * Filtruje tagi Open Graph.
		 *
		 * @since 1.0.0
		 * @param array $og_tags Tagi Open Graph.
		 * @param int   $post_id ID posta.
		 */
		return apply_filters( 'seo_nlc_og_output', $og_tags, $post_id );
	}

	/**
	 * Zwraca instancję Validator.
	 *
	 * @return Validator Instancja walidatora.
	 */
	public function get_validator(): Validator {
		return $this->validator;
	}

	/**
	 * Zwraca instancję Schema Generator.
	 *
	 * @return Schema_Generator Instancja generatora schema.
	 */
	public function get_schema_generator(): Schema_Generator {
		return $this->schema_generator;
	}

	/**
	 * Zwraca instancję Meta Tags.
	 *
	 * @return Meta_Tags Instancja generatora meta tagów.
	 */
	public function get_meta_tags(): Meta_Tags {
		return $this->meta_tags;
	}

	/**
	 * Pobiera opcję pluginu z domyślną wartością.
	 *
	 * @param string $option_name Nazwa opcji (bez prefiksu seo_nlc_).
	 * @param mixed  $default     Wartość domyślna.
	 * @return mixed Wartość opcji.
	 */
	public static function get_option( string $option_name, mixed $default = '' ): mixed {
		$full_name = 'seo_nlc_' . $option_name;
		return get_option( $full_name, $default );
	}

	/**
	 * Pobiera meta posta z domyślną wartością.
	 *
	 * @param int    $post_id   ID posta.
	 * @param string $meta_key  Klucz meta (bez prefiksu _seo_nlc_).
	 * @param mixed  $default   Wartość domyślna.
	 * @return mixed Wartość meta.
	 */
	public static function get_post_meta( int $post_id, string $meta_key, mixed $default = '' ): mixed {
		$full_key = '_seo_nlc_' . $meta_key;
		$value    = get_post_meta( $post_id, $full_key, true );

		return ( '' !== $value && false !== $value ) ? $value : $default;
	}

	/**
	 * Zapisuje meta posta.
	 *
	 * @param int    $post_id  ID posta.
	 * @param string $meta_key Klucz meta (bez prefiksu _seo_nlc_).
	 * @param mixed  $value    Wartość do zapisania.
	 * @return bool|int Meta ID lub false.
	 */
	public static function update_post_meta( int $post_id, string $meta_key, mixed $value ): bool|int {
		$full_key = '_seo_nlc_' . $meta_key;
		return update_post_meta( $post_id, $full_key, $value );
	}
}
