<?php
/**
 * Template strony ustawień pluginu.
 *
 * @package SeoCustomNLC
 * @since   1.0.0
 */

declare(strict_types=1);

// Zabezpieczenie przed bezpośrednim dostępem.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wrap seo-nlc-settings">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php settings_errors( 'seo_nlc_messages' ); ?>

	<div class="seo-nlc-settings-wrapper">
		<div class="seo-nlc-settings-main">
			<form method="post" action="options.php" id="seo-nlc-settings-form">
				<?php
				// Output security fields.
				settings_fields( \SeoCustomNLC\Admin::OPTION_GROUP );

				// Output setting sections and fields.
				do_settings_sections( \SeoCustomNLC\Admin::PAGE_SLUG );

				// Submit button.
				submit_button(
					__( 'Zapisz ustawienia', 'seo-costom-newlifecolor' ),
					'primary',
					'submit',
					true,
					array( 'id' => 'seo-nlc-submit' )
				);
				?>
			</form>
		</div>

		<div class="seo-nlc-settings-sidebar">
			<div class="seo-nlc-info-box">
				<h3><?php esc_html_e( 'SEO Custom NewLifeColor', 'seo-costom-newlifecolor' ); ?></h3>
				<p>
					<?php esc_html_e( 'Plugin SEO z pełnym wsparciem Schema.org dla lokalnych usług.', 'seo-costom-newlifecolor' ); ?>
				</p>
				<p>
					<strong><?php esc_html_e( 'Wersja:', 'seo-costom-newlifecolor' ); ?></strong>
					<?php echo esc_html( SEO_NLC_VERSION ); ?>
				</p>
			</div>

			<div class="seo-nlc-info-box">
				<h3><?php esc_html_e( 'Testowanie Schema', 'seo-costom-newlifecolor' ); ?></h3>
				<p>
					<?php esc_html_e( 'Po skonfigurowaniu pluginu przetestuj Schema.org:', 'seo-costom-newlifecolor' ); ?>
				</p>
				<ul>
					<li>
						<a href="https://search.google.com/test/rich-results" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Google Rich Results Test', 'seo-costom-newlifecolor' ); ?>
						</a>
					</li>
					<li>
						<a href="https://validator.schema.org/" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Schema Markup Validator', 'seo-costom-newlifecolor' ); ?>
						</a>
					</li>
				</ul>
			</div>

			<div class="seo-nlc-info-box">
				<h3><?php esc_html_e( 'Wskazówki', 'seo-costom-newlifecolor' ); ?></h3>
				<ul>
					<li>
						<?php esc_html_e( 'Wypełnij wszystkie dane firmy dla pełnego LocalBusiness schema.', 'seo-costom-newlifecolor' ); ?>
					</li>
					<li>
						<?php esc_html_e( 'Dodaj współrzędne GPS dla lepszej lokalizacji w Google Maps.', 'seo-costom-newlifecolor' ); ?>
					</li>
					<li>
						<?php esc_html_e( 'Ustaw godziny otwarcia - to ważne dla lokalnego SEO.', 'seo-costom-newlifecolor' ); ?>
					</li>
					<li>
						<?php esc_html_e( 'Dodaj usługi - będą widoczne jako OfferCatalog w Schema.', 'seo-costom-newlifecolor' ); ?>
					</li>
				</ul>
			</div>

			<div class="seo-nlc-info-box">
				<h3><?php esc_html_e( 'Znajdź współrzędne GPS', 'seo-costom-newlifecolor' ); ?></h3>
				<p>
					<?php esc_html_e( 'Aby znaleźć współrzędne GPS swojej lokalizacji:', 'seo-costom-newlifecolor' ); ?>
				</p>
				<ol>
					<li><?php esc_html_e( 'Otwórz Google Maps', 'seo-costom-newlifecolor' ); ?></li>
					<li><?php esc_html_e( 'Kliknij prawym przyciskiem na swoją lokalizację', 'seo-costom-newlifecolor' ); ?></li>
					<li><?php esc_html_e( 'Skopiuj współrzędne (pierwszy to latitude, drugi to longitude)', 'seo-costom-newlifecolor' ); ?></li>
				</ol>
			</div>
		</div>
	</div>
</div>

<style>
	.seo-nlc-settings-wrapper {
		display: flex;
		gap: 30px;
		margin-top: 20px;
	}

	.seo-nlc-settings-main {
		flex: 1;
		max-width: 800px;
	}

	.seo-nlc-settings-sidebar {
		width: 300px;
		flex-shrink: 0;
	}

	.seo-nlc-info-box {
		background: #fff;
		border: 1px solid #c3c4c7;
		border-radius: 4px;
		padding: 15px;
		margin-bottom: 20px;
	}

	.seo-nlc-info-box h3 {
		margin-top: 0;
		padding-bottom: 10px;
		border-bottom: 1px solid #ddd;
	}

	.seo-nlc-info-box ul,
	.seo-nlc-info-box ol {
		margin: 10px 0 0 20px;
	}

	.seo-nlc-info-box li {
		margin-bottom: 5px;
	}

	.seo-nlc-info-box a {
		text-decoration: none;
	}

	.seo-nlc-info-box a:hover {
		text-decoration: underline;
	}

	@media screen and (max-width: 1200px) {
		.seo-nlc-settings-wrapper {
			flex-direction: column;
		}

		.seo-nlc-settings-sidebar {
			width: 100%;
			max-width: 800px;
		}
	}
</style>
