<?php
/**
 * Klasa panelu administracyjnego.
 *
 * Zarządza stroną ustawień pluginu i Settings API.
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
 * Klasa Admin.
 *
 * Rejestruje menu, ustawienia i obsługuje stronę ustawień.
 *
 * @since 1.0.0
 */
class Admin {

	/**
	 * Slug strony ustawień.
	 *
	 * @var string
	 */
	public const PAGE_SLUG = 'seo-nlc-settings';

	/**
	 * Grupa opcji.
	 *
	 * @var string
	 */
	public const OPTION_GROUP = 'seo_nlc_options';

	/**
	 * Instancja walidatora.
	 *
	 * @var Validator
	 */
	private Validator $validator;

	/**
	 * Dostępne typy działalności.
	 *
	 * @var array
	 */
	private array $business_types = array(
		'LocalBusiness'               => 'Firma lokalna (ogólna)',
		'HousePainter'                => 'Usługi malarskie / Malarz',
		'HomeAndConstructionBusiness' => 'Budownictwo i remonty',
		'GeneralContractor'           => 'Generalny wykonawca',
		'RoofingContractor'           => 'Usługi dekarskie',
		'Plumber'                     => 'Hydraulik',
		'Electrician'                 => 'Elektryk',
		'HVACBusiness'                => 'Klimatyzacja i wentylacja',
		'Locksmith'                   => 'Ślusarz',
		'MovingCompany'               => 'Firma przeprowadzkowa',
		'ProfessionalService'         => 'Usługi profesjonalne',
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
	 * Dodaje stronę menu w panelu administracyjnym.
	 *
	 * @return void
	 */
	public function add_menu_page(): void {
		add_options_page(
			__( 'SEO Custom NLC - Ustawienia', 'seo-costom-newlifecolor' ),
			__( 'SEO Custom NLC', 'seo-costom-newlifecolor' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Rejestruje wszystkie ustawienia pluginu.
	 *
	 * @return void
	 */
	public function register_settings(): void {
		// Sekcja: Dane Firmy.
		$this->register_company_settings();

		// Sekcja: Obszar Działania.
		$this->register_area_settings();

		// Sekcja: Usługi.
		$this->register_services_settings();

		// Sekcja: Domyślne SEO.
		$this->register_seo_settings();
	}

	/**
	 * Rejestruje ustawienia sekcji Dane Firmy.
	 *
	 * @return void
	 */
	private function register_company_settings(): void {
		$section = 'seo_nlc_company_section';

		add_settings_section(
			$section,
			__( 'Dane Firmy', 'seo-costom-newlifecolor' ),
			array( $this, 'render_company_section' ),
			self::PAGE_SLUG
		);

		// Nazwa firmy.
		register_setting(
			self::OPTION_GROUP,
			'seo_nlc_company_name',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);
		add_settings_field(
			'seo_nlc_company_name',
			__( 'Nazwa firmy', 'seo-costom-newlifecolor' ),
			array( $this, 'render_text_field' ),
			self::PAGE_SLUG,
			$section,
			array(
				'name'        => 'seo_nlc_company_name',
				'description' => __( 'Oficjalna nazwa firmy wyświetlana w Schema.org', 'seo-costom-newlifecolor' ),
			)
		);

		// Typ działalności.
		register_setting(
			self::OPTION_GROUP,
			'seo_nlc_business_type',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'sanitize_business_type' ),
				'default'           => 'LocalBusiness',
			)
		);
		add_settings_field(
			'seo_nlc_business_type',
			__( 'Typ działalności', 'seo-costom-newlifecolor' ),
			array( $this, 'render_select_field' ),
			self::PAGE_SLUG,
			$section,
			array(
				'name'        => 'seo_nlc_business_type',
				'options'     => $this->business_types,
				'description' => __( 'Typ działalności według Schema.org', 'seo-costom-newlifecolor' ),
			)
		);

		// Adres - ulica.
		register_setting(
			self::OPTION_GROUP,
			'seo_nlc_street',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);
		add_settings_field(
			'seo_nlc_street',
			__( 'Ulica i numer', 'seo-costom-newlifecolor' ),
			array( $this, 'render_text_field' ),
			self::PAGE_SLUG,
			$section,
			array(
				'name'        => 'seo_nlc_street',
				'placeholder' => 'ul. Przykładowa 123',
			)
		);

		// Miasto.
		register_setting(
			self::OPTION_GROUP,
			'seo_nlc_city',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);
		add_settings_field(
			'seo_nlc_city',
			__( 'Miasto', 'seo-costom-newlifecolor' ),
			array( $this, 'render_text_field' ),
			self::PAGE_SLUG,
			$section,
			array( 'name' => 'seo_nlc_city' )
		);

		// Kod pocztowy.
		register_setting(
			self::OPTION_GROUP,
			'seo_nlc_postal_code',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this->validator, 'sanitize_postal_code' ),
				'default'           => '',
			)
		);
		add_settings_field(
			'seo_nlc_postal_code',
			__( 'Kod pocztowy', 'seo-costom-newlifecolor' ),
			array( $this, 'render_text_field' ),
			self::PAGE_SLUG,
			$section,
			array(
				'name'        => 'seo_nlc_postal_code',
				'placeholder' => '00-000',
				'class'       => 'small-text',
			)
		);

		// Województwo.
		register_setting(
			self::OPTION_GROUP,
			'seo_nlc_region',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);
		add_settings_field(
			'seo_nlc_region',
			__( 'Województwo', 'seo-costom-newlifecolor' ),
			array( $this, 'render_text_field' ),
			self::PAGE_SLUG,
			$section,
			array( 'name' => 'seo_nlc_region' )
		);

		// Telefon.
		register_setting(
			self::OPTION_GROUP,
			'seo_nlc_phone',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this->validator, 'sanitize_phone' ),
				'default'           => '',
			)
		);
		add_settings_field(
			'seo_nlc_phone',
			__( 'Telefon', 'seo-costom-newlifecolor' ),
			array( $this, 'render_text_field' ),
			self::PAGE_SLUG,
			$section,
			array(
				'name'        => 'seo_nlc_phone',
				'placeholder' => '+48 123 456 789',
			)
		);

		// Email.
		register_setting(
			self::OPTION_GROUP,
			'seo_nlc_email',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this->validator, 'sanitize_email' ),
				'default'           => '',
			)
		);
		add_settings_field(
			'seo_nlc_email',
			__( 'Email', 'seo-costom-newlifecolor' ),
			array( $this, 'render_text_field' ),
			self::PAGE_SLUG,
			$section,
			array(
				'name' => 'seo_nlc_email',
				'type' => 'email',
			)
		);

		// Godziny otwarcia (JSON).
		register_setting(
			self::OPTION_GROUP,
			'seo_nlc_opening_hours',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_textarea_field',
				'default'           => '',
			)
		);
		add_settings_field(
			'seo_nlc_opening_hours',
			__( 'Godziny otwarcia', 'seo-costom-newlifecolor' ),
			array( $this, 'render_opening_hours_field' ),
			self::PAGE_SLUG,
			$section,
			array( 'name' => 'seo_nlc_opening_hours' )
		);

		// Logo firmy.
		register_setting(
			self::OPTION_GROUP,
			'seo_nlc_logo',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'default'           => 0,
			)
		);
		add_settings_field(
			'seo_nlc_logo',
			__( 'Logo firmy', 'seo-costom-newlifecolor' ),
			array( $this, 'render_media_field' ),
			self::PAGE_SLUG,
			$section,
			array(
				'name'        => 'seo_nlc_logo',
				'description' => __( 'Zalecany rozmiar: 600x60px lub kwadrat 512x512px', 'seo-costom-newlifecolor' ),
			)
		);

		// Social media.
		$social_networks = array(
			'facebook'  => 'Facebook',
			'linkedin'  => 'LinkedIn',
			'instagram' => 'Instagram',
		);

		foreach ( $social_networks as $network => $label ) {
			$option_name = 'seo_nlc_social_' . $network;

			register_setting(
				self::OPTION_GROUP,
				$option_name,
				array(
					'type'              => 'string',
					'sanitize_callback' => array( $this->validator, 'sanitize_url' ),
					'default'           => '',
				)
			);
			add_settings_field(
				$option_name,
				$label,
				array( $this, 'render_text_field' ),
				self::PAGE_SLUG,
				$section,
				array(
					'name'        => $option_name,
					'type'        => 'url',
					'placeholder' => 'https://' . $network . '.com/...',
				)
			);
		}
	}

	/**
	 * Rejestruje ustawienia sekcji Obszar Działania.
	 *
	 * @return void
	 */
	private function register_area_settings(): void {
		$section = 'seo_nlc_area_section';

		add_settings_section(
			$section,
			__( 'Obszar Działania', 'seo-costom-newlifecolor' ),
			array( $this, 'render_area_section' ),
			self::PAGE_SLUG
		);

		// Lista miast.
		register_setting(
			self::OPTION_GROUP,
			'seo_nlc_cities',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this->validator, 'sanitize_cities_list' ),
				'default'           => '',
			)
		);
		add_settings_field(
			'seo_nlc_cities',
			__( 'Obsługiwane miasta', 'seo-costom-newlifecolor' ),
			array( $this, 'render_textarea_field' ),
			self::PAGE_SLUG,
			$section,
			array(
				'name'        => 'seo_nlc_cities',
				'description' => __( 'Jedno miasto w linii. Będą wyświetlane w Schema.org jako areaServed.', 'seo-costom-newlifecolor' ),
				'rows'        => 8,
			)
		);

		// Promień działania.
		register_setting(
			self::OPTION_GROUP,
			'seo_nlc_radius',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'default'           => 50,
			)
		);
		add_settings_field(
			'seo_nlc_radius',
			__( 'Promień działania (km)', 'seo-costom-newlifecolor' ),
			array( $this, 'render_number_field' ),
			self::PAGE_SLUG,
			$section,
			array(
				'name'        => 'seo_nlc_radius',
				'min'         => 1,
				'max'         => 500,
				'description' => __( 'Promień w kilometrach od siedziby firmy', 'seo-costom-newlifecolor' ),
			)
		);

		// Współrzędne GPS - szerokość.
		register_setting(
			self::OPTION_GROUP,
			'seo_nlc_geo_lat',
			array(
				'type'              => 'string',
				'sanitize_callback' => function ( $value ) {
					return $this->validator->sanitize_coordinate( (string) $value, 'lat' );
				},
				'default'           => '',
			)
		);
		add_settings_field(
			'seo_nlc_geo_lat',
			__( 'Szerokość geogr. (latitude)', 'seo-costom-newlifecolor' ),
			array( $this, 'render_text_field' ),
			self::PAGE_SLUG,
			$section,
			array(
				'name'        => 'seo_nlc_geo_lat',
				'placeholder' => '52.2297',
				'class'       => 'regular-text',
				'description' => __( 'Np. 52.2297 dla Warszawy', 'seo-costom-newlifecolor' ),
			)
		);

		// Współrzędne GPS - długość.
		register_setting(
			self::OPTION_GROUP,
			'seo_nlc_geo_lng',
			array(
				'type'              => 'string',
				'sanitize_callback' => function ( $value ) {
					return $this->validator->sanitize_coordinate( (string) $value, 'lng' );
				},
				'default'           => '',
			)
		);
		add_settings_field(
			'seo_nlc_geo_lng',
			__( 'Długość geogr. (longitude)', 'seo-costom-newlifecolor' ),
			array( $this, 'render_text_field' ),
			self::PAGE_SLUG,
			$section,
			array(
				'name'        => 'seo_nlc_geo_lng',
				'placeholder' => '21.0122',
				'class'       => 'regular-text',
				'description' => __( 'Np. 21.0122 dla Warszawy', 'seo-costom-newlifecolor' ),
			)
		);
	}

	/**
	 * Rejestruje ustawienia sekcji Usługi.
	 *
	 * @return void
	 */
	private function register_services_settings(): void {
		$section = 'seo_nlc_services_section';

		add_settings_section(
			$section,
			__( 'Usługi', 'seo-costom-newlifecolor' ),
			array( $this, 'render_services_section' ),
			self::PAGE_SLUG
		);

		// Repeater usług.
		register_setting(
			self::OPTION_GROUP,
			'seo_nlc_services',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this->validator, 'sanitize_services' ),
				'default'           => array(),
			)
		);
		add_settings_field(
			'seo_nlc_services',
			__( 'Lista usług', 'seo-costom-newlifecolor' ),
			array( $this, 'render_services_repeater' ),
			self::PAGE_SLUG,
			$section,
			array( 'name' => 'seo_nlc_services' )
		);
	}

	/**
	 * Rejestruje ustawienia sekcji Domyślne SEO.
	 *
	 * @return void
	 */
	private function register_seo_settings(): void {
		$section = 'seo_nlc_seo_section';

		add_settings_section(
			$section,
			__( 'Domyślne SEO', 'seo-costom-newlifecolor' ),
			array( $this, 'render_seo_section' ),
			self::PAGE_SLUG
		);

		// Separator tytułu.
		register_setting(
			self::OPTION_GROUP,
			'seo_nlc_title_separator',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '|',
			)
		);
		add_settings_field(
			'seo_nlc_title_separator',
			__( 'Separator tytułu', 'seo-costom-newlifecolor' ),
			array( $this, 'render_text_field' ),
			self::PAGE_SLUG,
			$section,
			array(
				'name'        => 'seo_nlc_title_separator',
				'class'       => 'small-text',
				'description' => __( 'Znak oddzielający tytuł strony od nazwy witryny', 'seo-costom-newlifecolor' ),
			)
		);

		// Format tytułu.
		register_setting(
			self::OPTION_GROUP,
			'seo_nlc_title_format',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '{{title}} {{separator}} {{site_name}}',
			)
		);
		add_settings_field(
			'seo_nlc_title_format',
			__( 'Format tytułu', 'seo-costom-newlifecolor' ),
			array( $this, 'render_text_field' ),
			self::PAGE_SLUG,
			$section,
			array(
				'name'        => 'seo_nlc_title_format',
				'description' => __( 'Dostępne: {{title}}, {{separator}}, {{site_name}}', 'seo-costom-newlifecolor' ),
			)
		);

		// Domyślny opis.
		register_setting(
			self::OPTION_GROUP,
			'seo_nlc_default_description',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this->validator, 'sanitize_meta_description' ),
				'default'           => '',
			)
		);
		add_settings_field(
			'seo_nlc_default_description',
			__( 'Domyślny opis', 'seo-costom-newlifecolor' ),
			array( $this, 'render_textarea_field' ),
			self::PAGE_SLUG,
			$section,
			array(
				'name'        => 'seo_nlc_default_description',
				'rows'        => 3,
				'maxlength'   => 160,
				'description' => __( 'Używany gdy strona nie ma własnego opisu. Max 160 znaków.', 'seo-costom-newlifecolor' ),
			)
		);

		// Domyślny obraz OG.
		register_setting(
			self::OPTION_GROUP,
			'seo_nlc_default_og_image',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'default'           => 0,
			)
		);
		add_settings_field(
			'seo_nlc_default_og_image',
			__( 'Domyślny obraz OG', 'seo-costom-newlifecolor' ),
			array( $this, 'render_media_field' ),
			self::PAGE_SLUG,
			$section,
			array(
				'name'        => 'seo_nlc_default_og_image',
				'description' => __( 'Zalecany rozmiar: 1200x630px', 'seo-costom-newlifecolor' ),
			)
		);
	}

	/**
	 * Sanityzuje typ działalności.
	 *
	 * @param string $value Wartość do sanityzacji.
	 * @return string Oczyszczona wartość.
	 */
	public function sanitize_business_type( string $value ): string {
		return array_key_exists( $value, $this->business_types )
			? $value
			: 'LocalBusiness';
	}

	/**
	 * Renderuje opis sekcji Dane Firmy.
	 *
	 * @return void
	 */
	public function render_company_section(): void {
		echo '<p>';
		esc_html_e(
			'Podstawowe informacje o firmie używane w Schema.org LocalBusiness.',
			'seo-costom-newlifecolor'
		);
		echo '</p>';
	}

	/**
	 * Renderuje opis sekcji Obszar Działania.
	 *
	 * @return void
	 */
	public function render_area_section(): void {
		echo '<p>';
		esc_html_e(
			'Określ obszar geograficzny świadczenia usług.',
			'seo-costom-newlifecolor'
		);
		echo '</p>';
	}

	/**
	 * Renderuje opis sekcji Usługi.
	 *
	 * @return void
	 */
	public function render_services_section(): void {
		echo '<p>';
		esc_html_e(
			'Lista usług oferowanych przez firmę. Będą dostępne do wyboru w meta boxie.',
			'seo-costom-newlifecolor'
		);
		echo '</p>';
	}

	/**
	 * Renderuje opis sekcji Domyślne SEO.
	 *
	 * @return void
	 */
	public function render_seo_section(): void {
		echo '<p>';
		esc_html_e(
			'Domyślne ustawienia SEO używane gdy strona nie ma własnych.',
			'seo-costom-newlifecolor'
		);
		echo '</p>';
	}

	/**
	 * Renderuje pole tekstowe.
	 *
	 * @param array $args Argumenty pola.
	 * @return void
	 */
	public function render_text_field( array $args ): void {
		$name        = $args['name'] ?? '';
		$type        = $args['type'] ?? 'text';
		$class       = $args['class'] ?? 'regular-text';
		$placeholder = $args['placeholder'] ?? '';
		$description = $args['description'] ?? '';
		$value       = get_option( $name, '' );

		printf(
			'<input type="%s" id="%s" name="%s" value="%s" class="%s" placeholder="%s" />',
			esc_attr( $type ),
			esc_attr( $name ),
			esc_attr( $name ),
			esc_attr( $value ),
			esc_attr( $class ),
			esc_attr( $placeholder )
		);

		if ( $description ) {
			printf(
				'<p class="description">%s</p>',
				esc_html( $description )
			);
		}
	}

	/**
	 * Renderuje pole select.
	 *
	 * @param array $args Argumenty pola.
	 * @return void
	 */
	public function render_select_field( array $args ): void {
		$name        = $args['name'] ?? '';
		$options     = $args['options'] ?? array();
		$description = $args['description'] ?? '';
		$value       = get_option( $name, '' );

		printf(
			'<select id="%s" name="%s">',
			esc_attr( $name ),
			esc_attr( $name )
		);

		foreach ( $options as $option_value => $option_label ) {
			printf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $option_value ),
				selected( $value, $option_value, false ),
				esc_html( $option_label )
			);
		}

		echo '</select>';

		if ( $description ) {
			printf(
				'<p class="description">%s</p>',
				esc_html( $description )
			);
		}
	}

	/**
	 * Renderuje pole textarea.
	 *
	 * @param array $args Argumenty pola.
	 * @return void
	 */
	public function render_textarea_field( array $args ): void {
		$name        = $args['name'] ?? '';
		$rows        = $args['rows'] ?? 5;
		$maxlength   = $args['maxlength'] ?? '';
		$description = $args['description'] ?? '';
		$value       = get_option( $name, '' );

		printf(
			'<textarea id="%s" name="%s" rows="%d" class="large-text" %s>%s</textarea>',
			esc_attr( $name ),
			esc_attr( $name ),
			(int) $rows,
			$maxlength ? 'maxlength="' . (int) $maxlength . '"' : '',
			esc_textarea( $value )
		);

		if ( $description ) {
			printf(
				'<p class="description">%s</p>',
				esc_html( $description )
			);
		}
	}

	/**
	 * Renderuje pole numeryczne.
	 *
	 * @param array $args Argumenty pola.
	 * @return void
	 */
	public function render_number_field( array $args ): void {
		$name        = $args['name'] ?? '';
		$min         = $args['min'] ?? 0;
		$max         = $args['max'] ?? 9999;
		$step        = $args['step'] ?? 1;
		$description = $args['description'] ?? '';
		$value       = get_option( $name, '' );

		printf(
			'<input type="number" id="%s" name="%s" value="%s" min="%d" max="%d" step="%s" class="small-text" />',
			esc_attr( $name ),
			esc_attr( $name ),
			esc_attr( $value ),
			(int) $min,
			(int) $max,
			esc_attr( $step )
		);

		if ( $description ) {
			printf(
				'<p class="description">%s</p>',
				esc_html( $description )
			);
		}
	}

	/**
	 * Renderuje pole media (upload obrazka).
	 *
	 * @param array $args Argumenty pola.
	 * @return void
	 */
	public function render_media_field( array $args ): void {
		$name        = $args['name'] ?? '';
		$description = $args['description'] ?? '';
		$value       = (int) get_option( $name, 0 );
		$image_url   = '';

		if ( $value ) {
			$image_url = wp_get_attachment_image_url( $value, 'medium' );
		}

		?>
		<div class="seo-nlc-media-field" data-name="<?php echo esc_attr( $name ); ?>">
			<input type="hidden"
				   id="<?php echo esc_attr( $name ); ?>"
				   name="<?php echo esc_attr( $name ); ?>"
				   value="<?php echo esc_attr( $value ); ?>" />

			<div class="seo-nlc-media-preview" <?php echo $image_url ? '' : 'style="display:none;"'; ?>>
				<img src="<?php echo esc_url( $image_url ); ?>" alt="" style="max-width: 200px; height: auto;" />
			</div>

			<p>
				<button type="button" class="button seo-nlc-media-upload">
					<?php esc_html_e( 'Wybierz obraz', 'seo-costom-newlifecolor' ); ?>
				</button>
				<button type="button" class="button seo-nlc-media-remove" <?php echo $value ? '' : 'style="display:none;"'; ?>>
					<?php esc_html_e( 'Usuń', 'seo-costom-newlifecolor' ); ?>
				</button>
			</p>

			<?php if ( $description ) : ?>
				<p class="description"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Renderuje pole godzin otwarcia.
	 *
	 * @param array $args Argumenty pola.
	 * @return void
	 */
	public function render_opening_hours_field( array $args ): void {
		$name  = $args['name'] ?? '';
		$value = get_option( $name, '' );

		$days = array(
			'Monday'    => __( 'Poniedziałek', 'seo-costom-newlifecolor' ),
			'Tuesday'   => __( 'Wtorek', 'seo-costom-newlifecolor' ),
			'Wednesday' => __( 'Środa', 'seo-costom-newlifecolor' ),
			'Thursday'  => __( 'Czwartek', 'seo-costom-newlifecolor' ),
			'Friday'    => __( 'Piątek', 'seo-costom-newlifecolor' ),
			'Saturday'  => __( 'Sobota', 'seo-costom-newlifecolor' ),
			'Sunday'    => __( 'Niedziela', 'seo-costom-newlifecolor' ),
		);

		// Parsuj istniejące godziny.
		$hours = array();
		if ( ! empty( $value ) ) {
			$decoded = json_decode( $value, true );
			if ( is_array( $decoded ) ) {
				$hours = $decoded;
			}
		}

		?>
		<div class="seo-nlc-opening-hours">
			<table class="widefat" style="max-width: 500px;">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Dzień', 'seo-costom-newlifecolor' ); ?></th>
						<th><?php esc_html_e( 'Otwarte', 'seo-costom-newlifecolor' ); ?></th>
						<th><?php esc_html_e( 'Od', 'seo-costom-newlifecolor' ); ?></th>
						<th><?php esc_html_e( 'Do', 'seo-costom-newlifecolor' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $days as $day_key => $day_label ) : ?>
						<?php
						$day_data = $hours[ $day_key ] ?? array(
							'open'   => false,
							'opens'  => '08:00',
							'closes' => '16:00',
						);
						?>
						<tr>
							<td><?php echo esc_html( $day_label ); ?></td>
							<td>
								<input type="checkbox"
									   name="seo_nlc_hours[<?php echo esc_attr( $day_key ); ?>][open]"
									   value="1"
									   <?php checked( ! empty( $day_data['open'] ) ); ?> />
							</td>
							<td>
								<input type="time"
									   name="seo_nlc_hours[<?php echo esc_attr( $day_key ); ?>][opens]"
									   value="<?php echo esc_attr( $day_data['opens'] ?? '08:00' ); ?>"
									   class="small-text" />
							</td>
							<td>
								<input type="time"
									   name="seo_nlc_hours[<?php echo esc_attr( $day_key ); ?>][closes]"
									   value="<?php echo esc_attr( $day_data['closes'] ?? '16:00' ); ?>"
									   class="small-text" />
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<input type="hidden" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" />
			<p class="description">
				<?php esc_html_e( 'Zaznacz dni w które firma jest otwarta i ustaw godziny.', 'seo-costom-newlifecolor' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Renderuje repeater usług.
	 *
	 * @param array $args Argumenty pola.
	 * @return void
	 */
	public function render_services_repeater( array $args ): void {
		$name     = $args['name'] ?? '';
		$services = get_option( $name, array() );

		if ( ! is_array( $services ) ) {
			$services = array();
		}

		?>
		<div class="seo-nlc-services-repeater" id="seo-nlc-services">
			<table class="widefat">
				<thead>
					<tr>
						<th style="width: 30%;"><?php esc_html_e( 'Nazwa usługi', 'seo-costom-newlifecolor' ); ?></th>
						<th style="width: 50%;"><?php esc_html_e( 'Opis', 'seo-costom-newlifecolor' ); ?></th>
						<th style="width: 10%;"><?php esc_html_e( 'Aktywna', 'seo-costom-newlifecolor' ); ?></th>
						<th style="width: 10%;"><?php esc_html_e( 'Akcje', 'seo-costom-newlifecolor' ); ?></th>
					</tr>
				</thead>
				<tbody id="seo-nlc-services-body">
					<?php
					if ( ! empty( $services ) ) :
						foreach ( $services as $index => $service ) :
							$this->render_service_row( $index, $service );
						endforeach;
					endif;
					?>
				</tbody>
			</table>

			<p>
				<button type="button" class="button button-secondary" id="seo-nlc-add-service">
					<?php esc_html_e( '+ Dodaj usługę', 'seo-costom-newlifecolor' ); ?>
				</button>
			</p>
		</div>

		<script type="text/template" id="seo-nlc-service-template">
			<?php $this->render_service_row( '{{INDEX}}', array() ); ?>
		</script>
		<?php
	}

	/**
	 * Renderuje wiersz usługi w repeaterze.
	 *
	 * @param int|string $index   Indeks wiersza.
	 * @param array      $service Dane usługi.
	 * @return void
	 */
	private function render_service_row( int|string $index, array $service ): void {
		$name        = $service['name'] ?? '';
		$description = $service['description'] ?? '';
		$active      = $service['active'] ?? true;

		?>
		<tr class="seo-nlc-service-row">
			<td>
				<input type="text"
					   name="seo_nlc_services[<?php echo esc_attr( $index ); ?>][name]"
					   value="<?php echo esc_attr( $name ); ?>"
					   class="regular-text"
					   placeholder="<?php esc_attr_e( 'Nazwa usługi', 'seo-costom-newlifecolor' ); ?>" />
			</td>
			<td>
				<textarea name="seo_nlc_services[<?php echo esc_attr( $index ); ?>][description]"
						  rows="2"
						  class="large-text"
						  placeholder="<?php esc_attr_e( 'Krótki opis usługi', 'seo-costom-newlifecolor' ); ?>"><?php echo esc_textarea( $description ); ?></textarea>
			</td>
			<td style="text-align: center;">
				<input type="checkbox"
					   name="seo_nlc_services[<?php echo esc_attr( $index ); ?>][active]"
					   value="1"
					   <?php checked( $active ); ?> />
			</td>
			<td>
				<button type="button" class="button button-link-delete seo-nlc-remove-service">
					<?php esc_html_e( 'Usuń', 'seo-costom-newlifecolor' ); ?>
				</button>
			</td>
		</tr>
		<?php
	}

	/**
	 * Renderuje stronę ustawień.
	 *
	 * @return void
	 */
	public function render_settings_page(): void {
		// Sprawdź uprawnienia.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Załaduj widok.
		include SEO_NLC_PATH . 'admin/views/settings-page.php';
	}

	/**
	 * Ładuje assety dla panelu administracyjnego.
	 *
	 * @param string $hook_suffix Suffix hooka strony.
	 * @return void
	 */
	public function enqueue_assets( string $hook_suffix ): void {
		// Ładuj tylko na stronie ustawień pluginu.
		if ( 'settings_page_' . self::PAGE_SLUG !== $hook_suffix ) {
			return;
		}

		// Media uploader.
		wp_enqueue_media();

		// Style.
		wp_enqueue_style(
			'seo-nlc-admin',
			SEO_NLC_URL . 'admin/assets/css/admin.css',
			array(),
			SEO_NLC_VERSION
		);

		// Skrypty.
		wp_enqueue_script(
			'seo-nlc-admin',
			SEO_NLC_URL . 'admin/assets/js/admin.js',
			array( 'jquery', 'wp-util' ),
			SEO_NLC_VERSION,
			true
		);

		// Lokalizacja.
		wp_localize_script(
			'seo-nlc-admin',
			'seoNlcAdmin',
			array(
				'strings' => array(
					'selectImage'  => __( 'Wybierz obraz', 'seo-costom-newlifecolor' ),
					'useImage'     => __( 'Użyj tego obrazu', 'seo-costom-newlifecolor' ),
					'removeImage'  => __( 'Usuń', 'seo-costom-newlifecolor' ),
					'confirmRemove' => __( 'Czy na pewno chcesz usunąć tę usługę?', 'seo-costom-newlifecolor' ),
				),
			)
		);
	}

	/**
	 * Pobiera dostępne typy działalności.
	 *
	 * @return array Tablica typów działalności.
	 */
	public function get_business_types(): array {
		return $this->business_types;
	}
}
