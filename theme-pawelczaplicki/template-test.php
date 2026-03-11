<?php

declare(strict_types=1);

/**
 * Template Name: Test / Ankieta
 */

get_header();

?>
<section class="pc-container pc-page-test">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class(); ?>>
				<header class="pc-section">
					<h1 class="pc-hero-title"><?php the_title(); ?></h1>
				</header>
				<div class="pc-section">
					<?php the_content(); ?>
				</div>

				<section class="pc-section pc-section--survey">
					<script
						defer
						type="text/javascript"
						src="https://app.frontlead.io/js/embed-form.min.js?v=1772980203381"
						data-iframe-script="true"
						data-form-id="69a94df47c901391a15f8302"></script>
					<style type="text/css">
						.form-iframe {
							background: #fff;
							display: flex;
							position: relative;
						}
						.form-iframe__element {
							display: block;
							border: none;
							overflow: hidden;
							z-index: 10;
						}
						.loading-circle {
							position: absolute;
							top: 50%;
							left: 50%;
							transform: translate(-50%, -50%);
							height: 128px;
							width: 128px;
							color: '#eaeaea';
							z-index: 0;
						}
						.rotate-icon {
							animation: rotate 0.75s linear infinite;
							transform-origin: center;
						}
						@keyframes rotate {
							from {
								transform: rotate(0deg);
							}
							to {
								transform: rotate(360deg);
							}
						}
					</style>
					<div class="form-iframe">
						<iframe
							id="69a94df47c901391a15f8302"
							class="form-iframe__element"
							style="min-height:300px;opacity:0;"
							width="100%"
							height="100%"
							data-scroll-offset="70"
							frameborder="0"
							scrolling="no"
							allowTransparency="true"
							src="https://app.frontlead.io/form/69a94df47c901391a15f8302/view"></iframe>
						<div class="loading-circle">
							<svg viewBox="0 0 24 24" class="rotate-icon" aria-hidden="true" focusable="false">
								<path fill="#eaeaea" d="M10.72,19.9a8,8,0,0,1-6.5-9.79A7.77,7.77,0,0,1,10.4,4.16a8,8,0,0,1,9.49,6.52A1.54,1.54,0,0,0,21.38,12h.13a1.37,1.37,0,0,0,1.38-1.54,11,11,0,1,0-12.7,12.39A1.54,1.54,0,0,0,12,21.34h0A1.47,1.47,0,0,0,10.72,19.9Z"></path>
							</svg>
						</div>
					</div>
				</section>
			</article>
			<?php
		endwhile;
	endif;
	?>
</section>

<?php

get_footer();

