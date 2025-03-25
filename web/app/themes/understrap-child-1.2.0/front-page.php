<?php get_header(); ?>

<main class="container my-5">
	<h1 class="mb-4">Об'єкти нерухомості</h1>

	<div class="row">
		<div class="col-md-8 mb-5">
			<?php echo do_shortcode('[realestate_filter]'); ?>
		</div>

		<div class="col-md-4">
			<h3 class="mb-4">Останні пости</h3>

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<article class="mb-4">
					<h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
					<p class="small text-muted"><?php the_time('d.m.Y'); ?></p>
					<p><?php the_excerpt(); ?></p>
				</article>
			<?php endwhile; else : ?>
				<p>Постів не знайдено.</p>
			<?php endif; ?>
		</div>
	</div>
</main>


<?php get_footer(); ?>
