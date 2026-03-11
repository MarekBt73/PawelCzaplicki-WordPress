<?php

declare(strict_types=1);

get_header();

?>
<section class="pc-snap-section pc-reveal" aria-label="<?php esc_attr_e( 'Wpis blogowy', 'pawelczaplicki' ); ?>">
	<div class="pc-snap-inner">
		<div class="pc-snap-content">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					?>
					<article <?php post_class( 'pc-post' ); ?>>
						<header class="pc-section-header">
							<p class="pc-kicker">
								<?php esc_html_e( 'Blog', 'pawelczaplicki' ); ?>
							</p>
							<h1 class="pc-h2">
								<?php the_title(); ?>
							</h1>
							<p class="pc-contact-note">
								<?php echo esc_html( get_the_date() ); ?>
								<?php
								$categories = get_the_category();
								if ( ! empty( $categories ) ) :
									?>
									&nbsp;&middot;&nbsp;
									<?php echo esc_html( $categories[0]->name ); ?>
								<?php endif; ?>
							</p>
						</header>

						<div class="pc-body">
							<?php the_content(); ?>
						</div>
					</article>
					<?php
				endwhile;
			endif;
			?>
		</div>
	</div>
</section>

<?php

get_footer();

?>

