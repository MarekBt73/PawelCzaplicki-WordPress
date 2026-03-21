<?php

declare(strict_types=1);

get_header();

?>
<section class="pc-container pc-main-offset">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class(); ?>>
				<header>
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

