<?php

declare(strict_types=1);

/**
 * Szablon archiwum bloga (Baza Wiedzy).
 * Używany gdy w Ustawieniach > Odczyt ustawiono statyczną stronę główną i stronę wpisów.
 */

get_header();

?>

<!-- Linie tła -->
<div class="pc-archive-bg-lines" aria-hidden="true">
	<svg class="absolute w-full h-full" viewBox="0 0 1440 1024" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M-200,300 C400,100 800,800 1600,400" stroke="var(--pc-accent)" stroke-width="0.75" />
		<circle cx="850" cy="550" r="3.5" fill="var(--pc-accent)"/>
		<path d="M-100,800 C300,500 900,900 1600,700" stroke="var(--pc-accent)" stroke-width="0.75" />
	</svg>
</div>

<div class="pt-40 pb-24 lg:pt-48 pc-archive-main">
	<div class="max-w-7xl mx-auto px-6 md:px-12">

		<!-- Hero sekcja -->
		<div class="mb-20 max-w-4xl">
			<p class="pc-archive-kicker"><?php esc_html_e( 'Instynkt Decyzyjny™', 'pawelczaplicki' ); ?></p>
			<h1 class="pc-archive-title">
				<?php esc_html_e( 'Architektura decyzji', 'pawelczaplicki' ); ?>
				<br><span class="pc-archive-title-accent"><?php esc_html_e( 'w praktyce.', 'pawelczaplicki' ); ?></span>
			</h1>
			<p class="pc-archive-lead">
				<?php esc_html_e( 'Artykuły, analizy przypadków i sprawdzone strategie dla właścicieli firm, którzy chcą przestać być wąskim gardłem własnego biznesu.', 'pawelczaplicki' ); ?>
			</p>
		</div>

		<!-- Filtry kategorii -->
		<?php
		$categories = get_categories( array( 'hide_empty' => true ) );
		$current_cat = is_category() ? get_queried_object_id() : 0;
		if ( ! empty( $categories ) ) :
			?>
			<div class="pc-archive-filters">
				<?php
				$posts_page_id = get_option( 'page_for_posts' );
				$blog_url      = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/' );
				?>
				<a href="<?php echo esc_url( $blog_url ); ?>" class="pc-archive-filter-btn pc-archive-filter-btn--active">
					<?php esc_html_e( 'Wszystkie', 'pawelczaplicki' ); ?>
				</a>
				<?php foreach ( $categories as $cat ) : ?>
					<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="pc-archive-filter-btn">
						<?php echo esc_html( $cat->name ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>

			<?php
			$post_index = 0;
			while ( have_posts() ) :
				the_post();
				$post_index++;

				if ( 1 === $post_index ) :
					// Wyróżniony artykuł (pierwszy)
					$first_cat = get_the_category();
					$first_cat_name = ! empty( $first_cat ) ? $first_cat[0]->name : '';
					?>
					<article class="pc-archive-featured group">
						<a href="<?php the_permalink(); ?>" class="pc-archive-featured-link">
							<div class="pc-archive-featured-grid">
								<div class="pc-archive-featured-media">
									<?php if ( has_post_thumbnail() ) : ?>
										<?php the_post_thumbnail( 'large', array( 'class' => 'pc-article-img', 'alt' => get_the_title() ) ); ?>
									<?php else : ?>
										<div class="pc-archive-placeholder">
											<i data-lucide="file-text" class="pc-archive-placeholder-icon" aria-hidden="true"></i>
										</div>
									<?php endif; ?>
									<span class="pc-archive-badge"><?php esc_html_e( 'Nowość', 'pawelczaplicki' ); ?></span>
								</div>
								<div class="pc-archive-featured-content">
									<div class="pc-archive-meta">
										<?php if ( $first_cat_name ) : ?>
											<span class="pc-archive-meta-cat"><?php echo esc_html( $first_cat_name ); ?></span>
											<span class="pc-archive-meta-sep">•</span>
										<?php endif; ?>
										<span><?php echo esc_html( get_the_date() ); ?></span>
									</div>
									<h2 class="pc-archive-featured-title"><?php the_title(); ?></h2>
									<p class="pc-archive-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
									<span class="pc-archive-read-more">
										<?php esc_html_e( 'Czytaj artykuł', 'pawelczaplicki' ); ?>
										<i data-lucide="arrow-right" class="pc-archive-arrow" aria-hidden="true"></i>
									</span>
								</div>
							</div>
						</a>
					</article>
					<?php
					continue;
				endif;
			endwhile;

			// Reset dla drugiej pętli (grid)
			rewind_posts();
			$post_index = 0;
			$has_grid_posts = false;
			?>

			<div class="pc-archive-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					$post_index++;
					if ( 1 === $post_index ) {
						continue; // Pomijamy wyróżniony
					}
					$has_grid_posts = true;
					$grid_cat = get_the_category();
					$grid_cat_name = ! empty( $grid_cat ) ? $grid_cat[0]->name : '';
					?>
					<article class="pc-archive-card group">
						<a href="<?php the_permalink(); ?>" class="pc-archive-card-link">
							<div class="pc-archive-card-media">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'medium_large', array( 'class' => 'pc-article-img', 'alt' => get_the_title() ) ); ?>
								<?php else : ?>
									<div class="pc-archive-placeholder pc-archive-placeholder--card">
										<i data-lucide="file-text" class="pc-archive-placeholder-icon" aria-hidden="true"></i>
									</div>
								<?php endif; ?>
							</div>
							<div class="pc-archive-card-meta">
								<?php if ( $grid_cat_name ) : ?>
									<span class="pc-archive-meta-cat"><?php echo esc_html( $grid_cat_name ); ?></span>
									<span class="pc-archive-meta-sep">•</span>
								<?php endif; ?>
								<span><?php echo esc_html( get_the_date() ); ?></span>
							</div>
							<h3 class="pc-archive-card-title"><?php the_title(); ?></h3>
							<p class="pc-archive-card-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
							<span class="pc-archive-read-more pc-archive-read-more--sm">
								<?php esc_html_e( 'Czytaj dalej', 'pawelczaplicki' ); ?>
								<i data-lucide="arrow-right" class="pc-archive-arrow pc-archive-arrow--sm" aria-hidden="true"></i>
							</span>
						</a>
					</article>
					<?php
				endwhile;
				?>
			</div>

			<?php
			global $wp_query;
			$next_url = get_next_posts_page_link( $wp_query->max_num_pages );
			if ( $next_url ) :
					?>
					<div class="pc-archive-pagination">
						<a href="<?php echo esc_url( $next_url ); ?>" class="pc-archive-load-more">
							<?php esc_html_e( 'Załaduj starsze wpisy', 'pawelczaplicki' ); ?>
						</a>
					</div>
			<?php endif; ?>

		<?php else : ?>
			<p class="pc-archive-empty"><?php esc_html_e( 'Brak wpisów do wyświetlenia.', 'pawelczaplicki' ); ?></p>
		<?php endif; ?>

	</div>
</div>

<?php

get_footer();
