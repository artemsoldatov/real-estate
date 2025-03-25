<?php get_header(); ?>

<main class="container my-5">
	<div class="row">

		<div class="col-md-9">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post();
				get_template_part( 'loop-templates/content', 'realestate' );
				?>
			<?php endwhile; endif; ?>
		</div>

		<?php if ( is_active_sidebar( 'left-sidebar' ) ) : ?>
			<aside class="col-md-3">
				<?php dynamic_sidebar( 'left-sidebar' ); ?>
			</aside>
		<?php endif; ?>

	</div>
</main>

<?php get_footer(); ?>
