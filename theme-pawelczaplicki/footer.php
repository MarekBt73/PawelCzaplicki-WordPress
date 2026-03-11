<?php

declare(strict_types=1);

?>
</main>
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
<?php wp_footer(); ?>
</body>
</html>

