<?php
/**
 * Klasa Meta Box dla edytora postów/stron.
 *
 * Zarządza meta boxem SEO w edytorze WordPress.
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
 * Klasa Meta_Box.
 *
 * Obsługuje meta box SEO w edytorze postów i stron.
 *
 * @since 1.0.0
 */
class Meta_Box {

	/**
	 * ID meta box.
	 *
	 * @var string
	 */
	public const META_BOX_ID = 'seo_nlc_meta_box';

	/**
	 * Nonce action.
	 *
	 * @var string
	 */
	public const NONCE_ACTION = 'seo_nlc_save_meta_box';

	/**
	 * Nonce field name.
	 *
	 * @var string
	 */
	public const NONCE_FIELD = 'seo_nlc_meta_nonce';

	/**
	 * Instancja walidatora.
	 *
	 * @var Validator
	 */
	private Validator $validator;

	/**
	 * Dozwolone typy Schema.
	 *
	 * @var array
	 */
	private array $schema_types = array(
		'WebPage'      => 'Strona WWW (domyślny)',
		'Service'      => 'Usługa',
		'ImageGallery' => 'Galeria obrazów',
		'VideoGallery' => 'Galeria filmów',
		'Article'      => 'Artykuł',
		'FAQPage'      => 'Strona FAQ',
	);

	/**
	 * Dozwolone typy Twitter Card.
	 *
	 * @var array
	 */
	private array $twitter_card_types = array(
		'summary'             => 'Summary',
		'summary_large_image' => 'Summary z dużym obrazem',
	);

	/**
	 * Konstruktor.
	 *
	 * @param Validator $validator Instancja walidatora.
	 */
	public function __construct( Validator $validator ) {
		$this->validator = $validator;
	}

	/**
	 * Rejestruje meta box.
	 *
	 * @return void
	 */
	public function add_meta_box(): void {
		$post_types = $this->get_supported_post_types();

		foreach ( $post_types as $post_type ) {
			add_meta_box(
				self::META_BOX_ID,
				__( 'SEO Custom NLC', 'seo-costom-newlifecolor' ),
				array( $this, 'render_meta_box' ),
				$post_type,
				'normal',
				'high'
			);
		}
	}

	/**
	 * Pobiera obsługiwane typy postów.
	 *
	 * @return array Lista typów postów.
	 */
	private function get_supported_post_types(): array {
		$post_types = array( 'post', 'page' );

		/**
		 * Filtruje obsługiwane typy postów.
		 *
		 * @since 1.0.0
		 * @param array $post_types Lista typów postów.
		 */
		return apply_filters( 'seo_nlc_supported_post_types', $post_types );
	}

	/**
	 * Renderuje meta box.
	 *
	 * @param \WP_Post $post Obiekt posta.
	 * @return void
	 */
	public function render_meta_box( \WP_Post $post ): void {
		// Pobierz dane meta.
		$meta = $this->get_post_meta( $post->ID );

		// Nonce field.
		wp_nonce_field( self::NONCE_ACTION, self::NONCE_FIELD );

		// Załaduj widok.
		include SEO_NLC_PATH . 'admin/views/meta-box.php';
	}

	/**
	 * Pobiera wszystkie meta posta.
	 *
	 * @param int $post_id ID posta.
	 * @return array Tablica meta.
	 */
	private function get_post_meta( int $post_id ): array {
		return array(
			// SEO.
			'title'         => Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'title', '' ),
			'description'   => Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'description', '' ),
			'focus_keyword' => Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'focus_keyword', '' ),
			'canonical_url' => Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'canonical_url', '' ),

			// Schema.
			'schema_type'   => Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'schema_type', 'WebPage' ),
			'auto_images'   => Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'auto_images', '1' ),
			'auto_videos'   => Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'auto_videos', '1' ),
			'service_id'    => Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'service_id', '' ),
			'video_urls'    => Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'video_urls', '' ),
			'faq_items'     => Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'faq_items', array() ),

			// Social.
			'og_title'            => Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'og_title', '' ),
			'og_description'      => Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'og_description', '' ),
			'og_image'            => Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'og_image', 0 ),
			'twitter_card'        => Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'twitter_card', 'summary_large_image' ),
			'twitter_title'       => Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'twitter_title', '' ),
			'twitter_description' => Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'twitter_description', '' ),
			'twitter_image'       => Seo_Costom_Newlifecolor::get_post_meta( $post_id, 'twitter_image', 0 ),
		);
	}

	/**
	 * Zapisuje meta box.
	 *
	 * @param int      $post_id ID posta.
	 * @param \WP_Post $post    Obiekt posta.
	 * @return void
	 */
	public function save_meta_box( int $post_id, \WP_Post $post ): void {
		// Sprawdź nonce.
		if ( ! isset( $_POST[ self::NONCE_FIELD ] ) ||
			 ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE_FIELD ] ) ), self::NONCE_ACTION )
		) {
			return;
		}

		// Sprawdź autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Sprawdź uprawnienia.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Sprawdź czy to obsługiwany typ posta.
		if ( ! in_array( $post->post_type, $this->get_supported_post_types(), true ) ) {
			return;
		}

		// Zapisz pola SEO.
		$this->save_seo_fields( $post_id );

		// Zapisz pola Schema.
		$this->save_schema_fields( $post_id );

		// Zapisz pola Social.
		$this->save_social_fields( $post_id );
	}

	/**
	 * Zapisuje pola SEO.
	 *
	 * @param int $post_id ID posta.
	 * @return void
	 */
	private function save_seo_fields( int $post_id ): void {
		// SEO Title.
		if ( isset( $_POST['seo_nlc_title'] ) ) {
			$title = $this->validator->sanitize_meta_title(
				sanitize_text_field( wp_unslash( $_POST['seo_nlc_title'] ) )
			);
			Seo_Costom_Newlifecolor::update_post_meta( $post_id, 'title', $title );
		}

		// Meta Description.
		if ( isset( $_POST['seo_nlc_description'] ) ) {
			$description = $this->validator->sanitize_meta_description(
				sanitize_textarea_field( wp_unslash( $_POST['seo_nlc_description'] ) )
			);
			Seo_Costom_Newlifecolor::update_post_meta( $post_id, 'description', $description );
		}

		// Focus Keyword.
		if ( isset( $_POST['seo_nlc_focus_keyword'] ) ) {
			$keyword = sanitize_text_field( wp_unslash( $_POST['seo_nlc_focus_keyword'] ) );
			Seo_Costom_Newlifecolor::update_post_meta( $post_id, 'focus_keyword', $keyword );
		}

		// Canonical URL.
		if ( isset( $_POST['seo_nlc_canonical_url'] ) ) {
			$canonical = $this->validator->sanitize_url(
				sanitize_text_field( wp_unslash( $_POST['seo_nlc_canonical_url'] ) )
			);
			Seo_Costom_Newlifecolor::update_post_meta( $post_id, 'canonical_url', $canonical );
		}
	}

	/**
	 * Zapisuje pola Schema.
	 *
	 * @param int $post_id ID posta.
	 * @return void
	 */
	private function save_schema_fields( int $post_id ): void {
		// Schema Type.
		if ( isset( $_POST['seo_nlc_schema_type'] ) ) {
			$schema_type = $this->validator->validate_select(
				sanitize_text_field( wp_unslash( $_POST['seo_nlc_schema_type'] ) ),
				array_keys( $this->schema_types ),
				'WebPage'
			);
			Seo_Costom_Newlifecolor::update_post_meta( $post_id, 'schema_type', $schema_type );
		}

		// Auto Images.
		$auto_images = isset( $_POST['seo_nlc_auto_images'] ) ? '1' : '0';
		Seo_Costom_Newlifecolor::update_post_meta( $post_id, 'auto_images', $auto_images );

		// Auto Videos.
		$auto_videos = isset( $_POST['seo_nlc_auto_videos'] ) ? '1' : '0';
		Seo_Costom_Newlifecolor::update_post_meta( $post_id, 'auto_videos', $auto_videos );

		// Service ID.
		if ( isset( $_POST['seo_nlc_service_id'] ) ) {
			$service_id = absint( $_POST['seo_nlc_service_id'] );
			Seo_Costom_Newlifecolor::update_post_meta( $post_id, 'service_id', $service_id );
		}

		// Video URLs.
		if ( isset( $_POST['seo_nlc_video_urls'] ) ) {
			$video_urls = sanitize_textarea_field( wp_unslash( $_POST['seo_nlc_video_urls'] ) );
			Seo_Costom_Newlifecolor::update_post_meta( $post_id, 'video_urls', $video_urls );
		}

		// FAQ Items.
		if ( isset( $_POST['seo_nlc_faq'] ) && is_array( $_POST['seo_nlc_faq'] ) ) {
			$faq_items = $this->validator->sanitize_faq_items(
				array_map( 'wp_unslash', $_POST['seo_nlc_faq'] )
			);
			Seo_Costom_Newlifecolor::update_post_meta( $post_id, 'faq_items', $faq_items );
		} else {
			Seo_Costom_Newlifecolor::update_post_meta( $post_id, 'faq_items', array() );
		}
	}

	/**
	 * Zapisuje pola Social.
	 *
	 * @param int $post_id ID posta.
	 * @return void
	 */
	private function save_social_fields( int $post_id ): void {
		// OG Title.
		if ( isset( $_POST['seo_nlc_og_title'] ) ) {
			$og_title = sanitize_text_field( wp_unslash( $_POST['seo_nlc_og_title'] ) );
			Seo_Costom_Newlifecolor::update_post_meta( $post_id, 'og_title', $og_title );
		}

		// OG Description.
		if ( isset( $_POST['seo_nlc_og_description'] ) ) {
			$og_description = sanitize_textarea_field( wp_unslash( $_POST['seo_nlc_og_description'] ) );
			Seo_Costom_Newlifecolor::update_post_meta( $post_id, 'og_description', $og_description );
		}

		// OG Image.
		if ( isset( $_POST['seo_nlc_og_image'] ) ) {
			$og_image = absint( $_POST['seo_nlc_og_image'] );
			Seo_Costom_Newlifecolor::update_post_meta( $post_id, 'og_image', $og_image );
		}

		// Twitter Card Type.
		if ( isset( $_POST['seo_nlc_twitter_card'] ) ) {
			$twitter_card = $this->validator->validate_select(
				sanitize_text_field( wp_unslash( $_POST['seo_nlc_twitter_card'] ) ),
				array_keys( $this->twitter_card_types ),
				'summary_large_image'
			);
			Seo_Costom_Newlifecolor::update_post_meta( $post_id, 'twitter_card', $twitter_card );
		}

		// Twitter Title.
		if ( isset( $_POST['seo_nlc_twitter_title'] ) ) {
			$twitter_title = sanitize_text_field( wp_unslash( $_POST['seo_nlc_twitter_title'] ) );
			Seo_Costom_Newlifecolor::update_post_meta( $post_id, 'twitter_title', $twitter_title );
		}

		// Twitter Description.
		if ( isset( $_POST['seo_nlc_twitter_description'] ) ) {
			$twitter_description = sanitize_textarea_field( wp_unslash( $_POST['seo_nlc_twitter_description'] ) );
			Seo_Costom_Newlifecolor::update_post_meta( $post_id, 'twitter_description', $twitter_description );
		}

		// Twitter Image.
		if ( isset( $_POST['seo_nlc_twitter_image'] ) ) {
			$twitter_image = absint( $_POST['seo_nlc_twitter_image'] );
			Seo_Costom_Newlifecolor::update_post_meta( $post_id, 'twitter_image', $twitter_image );
		}
	}

	/**
	 * Ładuje assety dla meta box.
	 *
	 * @param string $hook_suffix Suffix hooka strony.
	 * @return void
	 */
	public function enqueue_assets( string $hook_suffix ): void {
		// Ładuj tylko na stronach edycji.
		if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		global $post_type;

		// Sprawdź czy to obsługiwany typ posta.
		if ( ! in_array( $post_type, $this->get_supported_post_types(), true ) ) {
			return;
		}

		// Media uploader.
		wp_enqueue_media();

		// Style.
		wp_enqueue_style(
			'seo-nlc-meta-box',
			SEO_NLC_URL . 'admin/assets/css/admin.css',
			array(),
			SEO_NLC_VERSION
		);

		// Skrypty.
		wp_enqueue_script(
			'seo-nlc-meta-box',
			SEO_NLC_URL . 'admin/assets/js/admin.js',
			array( 'jquery', 'wp-util' ),
			SEO_NLC_VERSION,
			true
		);

		// Lokalizacja.
		wp_localize_script(
			'seo-nlc-meta-box',
			'seoNlcMetaBox',
			array(
				'maxTitleLength'       => Validator::MAX_TITLE_LENGTH,
				'maxDescriptionLength' => Validator::MAX_DESCRIPTION_LENGTH,
				'siteUrl'              => home_url(),
				'siteName'             => get_bloginfo( 'name' ),
				'strings'              => array(
					'selectImage'    => __( 'Wybierz obraz', 'seo-costom-newlifecolor' ),
					'useImage'       => __( 'Użyj tego obrazu', 'seo-costom-newlifecolor' ),
					'removeImage'    => __( 'Usuń', 'seo-costom-newlifecolor' ),
					'confirmRemove'  => __( 'Czy na pewno chcesz usunąć?', 'seo-costom-newlifecolor' ),
					'titleTooLong'   => __( 'Tytuł jest za długi', 'seo-costom-newlifecolor' ),
					'titleOptimal'   => __( 'Tytuł ma optymalną długość', 'seo-costom-newlifecolor' ),
					'descTooLong'    => __( 'Opis jest za długi', 'seo-costom-newlifecolor' ),
					'descOptimal'    => __( 'Opis ma optymalną długość', 'seo-costom-newlifecolor' ),
				),
			)
		);
	}

	/**
	 * Pobiera dostępne typy Schema.
	 *
	 * @return array Tablica typów Schema.
	 */
	public function get_schema_types(): array {
		return $this->schema_types;
	}

	/**
	 * Pobiera dostępne typy Twitter Card.
	 *
	 * @return array Tablica typów Twitter Card.
	 */
	public function get_twitter_card_types(): array {
		return $this->twitter_card_types;
	}

	/**
	 * Pobiera listę usług z ustawień globalnych.
	 *
	 * @return array Lista usług.
	 */
	public function get_services_list(): array {
		$services = get_option( 'seo_nlc_services', array() );

		if ( ! is_array( $services ) ) {
			return array();
		}

		// Filtruj tylko aktywne usługi.
		return array_filter(
			$services,
			function ( $service ) {
				return ! empty( $service['active'] ) && ! empty( $service['name'] );
			}
		);
	}
}
