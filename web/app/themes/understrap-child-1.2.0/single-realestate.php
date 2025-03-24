<?php get_header(); ?>

<main class="container my-5">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post();
		get_template_part( 'loop-templates/content', 'realestate' );
		?>
	<?php endwhile; endif; ?>
</main>

<?php get_footer(); ?>
