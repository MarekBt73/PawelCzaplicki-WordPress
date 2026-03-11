<?php

declare(strict_types=1);

/**
 * Template Name: Badanie RMP
 */

get_header();

?>
<section class="pc-container pc-page-badanie">
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
			</article>
			<?php
		endwhile;
	endif;
	?>
</section>

<?php

get_footer();

