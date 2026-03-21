<?php

declare(strict_types=1);

/**
 * Szablon pojedynczego wpisu blogowego.
 */

get_header();

?>

<?php
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();

		$categories    = get_the_category();
		$first_cat     = ! empty( $categories ) ? $categories[0] : null;
		$posts_page_id = get_option( 'page_for_posts' );
		$blog_url      = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/' );
		$tags          = get_the_tags();
		$content       = get_the_content();
		$word_count    = ! empty( $content ) ? str_word_count( strip_tags( $content ) ) : 0;
		$reading_min   = max( 1, (int) ceil( $word_count / 200 ) );
		?>
		<article <?php post_class( 'pc-single-article' ); ?>>

			<header class="pc-single-header">
				<div class="pc-single-header-inner">
					<!-- Breadcrumbs / Meta -->
					<nav class="pc-single-breadcrumb" aria-label="<?php esc_attr_e( 'Nawigacja', 'pawelczaplicki' ); ?>">
						<a href="<?php echo esc_url( $blog_url ); ?>" class="pc-single-breadcrumb-link"><?php esc_html_e( 'Baza Wiedzy', 'pawelczaplicki' ); ?></a>
						<span class="pc-single-breadcrumb-sep">/</span>
						<?php if ( $first_cat ) : ?>
							<a href="<?php echo esc_url( get_category_link( $first_cat->term_id ) ); ?>" class="pc-single-breadcrumb-cat"><?php echo esc_html( $first_cat->name ); ?></a>
						<?php else : ?>
							<span class="pc-single-breadcrumb-cat"><?php esc_html_e( 'Blog', 'pawelczaplicki' ); ?></span>
						<?php endif; ?>
					</nav>

					<h1 class="pc-single-title"><?php the_title(); ?></h1>

					<?php if ( has_excerpt() ) : ?>
						<p class="pc-single-lead"><?php the_excerpt(); ?></p>
					<?php endif; ?>

					<!-- Autor i data -->
					<div class="pc-single-meta">
						<div class="pc-single-author">
							<?php echo get_avatar( get_the_author_meta( 'ID' ), 48, '', '', array( 'class' => 'pc-single-avatar' ) ); ?>
							<div>
								<span class="pc-single-author-name"><?php the_author(); ?></span>
								<span class="pc-single-author-role"><?php esc_html_e( 'Architekt Decyzji', 'pawelczaplicki' ); ?></span>
							</div>
						</div>
						<div class="pc-single-date">
							<span class="pc-single-date-value"><?php echo esc_html( get_the_date() ); ?></span>
							<span class="pc-single-reading-time"><?php printf( esc_html__( 'Czas czytania: %d min', 'pawelczaplicki' ), $reading_min ); ?></span>
						</div>
					</div>
				</div>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="pc-single-hero">
					<div class="pc-single-hero-inner">
						<?php the_post_thumbnail( 'full', array( 'class' => 'pc-single-hero-img', 'alt' => get_the_title() ) ); ?>
					</div>
					<?php
					$thumb_caption = get_the_post_thumbnail_caption();
					if ( $thumb_caption ) :
						?>
						<p class="pc-single-hero-caption"><?php echo esc_html( $thumb_caption ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="pc-single-content pc-article-content">
				<?php
				the_content();
				wp_link_pages(
					array(
						'before' => '<nav class="pc-single-pagination"><p>' . esc_html__( 'Strony:', 'pawelczaplicki' ) . ' ',
						'after'  => '</p></nav>',
					)
				);
				?>
			</div>

			<?php if ( $tags && ! is_wp_error( $tags ) ) : ?>
				<div class="pc-single-tags">
					<?php foreach ( $tags as $tag ) : ?>
						<a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="pc-single-tag"><?php echo esc_html( $tag->name ); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="pc-single-share">
				<span class="pc-single-share-label"><?php esc_html_e( 'Udostępnij:', 'pawelczaplicki' ); ?></span>
				<a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo rawurlencode( get_permalink() ); ?>&title=<?php echo rawurlencode( get_the_title() ); ?>" target="_blank" rel="noopener noreferrer" class="pc-single-share-link" aria-label="LinkedIn"><i data-lucide="linkedin" class="w-6 h-6" aria-hidden="true"></i></a>
				<a href="https://twitter.com/intent/tweet?url=<?php echo rawurlencode( get_permalink() ); ?>&text=<?php echo rawurlencode( get_the_title() ); ?>" target="_blank" rel="noopener noreferrer" class="pc-single-share-link" aria-label="X"><i data-lucide="twitter" class="w-6 h-6" aria-hidden="true"></i></a>
				<button type="button" class="pc-single-share-link pc-single-share-copy" data-url="<?php echo esc_url( get_permalink() ); ?>" aria-label="<?php esc_attr_e( 'Kopiuj link', 'pawelczaplicki' ); ?>"><i data-lucide="link-2" class="w-6 h-6" aria-hidden="true"></i></button>
			</div>

		</article>

		<!-- CTA sekcja (bez tła z linii) -->
		<section class="pc-single-cta">
			<div class="pc-single-cta-inner">
				<p class="pc-single-cta-kicker"><?php esc_html_e( 'Twój kolejny krok', 'pawelczaplicki' ); ?></p>
				<h2 class="pc-single-cta-title"><?php esc_html_e( 'Gotowy, by przestać być wąskim gardłem swojej firmy?', 'pawelczaplicki' ); ?></h2>
				<p class="pc-single-cta-text"><?php esc_html_e( 'Zarezerwuj wstępny audyt operacyjny. Sprawdzimy, czy Protokół 17:00™ jest rozwiązaniem, którego potrzebuje Twój zespół do przejęcia pełnej odpowiedzialności.', 'pawelczaplicki' ); ?></p>
				<a href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>" class="pc-single-cta-btn">
					<?php esc_html_e( 'Zapytaj o współpracę', 'pawelczaplicki' ); ?>
					<i data-lucide="arrow-right" class="pc-single-cta-icon" aria-hidden="true"></i>
				</a>
			</div>
		</section>

		<?php
	endwhile;
endif;
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
	var copyBtn = document.querySelector('.pc-single-share-copy');
	if (copyBtn) {
		copyBtn.addEventListener('click', function() {
			var url = this.getAttribute('data-url');
			if (url && navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(url).then(function() {
					copyBtn.setAttribute('aria-label', '<?php echo esc_js( __( 'Skopiowano!', 'pawelczaplicki' ) ); ?>');
					setTimeout(function() { copyBtn.setAttribute('aria-label', '<?php echo esc_js( __( 'Kopiuj link', 'pawelczaplicki' ) ); ?>'); }, 2000);
				});
			}
		});
	}
});
</script>

<?php

get_footer();
