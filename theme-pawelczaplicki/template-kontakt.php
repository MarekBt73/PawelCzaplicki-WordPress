<?php

declare(strict_types=1);

/**
 * Template Name: Kontakt
 * Description: Strona kontaktowa w stylu Protokół 17:00™ – dwukolumnowy layout z formularzem.
 */

get_header();

?>

<div class="pt-40 pb-24 lg:pt-48 pc-contact-main">
	<div class="max-w-7xl mx-auto px-6 md:px-12">
			<div class="grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-24">

				<!-- Lewa kolumna: Copy & Info (jak we wzorze – bez formularza) -->
				<div class="lg:col-span-5">
					<p class="pc-contact-kicker"><?php esc_html_e( 'Audyt / Współpraca', 'pawelczaplicki' ); ?></p>
					<h1 class="pc-contact-title">
						<?php esc_html_e( 'Porozmawiajmy o architekturze', 'pawelczaplicki' ); ?> <span class="pc-contact-title-accent"><?php esc_html_e( 'Twojej firmy.', 'pawelczaplicki' ); ?></span>
					</h1>
					<p class="pc-contact-lead">
						<?php esc_html_e( 'Wypełnij formularz obok. Skontaktuję się z Tobą w ciągu 24 godzin, aby ustalić, czy Protokół 17:00™ sprawdzi się w Twojej organizacji.', 'pawelczaplicki' ); ?>
					</p>

					<div class="pc-contact-info">
						<div class="pc-contact-info-item">
							<span class="pc-contact-info-label"><?php esc_html_e( 'Bezpośredni kontakt', 'pawelczaplicki' ); ?></span>
							<a href="mailto:kontakt@pawelczaplicki.com" class="pc-contact-email-link">kontakt@pawelczaplicki.com</a>
						</div>
						<div class="pc-contact-info-item">
							<span class="pc-contact-info-label"><?php esc_html_e( 'Telefon', 'pawelczaplicki' ); ?></span>
							<a href="tel:+48000000000" class="pc-contact-tel-link">XXX XXX XXX</a>
						</div>
						<div class="pc-contact-info-item">
							<span class="pc-contact-info-label"><?php esc_html_e( 'Obszar działania', 'pawelczaplicki' ); ?></span>
							<span class="pc-contact-info-text"><?php esc_html_e( 'Cała Polska (Online / Dojazd do firmy)', 'pawelczaplicki' ); ?></span>
						</div>
						<div class="pc-contact-info-item pc-contact-social">
							<span class="pc-contact-info-label"><?php esc_html_e( 'Social media', 'pawelczaplicki' ); ?></span>
							<div class="pc-contact-social-links">
								<a href="#" class="pc-contact-social-link" aria-label="Facebook" title="Facebook"><i data-lucide="facebook" class="w-5 h-5" aria-hidden="true"></i></a>
								<a href="#" class="pc-contact-social-link" aria-label="LinkedIn" title="LinkedIn"><i data-lucide="linkedin" class="w-5 h-5" aria-hidden="true"></i></a>
								<a href="#" class="pc-contact-social-link" aria-label="X" title="X"><i data-lucide="twitter" class="w-5 h-5" aria-hidden="true"></i></a>
								<a href="#" class="pc-contact-social-link" aria-label="YouTube" title="YouTube"><i data-lucide="youtube" class="w-5 h-5" aria-hidden="true"></i></a>
							</div>
						</div>
					</div>
				</div>

				<!-- Prawa kolumna: Tylko formularz pc-contact-form -->
				<div class="lg:col-span-7">
					<div class="pc-contact-form-wrapper">
						<?php echo do_shortcode( '[pc_contact_form]' ); ?>
					</div>
				</div>

			</div>
		</div>
</div>

<?php

get_footer();
