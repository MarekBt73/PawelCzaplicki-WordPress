<?php

declare(strict_types=1);

?>
</main>

<?php if ( is_front_page() ) : ?>
	<footer class="bg-brand-light py-16 border-t border-gray-200">
		<div class="max-w-7xl mx-auto px-6 md:px-12 flex flex-col md:flex-row justify-between items-start md:items-center">
			<div class="mb-8 md:mb-0">
				<div class="text-2xl font-semi-expanded font-bold text-brand-dark mb-2">
					Protokół <span class="text-brand-red">17:00™</span>
				</div>
				<div class="text-sm font-bold text-gray-400 uppercase tracking-widest">
					Doradztwo Strategiczne
				</div>
			</div>
			<div class="text-sm text-gray-400 md:text-right">
				<p class="mb-2">&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> Wszelkie prawa zastrzeżone.</p>
				<p>Metodologia RMP® i Protokół 17:00™</p>
			</div>
		</div>
	</footer>
<?php else : ?>
	<footer class="pc-footer">
		<div class="pc-container">
			<div>
				<?php
				printf(
					'&copy; %s %s',
					esc_html( date_i18n( 'Y' ) ),
					esc_html( get_bloginfo( 'name' ) )
				);
				?>
			</div>
			<nav class="pc-nav-footer" aria-label="<?php esc_attr_e( 'Menu w stopce', 'pawelczaplicki' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'pc-nav-list pc-nav-list--footer',
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
		</div>
	</footer>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
