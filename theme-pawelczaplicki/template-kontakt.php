<?php

declare(strict_types=1);

/**
 * Template Name: Kontakt
 * Description: Strona kontaktowa z obszarem widgetów (social media, telefon, e-mail itd.).
 */

get_header();

?>
<section class="pc-container pc-contact-page">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class(); ?>>
				<header class="pc-contact-page__header">
					<h1 class="pc-hero-title"><?php the_title(); ?></h1>
				</header>

				<div class="pc-contact-page__grid">
					<div class="pc-contact-page__content">
						<?php the_content(); ?>
					</div>

					<aside class="pc-contact-page__aside" aria-label="<?php esc_attr_e( 'Dane kontaktowe', 'pawelczaplicki' ); ?>">
						<?php if ( is_active_sidebar( 'pawelczaplicki-contact-widgets' ) ) : ?>
							<?php dynamic_sidebar( 'pawelczaplicki-contact-widgets' ); ?>
						<?php else : ?>
							<section class="pc-widget">
								<h3 class="pc-widget-title"><?php esc_html_e( 'Kontakt', 'pawelczaplicki' ); ?></h3>
								<p class="pc-widget-text">
									<?php esc_html_e( 'Dodaj widgety w: Wygląd → Widgety → Kontakt – widgety.', 'pawelczaplicki' ); ?>
								</p>
							</section>
						<?php endif; ?>
					</aside>
				</div>
			</article>
			<?php
		endwhile;
	endif;
	?>
</section>

<?php

get_footer();

