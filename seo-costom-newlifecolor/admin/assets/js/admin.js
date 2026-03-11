/**
 * SEO Custom NewLifeColor - Admin JavaScript
 *
 * @package SeoCustomNLC
 * @since   1.0.0
 */

(function($) {
	'use strict';

	/**
	 * SEO NLC Admin Module
	 */
	const SeoNlcAdmin = {
		/**
		 * Configuration
		 */
		config: {
			maxTitleLength: 60,
			maxDescriptionLength: 160,
			titleWarning: 50,
			descWarning: 140
		},

		/**
		 * Initialize
		 */
		init: function() {
			// Get config from localized script
			if (typeof seoNlcMetaBox !== 'undefined') {
				this.config.maxTitleLength = seoNlcMetaBox.maxTitleLength || 60;
				this.config.maxDescriptionLength = seoNlcMetaBox.maxDescriptionLength || 160;
			}

			this.initTabs();
			this.initCharacterCounters();
			this.initPreview();
			this.initMediaUploaders();
			this.initConditionalFields();
			this.initRepeaters();
			this.initOpeningHours();
		},

		/**
		 * Initialize tabs
		 */
		initTabs: function() {
			$(document).on('click', '.seo-nlc-tab', function(e) {
				e.preventDefault();

				const $tab = $(this);
				const tabId = $tab.data('tab');
				const $container = $tab.closest('.seo-nlc-meta-box');

				// Switch active tab
				$container.find('.seo-nlc-tab').removeClass('active');
				$tab.addClass('active');

				// Switch active content
				$container.find('.seo-nlc-tab-content').removeClass('active');
				$container.find('#seo-nlc-tab-' + tabId).addClass('active');
			});
		},

		/**
		 * Initialize character counters
		 */
		initCharacterCounters: function() {
			const self = this;

			// Title counter
			$('#seo_nlc_title').on('input keyup', function() {
				self.updateCounter(
					$(this),
					'#seo-nlc-title-count',
					'#seo-nlc-title-status',
					self.config.maxTitleLength,
					self.config.titleWarning
				);
				self.updatePreview();
			});

			// Description counter
			$('#seo_nlc_description').on('input keyup', function() {
				self.updateCounter(
					$(this),
					'#seo-nlc-desc-count',
					'#seo-nlc-desc-status',
					self.config.maxDescriptionLength,
					self.config.descWarning
				);
				self.updatePreview();
			});

			// Trigger initial count
			$('#seo_nlc_title, #seo_nlc_description').trigger('input');
		},

		/**
		 * Update character counter
		 */
		updateCounter: function($input, countSelector, statusSelector, maxLength, warningThreshold) {
			const length = $input.val().length;
			const $count = $(countSelector);
			const $status = $(statusSelector);

			// Update count
			$count.text(length);

			// Remove all classes
			$count.removeClass('good caution warning');

			// Add appropriate class
			if (length > maxLength) {
				$count.addClass('warning');
				$status.text('- za długi');
			} else if (length > warningThreshold) {
				$count.addClass('caution');
				$status.text('- prawie optymalny');
			} else if (length > 0) {
				$count.addClass('good');
				$status.text('- optymalny');
			} else {
				$status.text('');
			}
		},

		/**
		 * Initialize Google preview
		 */
		initPreview: function() {
			this.updatePreview();
		},

		/**
		 * Update Google preview
		 */
		updatePreview: function() {
			const $titleInput = $('#seo_nlc_title');
			const $descInput = $('#seo_nlc_description');
			const $previewTitle = $('#seo-nlc-preview-title');
			const $previewDesc = $('#seo-nlc-preview-description');

			// Get values
			let title = $titleInput.val();
			let desc = $descInput.val();

			// Fallback to placeholder
			if (!title) {
				title = $titleInput.attr('placeholder') || 'Tytuł strony';
			}

			if (!desc) {
				desc = $descInput.attr('placeholder') || 'Opis strony...';
			}

			// Update preview
			$previewTitle.text(title);
			$previewDesc.text(desc);
		},

		/**
		 * Initialize media uploaders
		 */
		initMediaUploaders: function() {
			const self = this;

			// Upload button
			$(document).on('click', '.seo-nlc-media-upload', function(e) {
				e.preventDefault();

				const $container = $(this).closest('.seo-nlc-media-field');
				const $input = $container.find('input[type="hidden"]');
				const $preview = $container.find('.seo-nlc-media-preview');
				const $removeBtn = $container.find('.seo-nlc-media-remove');

				// Create media frame
				const frame = wp.media({
					title: self.getString('selectImage'),
					button: {
						text: self.getString('useImage')
					},
					multiple: false
				});

				// Handle selection
				frame.on('select', function() {
					const attachment = frame.state().get('selection').first().toJSON();

					// Update hidden input
					$input.val(attachment.id);

					// Update preview
					let imageUrl = attachment.url;
					if (attachment.sizes && attachment.sizes.medium) {
						imageUrl = attachment.sizes.medium.url;
					}

					$preview.find('img').attr('src', imageUrl);
					$preview.show();
					$removeBtn.show();
				});

				frame.open();
			});

			// Remove button
			$(document).on('click', '.seo-nlc-media-remove', function(e) {
				e.preventDefault();

				const $container = $(this).closest('.seo-nlc-media-field');
				const $input = $container.find('input[type="hidden"]');
				const $preview = $container.find('.seo-nlc-media-preview');

				$input.val('');
				$preview.hide();
				$(this).hide();
			});
		},

		/**
		 * Initialize conditional fields
		 */
		initConditionalFields: function() {
			const self = this;

			// Schema type change
			$('#seo_nlc_schema_type').on('change', function() {
				self.toggleConditionalFields();
			});

			// Initial toggle
			this.toggleConditionalFields();
		},

		/**
		 * Toggle conditional fields based on schema type
		 */
		toggleConditionalFields: function() {
			const schemaType = $('#seo_nlc_schema_type').val();

			$('.seo-nlc-conditional').each(function() {
				const $field = $(this);
				const showWhen = $field.data('show-when');
				const showValue = $field.data('show-value');

				if (showWhen === 'seo_nlc_schema_type') {
					if (showValue === schemaType) {
						$field.addClass('visible');
					} else {
						$field.removeClass('visible');
					}
				}
			});
		},

		/**
		 * Initialize repeater fields
		 */
		initRepeaters: function() {
			const self = this;

			// Add service
			$(document).on('click', '#seo-nlc-add-service', function(e) {
				e.preventDefault();
				self.addRepeaterRow('#seo-nlc-service-template', '#seo-nlc-services-body');
			});

			// Remove service
			$(document).on('click', '.seo-nlc-remove-service', function(e) {
				e.preventDefault();
				if (confirm(self.getString('confirmRemove'))) {
					$(this).closest('tr').remove();
				}
			});

			// Add FAQ
			$(document).on('click', '#seo-nlc-add-faq', function(e) {
				e.preventDefault();
				self.addFaqRow();
			});

			// Remove FAQ
			$(document).on('click', '.seo-nlc-remove-faq', function(e) {
				e.preventDefault();
				if (confirm(self.getString('confirmRemove'))) {
					$(this).closest('.seo-nlc-faq-item').remove();
				}
			});
		},

		/**
		 * Add repeater row
		 */
		addRepeaterRow: function(templateSelector, containerSelector) {
			const template = $(templateSelector).html();
			const $container = $(containerSelector);
			const index = $container.children().length;

			const html = template.replace(/\{\{INDEX\}\}/g, index);
			$container.append(html);
		},

		/**
		 * Add FAQ row
		 */
		addFaqRow: function() {
			const template = $('#seo-nlc-faq-template').html();
			const $container = $('#seo-nlc-faq-items');
			const index = $container.children().length;

			const html = template.replace(/\{\{INDEX\}\}/g, index);
			$container.append(html);
		},

		/**
		 * Initialize opening hours
		 */
		initOpeningHours: function() {
			const $container = $('.seo-nlc-opening-hours');
			if (!$container.length) return;

			const $hiddenInput = $container.find('input[type="hidden"]');

			// Update JSON on any change
			$container.find('input').on('change', function() {
				const hours = {};

				$container.find('tbody tr').each(function() {
					const $row = $(this);
					const day = $row.find('input[type="checkbox"]').attr('name').match(/\[(\w+)\]/)[1];

					hours[day] = {
						open: $row.find('input[type="checkbox"]').is(':checked'),
						opens: $row.find('input[name*="[opens]"]').val(),
						closes: $row.find('input[name*="[closes]"]').val()
					};
				});

				$hiddenInput.val(JSON.stringify(hours));
			});

			// Trigger initial update
			$container.find('input:first').trigger('change');
		},

		/**
		 * Get localized string
		 */
		getString: function(key) {
			if (typeof seoNlcAdmin !== 'undefined' && seoNlcAdmin.strings && seoNlcAdmin.strings[key]) {
				return seoNlcAdmin.strings[key];
			}
			if (typeof seoNlcMetaBox !== 'undefined' && seoNlcMetaBox.strings && seoNlcMetaBox.strings[key]) {
				return seoNlcMetaBox.strings[key];
			}

			// Fallbacks
			const fallbacks = {
				selectImage: 'Wybierz obraz',
				useImage: 'Użyj tego obrazu',
				confirmRemove: 'Czy na pewno chcesz usunąć?'
			};

			return fallbacks[key] || key;
		}
	};

	// Initialize on document ready
	$(document).ready(function() {
		SeoNlcAdmin.init();
	});

	// Gutenberg compatibility - reinit on editor ready
	if (typeof wp !== 'undefined' && wp.domReady) {
		wp.domReady(function() {
			// Delay to ensure meta box is loaded
			setTimeout(function() {
				SeoNlcAdmin.init();
			}, 500);
		});
	}

})(jQuery);
