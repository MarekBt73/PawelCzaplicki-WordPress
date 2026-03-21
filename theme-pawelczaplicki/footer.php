<?php

declare(strict_types=1);

?>
</main>

<footer class="footer-unified">
	<div class="footer-unified__inner">
		<div class="footer-unified__brand">
			<a href="<?php echo esc_url( home_url( '/protokol-17-00/' ) ); ?>" class="footer-unified__logo">
				Protokół <span class="footer-unified__logo-accent">17:00™</span>
			</a>
			<div class="footer-unified__tagline">
				<?php esc_html_e( 'Doradztwo Strategiczne', 'pawelczaplicki' ); ?>
			</div>
		</div>
		<nav class="footer-unified__nav" aria-label="<?php esc_attr_e( 'Menu w stopce', 'pawelczaplicki' ); ?>">
			<?php
			if ( has_nav_menu( 'footer' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'footer-unified__menu',
						'fallback_cb'    => false,
						'link_before'    => '',
						'link_after'     => '',
						'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
					)
				);
			} else {
				?>
				<ul class="footer-unified__menu">
					<li><a href="<?php echo esc_url( home_url( '/polityka-prywatnosci/' ) ); ?>"><?php esc_html_e( 'Polityka Prywatności', 'pawelczaplicki' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/kontakt/' ) ); ?>"><?php esc_html_e( 'Kontakt', 'pawelczaplicki' ); ?></a></li>
				</ul>
				<?php
			}
			?>
		</nav>
		<div class="footer-unified__copy">
			<p class="footer-unified__copy-line">&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php esc_html_e( 'Wszelkie prawa zastrzeżone.', 'pawelczaplicki' ); ?></p>
			<p class="footer-unified__copy-line"><?php esc_html_e( 'Metodologia RMP® i Protokół 17:00™', 'pawelczaplicki' ); ?></p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
