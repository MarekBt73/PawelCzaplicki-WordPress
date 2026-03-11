<?php
/**
 * Plugin Name: PC Contact Form
 * Description: Bezpieczny formularz kontaktowy (shortcode) bez zapisu w bazie. Wysyła wiadomość do administratora i potwierdzenie do nadawcy.
 * Version: 0.1.2
 * Author: marekbecht.pl
 * License: GPL-2.0-or-later
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class PC_Contact_Form {
	private const OPTION_KEY = 'pc_contact_form_options';
	private const NONCE_ACTION = 'pc_contact_form_submit';
	private const NONCE_NAME = 'pc_contact_form_nonce';
	private const SHORTCODE = 'pc_contact_form';
	private const RATE_LIMIT_WINDOW_SECONDS = 10 * 60;
	private const RATE_LIMIT_MAX_PER_WINDOW = 5;

	public static function bootstrap(): void {
		add_action( 'admin_menu', array( __CLASS__, 'register_settings_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );

		add_shortcode( self::SHORTCODE, array( __CLASS__, 'render_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_assets' ) );

		add_action( 'admin_post_pc_contact_form_submit', array( __CLASS__, 'handle_submit' ) );
		add_action( 'admin_post_nopriv_pc_contact_form_submit', array( __CLASS__, 'handle_submit' ) );
	}

	public static function register_assets(): void {
		wp_register_style(
			'pc-contact-form',
			plugins_url( 'assets/contact-form.css', __FILE__ ),
			array(),
			'0.1.2'
		);
	}

	public static function register_settings_page(): void {
		add_options_page(
			__( 'PC Contact Form', 'pc-contact-form' ),
			__( 'PC Contact Form', 'pc-contact-form' ),
			'manage_options',
			'pc-contact-form',
			array( __CLASS__, 'render_settings_page' )
		);
	}

	public static function register_settings(): void {
		register_setting(
			'pc_contact_form',
			self::OPTION_KEY,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize_options' ),
				'default'           => array(),
			)
		);

		add_settings_section(
			'pc_contact_form_main',
			__( 'Ustawienia', 'pc-contact-form' ),
			static function (): void {
				echo '<p>' . esc_html__( 'Konfiguracja adresu administratora oraz linku do polityki prywatności/RODO.', 'pc-contact-form' ) . '</p>';
			},
			'pc_contact_form'
		);

		add_settings_field(
			'admin_email',
			__( 'E-mail administratora', 'pc-contact-form' ),
			array( __CLASS__, 'render_field_admin_email' ),
			'pc_contact_form',
			'pc_contact_form_main'
		);

		add_settings_field(
			'policy_url',
			__( 'Link do polityki (RODO)', 'pc-contact-form' ),
			array( __CLASS__, 'render_field_policy_url' ),
			'pc_contact_form',
			'pc_contact_form_main'
		);

		add_settings_field(
			'from_email',
			__( 'E-mail nadawcy (From)', 'pc-contact-form' ),
			array( __CLASS__, 'render_field_from_email' ),
			'pc_contact_form',
			'pc_contact_form_main'
		);

		add_settings_field(
			'sender_signature',
			__( 'Podpis nadawcy (stopka)', 'pc-contact-form' ),
			array( __CLASS__, 'render_field_sender_signature' ),
			'pc_contact_form',
			'pc_contact_form_main'
		);
	}

	public static function sanitize_options( $value ): array {
		$value = is_array( $value ) ? $value : array();

		$admin_email = isset( $value['admin_email'] ) ? (string) $value['admin_email'] : '';
		$policy_url  = isset( $value['policy_url'] ) ? (string) $value['policy_url'] : '';
		$from_email  = isset( $value['from_email'] ) ? (string) $value['from_email'] : '';
		$sender_signature = isset( $value['sender_signature'] ) ? (string) $value['sender_signature'] : '';

		$admin_email = sanitize_email( $admin_email );
		if ( $admin_email === '' ) {
			$admin_email = (string) get_option( 'admin_email' );
		}

		$policy_url = esc_url_raw( $policy_url );

		$from_email = sanitize_email( $from_email );
		if ( $from_email === '' ) {
			$from_email = $admin_email;
		}

		$sender_signature = sanitize_textarea_field( $sender_signature );
		if ( trim( $sender_signature ) === '' ) {
			$sender_signature = (string) get_bloginfo( 'name' );
		}

		return array(
			'admin_email' => $admin_email,
			'policy_url'  => $policy_url,
			'from_email'  => $from_email,
			'sender_signature' => $sender_signature,
		);
	}

	private static function get_options(): array {
		$options = get_option( self::OPTION_KEY );
		return is_array( $options ) ? $options : array();
	}

	public static function render_field_admin_email(): void {
		$options    = self::get_options();
		$admin_email = isset( $options['admin_email'] ) && is_string( $options['admin_email'] ) ? $options['admin_email'] : (string) get_option( 'admin_email' );
		?>
		<input type="email" class="regular-text" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[admin_email]" value="<?php echo esc_attr( $admin_email ); ?>" required>
		<?php
	}

	public static function render_field_policy_url(): void {
		$options   = self::get_options();
		$policy_url = isset( $options['policy_url'] ) && is_string( $options['policy_url'] ) ? $options['policy_url'] : '';
		?>
		<input type="url" class="regular-text" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[policy_url]" value="<?php echo esc_attr( $policy_url ); ?>" placeholder="https://…">
		<p class="description"><?php echo esc_html__( 'Jeśli puste, link w formularzu nie będzie wyświetlany.', 'pc-contact-form' ); ?></p>
		<?php
	}

	public static function render_field_from_email(): void {
		$options   = self::get_options();
		$admin_email = isset( $options['admin_email'] ) && is_string( $options['admin_email'] ) ? $options['admin_email'] : (string) get_option( 'admin_email' );
		$from_email  = isset( $options['from_email'] ) && is_string( $options['from_email'] ) ? $options['from_email'] : $admin_email;
		?>
		<input type="email" class="regular-text" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[from_email]" value="<?php echo esc_attr( $from_email ); ?>">
		<p class="description"><?php echo esc_html__( 'Adres używany w nagłówku From dla e-maili wysyłanych przez formularz.', 'pc-contact-form' ); ?></p>
		<?php
	}

	public static function render_field_sender_signature(): void {
		$options   = self::get_options();
		$signature = isset( $options['sender_signature'] ) && is_string( $options['sender_signature'] ) ? $options['sender_signature'] : (string) get_bloginfo( 'name' );
		?>
		<textarea class="large-text" rows="4" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[sender_signature]"><?php echo esc_textarea( $signature ); ?></textarea>
		<p class="description"><?php echo esc_html__( 'Podpis dodawany w potwierdzeniu do nadawcy (np. imię/nazwa firmy).', 'pc-contact-form' ); ?></p>
		<?php
	}

	public static function render_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'PC Contact Form', 'pc-contact-form' ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'pc_contact_form' );
				do_settings_sections( 'pc_contact_form' );
				submit_button();
				?>
			</form>
			<hr>
			<h2><?php echo esc_html__( 'Użycie', 'pc-contact-form' ); ?></h2>
			<p>
				<?php echo esc_html__( 'W treści strony wstaw shortcode:', 'pc-contact-form' ); ?>
				<code>[pc_contact_form]</code>
			</p>
		</div>
		<?php
	}

	public static function render_shortcode(): string {
		wp_enqueue_style( 'pc-contact-form' );

		$options   = self::get_options();
		$policy_url = isset( $options['policy_url'] ) && is_string( $options['policy_url'] ) ? $options['policy_url'] : '';

		$action_url = esc_url( admin_url( 'admin-post.php' ) );
		$timestamp  = (string) time();

		$success = isset( $_GET['pc_contact_form'] ) && $_GET['pc_contact_form'] === 'success'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$error   = isset( $_GET['pc_contact_form'] ) && $_GET['pc_contact_form'] === 'error'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		ob_start();
		?>
		<div class="pc-contact-form">
			<?php if ( $success ) : ?>
				<div class="pc-contact-form__modal" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Wiadomość wysłana', 'pc-contact-form' ); ?>">
					<div class="pc-contact-form__modal-card">
						<div class="pc-contact-form__modal-title"><?php echo esc_html__( 'Wiadomość wysłana', 'pc-contact-form' ); ?></div>
						<div class="pc-contact-form__modal-text"><?php echo esc_html__( 'Dziękuję. Otrzymałem wiadomość i niedługo się z Tobą skontaktuję.', 'pc-contact-form' ); ?></div>
						<button type="button" class="pc-contact-form__modal-close"><?php echo esc_html__( 'OK', 'pc-contact-form' ); ?></button>
					</div>
				</div>
				<script>
					(function () {
						var modal = document.querySelector('.pc-contact-form__modal');
						if (!modal) return;
						var closeBtn = modal.querySelector('.pc-contact-form__modal-close');
						function close() { modal.remove(); }
						if (closeBtn) closeBtn.addEventListener('click', close);
						modal.addEventListener('click', function (e) { if (e.target === modal) close(); });
						document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); }, { once: true });
						setTimeout(close, 6000);
					})();
				</script>
			<?php endif; ?>

			<?php if ( $success ) : ?>
				<div class="pc-contact-form__notice pc-contact-form__notice--success">
					<?php echo esc_html__( 'Dziękuję. Wiadomość została wysłana.', 'pc-contact-form' ); ?>
				</div>
			<?php elseif ( $error ) : ?>
				<div class="pc-contact-form__notice pc-contact-form__notice--error">
					<?php echo esc_html__( 'Nie udało się wysłać wiadomości. Spróbuj ponownie za chwilę.', 'pc-contact-form' ); ?>
				</div>
			<?php endif; ?>

			<form class="pc-contact-form__form" method="post" action="<?php echo $action_url; ?>" novalidate>
				<input type="hidden" name="action" value="pc_contact_form_submit">
				<input type="hidden" name="pc_ts" value="<?php echo esc_attr( $timestamp ); ?>">
				<?php wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME ); ?>

				<div class="pc-contact-form__hp" aria-hidden="true">
					<label>
						<?php echo esc_html__( 'Firma', 'pc-contact-form' ); ?>
						<input type="text" name="pc_company" autocomplete="off" tabindex="-1">
					</label>
				</div>

				<div class="pc-contact-form__row">
					<label class="pc-contact-form__label">
						<?php echo esc_html__( 'Imię', 'pc-contact-form' ); ?> *
						<input class="pc-contact-form__input" type="text" name="pc_name" required maxlength="80" autocomplete="name">
					</label>
				</div>

				<div class="pc-contact-form__row">
					<label class="pc-contact-form__label">
						<?php echo esc_html__( 'E-mail', 'pc-contact-form' ); ?> *
						<input class="pc-contact-form__input" type="email" name="pc_email" required maxlength="120" autocomplete="email">
					</label>
				</div>

				<div class="pc-contact-form__row">
					<label class="pc-contact-form__label">
						<?php echo esc_html__( 'Telefon', 'pc-contact-form' ); ?>
						<input class="pc-contact-form__input" type="tel" name="pc_phone" maxlength="40" autocomplete="tel">
					</label>
				</div>

				<div class="pc-contact-form__row">
					<label class="pc-contact-form__label">
						<?php echo esc_html__( 'Treść', 'pc-contact-form' ); ?> *
						<textarea class="pc-contact-form__input pc-contact-form__textarea" name="pc_message" required maxlength="300" rows="5"></textarea>
						<span class="pc-contact-form__hint"><?php echo esc_html__( 'Maks. 300 znaków.', 'pc-contact-form' ); ?></span>
					</label>
				</div>

				<div class="pc-contact-form__row pc-contact-form__row--consent">
					<label class="pc-contact-form__consent">
						<input type="checkbox" name="pc_consent" value="1" required>
						<span>
							<?php echo esc_html__( 'Zapoznałem/am się z informacją RODO i wyrażam zgodę na kontakt w celach marketingowych', 'pc-contact-form' ); ?>
							<?php if ( $policy_url !== '' ) : ?>
								<a href="<?php echo esc_url( $policy_url ); ?>" target="_blank" rel="noopener noreferrer">
									<?php echo esc_html__( '(polityka)', 'pc-contact-form' ); ?>
								</a>
							<?php endif; ?>
						</span>
					</label>
				</div>

				<div class="pc-contact-form__row">
					<button class="pc-contact-form__btn" type="submit">
						<?php echo esc_html__( 'Wyślij wiadomość', 'pc-contact-form' ); ?>
					</button>
				</div>
			</form>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	public static function handle_submit(): void {
		$redirect_base = wp_get_referer();
		if ( ! is_string( $redirect_base ) || $redirect_base === '' ) {
			$redirect_base = (string) home_url( '/' );
		}

		if ( ! isset( $_POST[ self::NONCE_NAME ] ) || ! wp_verify_nonce( (string) $_POST[ self::NONCE_NAME ], self::NONCE_ACTION ) ) {
			wp_safe_redirect( add_query_arg( 'pc_contact_form', 'error', $redirect_base ) );
			exit;
		}

		if ( ! self::passes_rate_limit() ) {
			wp_safe_redirect( add_query_arg( 'pc_contact_form', 'error', $redirect_base ) );
			exit;
		}

		$company = isset( $_POST['pc_company'] ) ? trim( (string) $_POST['pc_company'] ) : '';
		if ( $company !== '' ) {
			wp_safe_redirect( add_query_arg( 'pc_contact_form', 'success', $redirect_base ) );
			exit;
		}

		$ts = isset( $_POST['pc_ts'] ) ? (int) $_POST['pc_ts'] : 0;
		if ( $ts > 0 && ( time() - $ts ) < 3 ) {
			wp_safe_redirect( add_query_arg( 'pc_contact_form', 'error', $redirect_base ) );
			exit;
		}

		$name  = isset( $_POST['pc_name'] ) ? sanitize_text_field( (string) wp_unslash( $_POST['pc_name'] ) ) : '';
		$email = isset( $_POST['pc_email'] ) ? sanitize_email( (string) wp_unslash( $_POST['pc_email'] ) ) : '';
		$phone = isset( $_POST['pc_phone'] ) ? sanitize_text_field( (string) wp_unslash( $_POST['pc_phone'] ) ) : '';
		$message = isset( $_POST['pc_message'] ) ? sanitize_textarea_field( (string) wp_unslash( $_POST['pc_message'] ) ) : '';
		$consent = isset( $_POST['pc_consent'] ) ? (string) wp_unslash( $_POST['pc_consent'] ) : '';

		$message = trim( $message );
		if ( mb_strlen( $message ) > 300 ) {
			$message = mb_substr( $message, 0, 300 );
		}

		if ( $name === '' || $email === '' || ! is_email( $email ) || $message === '' || $consent !== '1' ) {
			wp_safe_redirect( add_query_arg( 'pc_contact_form', 'error', $redirect_base ) );
			exit;
		}

		$options = self::get_options();
		$admin_email = isset( $options['admin_email'] ) && is_email( $options['admin_email'] ) ? (string) $options['admin_email'] : (string) get_option( 'admin_email' );
		$from_email  = isset( $options['from_email'] ) && is_email( $options['from_email'] ) ? (string) $options['from_email'] : $admin_email;
		$signature   = isset( $options['sender_signature'] ) && is_string( $options['sender_signature'] ) ? trim( (string) $options['sender_signature'] ) : '';
		if ( $signature === '' ) {
			$signature = (string) get_bloginfo( 'name' );
		}

		$site_name = (string) wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
		$site_url  = (string) home_url( '/' );

		$subject_admin = sprintf(
			/* translators: %s: sender name */
			__( 'Prośba o kontakt: %s', 'pc-contact-form' ),
			$name
		);

		$body_admin = implode(
			"\n",
			array_filter(
				array(
					'Imię: ' . $name,
					'E-mail: ' . $email,
					$phone !== '' ? 'Telefon: ' . $phone : null,
					'Zgoda marketingowa/RODO: TAK',
					'---',
					'Treść:',
					$message,
				)
			)
		);

		$headers_admin = array(
			'Content-Type: text/plain; charset=UTF-8',
			'From: ' . $site_name . ' <' . $from_email . '>',
			'Reply-To: ' . $name . ' <' . $email . '>',
		);

		$sent_admin = wp_mail( $admin_email, $subject_admin, $body_admin, $headers_admin );

		$subject_user = __( 'Potwierdzenie: otrzymaliśmy Twoje zgłoszenie', 'pc-contact-form' );
		$body_user = implode(
			"\n",
			array(
				'Dziękuję za kontakt.',
				'Otrzymałem wiadomość i niedługo się z Tobą skontaktuję.',
				'',
				'Wiadomość od: ' . $site_name,
				$site_url,
				'',
				$signature,
			)
		);
		$headers_user = array(
			'Content-Type: text/plain; charset=UTF-8',
			'From: ' . $site_name . ' <' . $from_email . '>',
		);
		$sent_user = wp_mail( $email, $subject_user, $body_user, $headers_user );

		if ( $sent_admin && $sent_user ) {
			wp_safe_redirect( add_query_arg( 'pc_contact_form', 'success', $redirect_base ) );
			exit;
		}

		wp_safe_redirect( add_query_arg( 'pc_contact_form', 'error', $redirect_base ) );
		exit;
	}

	private static function passes_rate_limit(): bool {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) $_SERVER['REMOTE_ADDR'] : '';
		if ( $ip === '' ) {
			return true;
		}

		$key = 'pc_cf_rl_' . md5( $ip );
		$data = get_transient( $key );
		$data = is_array( $data ) ? $data : array( 'count' => 0, 'start' => time() );

		$start = isset( $data['start'] ) ? (int) $data['start'] : time();
		$count = isset( $data['count'] ) ? (int) $data['count'] : 0;

		if ( ( time() - $start ) > self::RATE_LIMIT_WINDOW_SECONDS ) {
			$start = time();
			$count = 0;
		}

		$count++;
		set_transient( $key, array( 'count' => $count, 'start' => $start ), self::RATE_LIMIT_WINDOW_SECONDS );

		return $count <= self::RATE_LIMIT_MAX_PER_WINDOW;
	}
}

PC_Contact_Form::bootstrap();

