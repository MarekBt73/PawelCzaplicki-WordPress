<?php
/**
 * Template meta box SEO.
 *
 * @package SeoCustomNLC
 * @since   1.0.0
 *
 * @var \WP_Post $post Obiekt posta.
 * @var array    $meta Dane meta posta.
 */

declare(strict_types=1);

// Zabezpieczenie przed bezpośrednim dostępem.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Pobierz instancję Meta Box dla dostępu do metod.
$meta_box_instance = new \SeoCustomNLC\Meta_Box( new \SeoCustomNLC\Validator() );
$schema_types      = $meta_box_instance->get_schema_types();
$twitter_types     = $meta_box_instance->get_twitter_card_types();
$services          = $meta_box_instance->get_services_list();

// Oblicz długości.
$title_length = mb_strlen( $meta['title'] );
$desc_length  = mb_strlen( $meta['description'] );

// Pobierz dane dla preview.
$preview_title = ! empty( $meta['title'] ) ? $meta['title'] : $post->post_title;
$preview_url   = get_permalink( $post->ID );
$preview_desc  = ! empty( $meta['description'] )
	? $meta['description']
	: wp_trim_words( $post->post_content, 25, '...' );

?>
<div class="seo-nlc-meta-box">
	<!-- Tabs Navigation -->
	<div class="seo-nlc-tabs">
		<button type="button" class="seo-nlc-tab active" data-tab="seo">
			<?php esc_html_e( 'SEO', 'seo-costom-newlifecolor' ); ?>
		</button>
		<button type="button" class="seo-nlc-tab" data-tab="schema">
			<?php esc_html_e( 'Schema', 'seo-costom-newlifecolor' ); ?>
		</button>
		<button type="button" class="seo-nlc-tab" data-tab="social">
			<?php esc_html_e( 'Social', 'seo-costom-newlifecolor' ); ?>
		</button>
	</div>

	<!-- TAB: SEO -->
	<div class="seo-nlc-tab-content active" id="seo-nlc-tab-seo">
		<!-- Google Preview -->
		<div class="seo-nlc-preview">
			<h4><?php esc_html_e( 'Podgląd w Google', 'seo-costom-newlifecolor' ); ?></h4>
			<div class="seo-nlc-google-preview">
				<div class="seo-nlc-preview-title" id="seo-nlc-preview-title">
					<?php echo esc_html( $preview_title ); ?>
				</div>
				<div class="seo-nlc-preview-url" id="seo-nlc-preview-url">
					<?php echo esc_url( $preview_url ); ?>
				</div>
				<div class="seo-nlc-preview-description" id="seo-nlc-preview-description">
					<?php echo esc_html( $preview_desc ); ?>
				</div>
			</div>
		</div>

		<!-- SEO Title -->
		<div class="seo-nlc-field">
			<label for="seo_nlc_title">
				<?php esc_html_e( 'Tytuł SEO', 'seo-costom-newlifecolor' ); ?>
			</label>
			<input type="text"
				   id="seo_nlc_title"
				   name="seo_nlc_title"
				   value="<?php echo esc_attr( $meta['title'] ); ?>"
				   class="large-text"
				   maxlength="70"
				   placeholder="<?php echo esc_attr( $post->post_title ); ?>" />
			<div class="seo-nlc-counter">
				<span class="seo-nlc-counter-current <?php echo $title_length > 60 ? 'warning' : ( $title_length > 50 ? 'caution' : 'good' ); ?>" id="seo-nlc-title-count">
					<?php echo esc_html( $title_length ); ?>
				</span>
				<span class="seo-nlc-counter-max">/ 60</span>
				<span class="seo-nlc-counter-label" id="seo-nlc-title-status">
					<?php
					if ( $title_length > 60 ) {
						esc_html_e( '- za długi', 'seo-costom-newlifecolor' );
					} elseif ( $title_length > 50 ) {
						esc_html_e( '- prawie optymalny', 'seo-costom-newlifecolor' );
					} elseif ( $title_length > 0 ) {
						esc_html_e( '- optymalny', 'seo-costom-newlifecolor' );
					}
					?>
				</span>
			</div>
		</div>

		<!-- Meta Description -->
		<div class="seo-nlc-field">
			<label for="seo_nlc_description">
				<?php esc_html_e( 'Meta opis', 'seo-costom-newlifecolor' ); ?>
			</label>
			<textarea id="seo_nlc_description"
					  name="seo_nlc_description"
					  rows="3"
					  class="large-text"
					  maxlength="170"
					  placeholder="<?php esc_attr_e( 'Krótki opis strony dla wyszukiwarek...', 'seo-costom-newlifecolor' ); ?>"><?php echo esc_textarea( $meta['description'] ); ?></textarea>
			<div class="seo-nlc-counter">
				<span class="seo-nlc-counter-current <?php echo $desc_length > 160 ? 'warning' : ( $desc_length > 140 ? 'caution' : 'good' ); ?>" id="seo-nlc-desc-count">
					<?php echo esc_html( $desc_length ); ?>
				</span>
				<span class="seo-nlc-counter-max">/ 160</span>
				<span class="seo-nlc-counter-label" id="seo-nlc-desc-status">
					<?php
					if ( $desc_length > 160 ) {
						esc_html_e( '- za długi', 'seo-costom-newlifecolor' );
					} elseif ( $desc_length > 140 ) {
						esc_html_e( '- prawie optymalny', 'seo-costom-newlifecolor' );
					} elseif ( $desc_length > 0 ) {
						esc_html_e( '- optymalny', 'seo-costom-newlifecolor' );
					}
					?>
				</span>
			</div>
		</div>

		<!-- Focus Keyword -->
		<div class="seo-nlc-field">
			<label for="seo_nlc_focus_keyword">
				<?php esc_html_e( 'Słowo kluczowe (opcjonalnie)', 'seo-costom-newlifecolor' ); ?>
			</label>
			<input type="text"
				   id="seo_nlc_focus_keyword"
				   name="seo_nlc_focus_keyword"
				   value="<?php echo esc_attr( $meta['focus_keyword'] ); ?>"
				   class="regular-text"
				   placeholder="<?php esc_attr_e( 'Główne słowo kluczowe', 'seo-costom-newlifecolor' ); ?>" />
			<p class="description">
				<?php esc_html_e( 'Główne słowo kluczowe dla tej strony.', 'seo-costom-newlifecolor' ); ?>
			</p>
		</div>

		<!-- Canonical URL -->
		<div class="seo-nlc-field">
			<label for="seo_nlc_canonical_url">
				<?php esc_html_e( 'Canonical URL', 'seo-costom-newlifecolor' ); ?>
			</label>
			<input type="url"
				   id="seo_nlc_canonical_url"
				   name="seo_nlc_canonical_url"
				   value="<?php echo esc_url( $meta['canonical_url'] ); ?>"
				   class="large-text"
				   placeholder="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" />
			<p class="description">
				<?php esc_html_e( 'Pozostaw puste, aby użyć domyślnego URL strony.', 'seo-costom-newlifecolor' ); ?>
			</p>
		</div>
	</div>

	<!-- TAB: Schema -->
	<div class="seo-nlc-tab-content" id="seo-nlc-tab-schema">
		<!-- Schema Type -->
		<div class="seo-nlc-field">
			<label for="seo_nlc_schema_type">
				<?php esc_html_e( 'Typ Schema', 'seo-costom-newlifecolor' ); ?>
			</label>
			<select id="seo_nlc_schema_type" name="seo_nlc_schema_type" class="regular-text">
				<?php foreach ( $schema_types as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $meta['schema_type'], $value ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<!-- Auto Images -->
		<div class="seo-nlc-field seo-nlc-checkbox">
			<label>
				<input type="checkbox"
					   id="seo_nlc_auto_images"
					   name="seo_nlc_auto_images"
					   value="1"
					   <?php checked( $meta['auto_images'], '1' ); ?> />
				<?php esc_html_e( 'Automatycznie generuj Schema dla obrazów z treści', 'seo-costom-newlifecolor' ); ?>
			</label>
		</div>

		<!-- Auto Videos -->
		<div class="seo-nlc-field seo-nlc-checkbox">
			<label>
				<input type="checkbox"
					   id="seo_nlc_auto_videos"
					   name="seo_nlc_auto_videos"
					   value="1"
					   <?php checked( $meta['auto_videos'], '1' ); ?> />
				<?php esc_html_e( 'Automatycznie generuj Schema dla filmów z treści', 'seo-costom-newlifecolor' ); ?>
			</label>
		</div>

		<!-- Service (widoczne tylko dla typu Service) -->
		<div class="seo-nlc-field seo-nlc-conditional" data-show-when="seo_nlc_schema_type" data-show-value="Service">
			<label for="seo_nlc_service_id">
				<?php esc_html_e( 'Wybierz usługę', 'seo-costom-newlifecolor' ); ?>
			</label>
			<select id="seo_nlc_service_id" name="seo_nlc_service_id" class="regular-text">
				<option value=""><?php esc_html_e( '-- Wybierz usługę --', 'seo-costom-newlifecolor' ); ?></option>
				<?php foreach ( $services as $index => $service ) : ?>
					<option value="<?php echo esc_attr( $index ); ?>" <?php selected( $meta['service_id'], (string) $index ); ?>>
						<?php echo esc_html( $service['name'] ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<?php if ( empty( $services ) ) : ?>
				<p class="description">
					<?php
					printf(
						/* translators: %s: URL do ustawień */
						esc_html__( 'Brak usług. %s aby dodać usługi.', 'seo-costom-newlifecolor' ),
						'<a href="' . esc_url( admin_url( 'options-general.php?page=seo-nlc-settings' ) ) . '">' .
						esc_html__( 'Przejdź do ustawień', 'seo-costom-newlifecolor' ) .
						'</a>'
					);
					?>
				</p>
			<?php endif; ?>
		</div>

		<!-- Video URLs (widoczne tylko dla VideoGallery) -->
		<div class="seo-nlc-field seo-nlc-conditional" data-show-when="seo_nlc_schema_type" data-show-value="VideoGallery">
			<label for="seo_nlc_video_urls">
				<?php esc_html_e( 'URL-e filmów (YouTube/Vimeo)', 'seo-costom-newlifecolor' ); ?>
			</label>
			<textarea id="seo_nlc_video_urls"
					  name="seo_nlc_video_urls"
					  rows="5"
					  class="large-text"
					  placeholder="https://www.youtube.com/watch?v=...&#10;https://vimeo.com/..."><?php echo esc_textarea( $meta['video_urls'] ); ?></textarea>
			<p class="description">
				<?php esc_html_e( 'Jeden URL w linii. Obsługiwane: YouTube i Vimeo.', 'seo-costom-newlifecolor' ); ?>
			</p>
		</div>

		<!-- FAQ Items (widoczne tylko dla FAQPage) -->
		<div class="seo-nlc-field seo-nlc-conditional" data-show-when="seo_nlc_schema_type" data-show-value="FAQPage">
			<label><?php esc_html_e( 'Pytania i odpowiedzi (FAQ)', 'seo-costom-newlifecolor' ); ?></label>

			<div class="seo-nlc-faq-repeater" id="seo-nlc-faq-items">
				<?php
				$faq_items = is_array( $meta['faq_items'] ) ? $meta['faq_items'] : array();
				if ( ! empty( $faq_items ) ) :
					foreach ( $faq_items as $index => $item ) :
						?>
						<div class="seo-nlc-faq-item">
							<div class="seo-nlc-faq-question">
								<label><?php esc_html_e( 'Pytanie', 'seo-costom-newlifecolor' ); ?></label>
								<input type="text"
									   name="seo_nlc_faq[<?php echo esc_attr( $index ); ?>][question]"
									   value="<?php echo esc_attr( $item['question'] ?? '' ); ?>"
									   class="large-text"
									   placeholder="<?php esc_attr_e( 'Wpisz pytanie...', 'seo-costom-newlifecolor' ); ?>" />
							</div>
							<div class="seo-nlc-faq-answer">
								<label><?php esc_html_e( 'Odpowiedź', 'seo-costom-newlifecolor' ); ?></label>
								<textarea name="seo_nlc_faq[<?php echo esc_attr( $index ); ?>][answer]"
										  rows="3"
										  class="large-text"
										  placeholder="<?php esc_attr_e( 'Wpisz odpowiedź...', 'seo-costom-newlifecolor' ); ?>"><?php echo esc_textarea( $item['answer'] ?? '' ); ?></textarea>
							</div>
							<button type="button" class="button button-link-delete seo-nlc-remove-faq">
								<?php esc_html_e( 'Usuń', 'seo-costom-newlifecolor' ); ?>
							</button>
						</div>
						<?php
					endforeach;
				endif;
				?>
			</div>

			<button type="button" class="button" id="seo-nlc-add-faq">
				<?php esc_html_e( '+ Dodaj pytanie', 'seo-costom-newlifecolor' ); ?>
			</button>
		</div>

		<!-- FAQ Template -->
		<script type="text/template" id="seo-nlc-faq-template">
			<div class="seo-nlc-faq-item">
				<div class="seo-nlc-faq-question">
					<label><?php esc_html_e( 'Pytanie', 'seo-costom-newlifecolor' ); ?></label>
					<input type="text"
						   name="seo_nlc_faq[{{INDEX}}][question]"
						   value=""
						   class="large-text"
						   placeholder="<?php esc_attr_e( 'Wpisz pytanie...', 'seo-costom-newlifecolor' ); ?>" />
				</div>
				<div class="seo-nlc-faq-answer">
					<label><?php esc_html_e( 'Odpowiedź', 'seo-costom-newlifecolor' ); ?></label>
					<textarea name="seo_nlc_faq[{{INDEX}}][answer]"
							  rows="3"
							  class="large-text"
							  placeholder="<?php esc_attr_e( 'Wpisz odpowiedź...', 'seo-costom-newlifecolor' ); ?>"></textarea>
				</div>
				<button type="button" class="button button-link-delete seo-nlc-remove-faq">
					<?php esc_html_e( 'Usuń', 'seo-costom-newlifecolor' ); ?>
				</button>
			</div>
		</script>
	</div>

	<!-- TAB: Social -->
	<div class="seo-nlc-tab-content" id="seo-nlc-tab-social">
		<h4><?php esc_html_e( 'Open Graph (Facebook, LinkedIn)', 'seo-costom-newlifecolor' ); ?></h4>

		<!-- OG Title -->
		<div class="seo-nlc-field">
			<label for="seo_nlc_og_title">
				<?php esc_html_e( 'OG Title', 'seo-costom-newlifecolor' ); ?>
			</label>
			<input type="text"
				   id="seo_nlc_og_title"
				   name="seo_nlc_og_title"
				   value="<?php echo esc_attr( $meta['og_title'] ); ?>"
				   class="large-text"
				   placeholder="<?php esc_attr_e( 'Zostaw puste, aby użyć tytułu SEO', 'seo-costom-newlifecolor' ); ?>" />
		</div>

		<!-- OG Description -->
		<div class="seo-nlc-field">
			<label for="seo_nlc_og_description">
				<?php esc_html_e( 'OG Description', 'seo-costom-newlifecolor' ); ?>
			</label>
			<textarea id="seo_nlc_og_description"
					  name="seo_nlc_og_description"
					  rows="2"
					  class="large-text"
					  placeholder="<?php esc_attr_e( 'Zostaw puste, aby użyć meta opisu', 'seo-costom-newlifecolor' ); ?>"><?php echo esc_textarea( $meta['og_description'] ); ?></textarea>
		</div>

		<!-- OG Image -->
		<div class="seo-nlc-field">
			<label><?php esc_html_e( 'OG Image', 'seo-costom-newlifecolor' ); ?></label>
			<?php
			$og_image_url = '';
			if ( ! empty( $meta['og_image'] ) ) {
				$og_image_url = wp_get_attachment_image_url( (int) $meta['og_image'], 'medium' );
			}
			?>
			<div class="seo-nlc-media-field" data-name="seo_nlc_og_image">
				<input type="hidden"
					   id="seo_nlc_og_image"
					   name="seo_nlc_og_image"
					   value="<?php echo esc_attr( $meta['og_image'] ); ?>" />

				<div class="seo-nlc-media-preview" <?php echo $og_image_url ? '' : 'style="display:none;"'; ?>>
					<img src="<?php echo esc_url( $og_image_url ); ?>" alt="" style="max-width: 200px; height: auto;" />
				</div>

				<p>
					<button type="button" class="button seo-nlc-media-upload">
						<?php esc_html_e( 'Wybierz obraz', 'seo-costom-newlifecolor' ); ?>
					</button>
					<button type="button" class="button seo-nlc-media-remove" <?php echo $og_image_url ? '' : 'style="display:none;"'; ?>>
						<?php esc_html_e( 'Usuń', 'seo-costom-newlifecolor' ); ?>
					</button>
				</p>
				<p class="description">
					<?php esc_html_e( 'Zalecany rozmiar: 1200x630px', 'seo-costom-newlifecolor' ); ?>
				</p>
			</div>
		</div>

		<hr />

		<h4><?php esc_html_e( 'Twitter Card', 'seo-costom-newlifecolor' ); ?></h4>

		<!-- Twitter Card Type -->
		<div class="seo-nlc-field">
			<label for="seo_nlc_twitter_card">
				<?php esc_html_e( 'Typ karty', 'seo-costom-newlifecolor' ); ?>
			</label>
			<select id="seo_nlc_twitter_card" name="seo_nlc_twitter_card" class="regular-text">
				<?php foreach ( $twitter_types as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $meta['twitter_card'], $value ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<!-- Twitter Title -->
		<div class="seo-nlc-field">
			<label for="seo_nlc_twitter_title">
				<?php esc_html_e( 'Twitter Title', 'seo-costom-newlifecolor' ); ?>
			</label>
			<input type="text"
				   id="seo_nlc_twitter_title"
				   name="seo_nlc_twitter_title"
				   value="<?php echo esc_attr( $meta['twitter_title'] ); ?>"
				   class="large-text"
				   placeholder="<?php esc_attr_e( 'Zostaw puste, aby użyć OG Title', 'seo-costom-newlifecolor' ); ?>" />
		</div>

		<!-- Twitter Description -->
		<div class="seo-nlc-field">
			<label for="seo_nlc_twitter_description">
				<?php esc_html_e( 'Twitter Description', 'seo-costom-newlifecolor' ); ?>
			</label>
			<textarea id="seo_nlc_twitter_description"
					  name="seo_nlc_twitter_description"
					  rows="2"
					  class="large-text"
					  placeholder="<?php esc_attr_e( 'Zostaw puste, aby użyć OG Description', 'seo-costom-newlifecolor' ); ?>"><?php echo esc_textarea( $meta['twitter_description'] ); ?></textarea>
		</div>

		<!-- Twitter Image -->
		<div class="seo-nlc-field">
			<label><?php esc_html_e( 'Twitter Image', 'seo-costom-newlifecolor' ); ?></label>
			<?php
			$twitter_image_url = '';
			if ( ! empty( $meta['twitter_image'] ) ) {
				$twitter_image_url = wp_get_attachment_image_url( (int) $meta['twitter_image'], 'medium' );
			}
			?>
			<div class="seo-nlc-media-field" data-name="seo_nlc_twitter_image">
				<input type="hidden"
					   id="seo_nlc_twitter_image"
					   name="seo_nlc_twitter_image"
					   value="<?php echo esc_attr( $meta['twitter_image'] ); ?>" />

				<div class="seo-nlc-media-preview" <?php echo $twitter_image_url ? '' : 'style="display:none;"'; ?>>
					<img src="<?php echo esc_url( $twitter_image_url ); ?>" alt="" style="max-width: 200px; height: auto;" />
				</div>

				<p>
					<button type="button" class="button seo-nlc-media-upload">
						<?php esc_html_e( 'Wybierz obraz', 'seo-costom-newlifecolor' ); ?>
					</button>
					<button type="button" class="button seo-nlc-media-remove" <?php echo $twitter_image_url ? '' : 'style="display:none;"'; ?>>
						<?php esc_html_e( 'Usuń', 'seo-costom-newlifecolor' ); ?>
					</button>
				</p>
				<p class="description">
					<?php esc_html_e( 'Zostaw puste, aby użyć OG Image', 'seo-costom-newlifecolor' ); ?>
				</p>
			</div>
		</div>
	</div>
</div>
