<?php

declare(strict_types=1);

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<header class="pc-header pc-container pl-6">
	<div class="pc-logo">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
			Czaplicki<span style="color:var(--pc-accent)">’</span>
		</a>
	</div>
	<button type="button" class="pc-nav-toggle" aria-label="<?php esc_attr_e( 'Przełącz menu', 'pawelczaplicki' ); ?>" aria-expanded="false">
		<span></span>
		<span></span>
	</button>
	<nav class="pc-nav" aria-label="<?php esc_attr_e( 'Główne menu', 'pawelczaplicki' ); ?>">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'pc-nav-list',
				'fallback_cb'    => false,
			)
		);
		?>
	</nav>
</header>
<main class="pc-main">

