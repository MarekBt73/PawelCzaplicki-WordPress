<?php

declare(strict_types=1);

/**
 * Główny szablon motywu.
 * Używany jako fallback, gdy nie ma bardziej specyficznego pliku szablonu.
 */

get_header();

?>
<section class="pc-container">
	<?php if ( have_posts() ) : ?>
		<?php if ( is_home() && ! is_front_page() ) : ?>
			<header class="pc-section">
				<h1 class="pc-section-title">
					<?php single_post_title(); ?>
				</h1>
			</header>
		<?php endif; ?>

		<div class="pc-section">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class(); ?>>
					<h2>
						<a href="<?php the_permalink(); ?>">
							<?php the_title(); ?>
						</a>
					</h2>
					<div>
						<?php the_excerpt(); ?>
					</div>
				</article>
				<?php
			endwhile;

			the_posts_pagination();
			?>
		</div>
	<?php else : ?>
		<div class="pc-section">
			<p><?php esc_html_e( 'Brak treści do wyświetlenia.', 'pawelczaplicki' ); ?></p>
		</div>
	<?php endif; ?>
</section>

<?php

get_footer();

