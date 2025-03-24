<?php get_header(); ?>

<main class="container my-5">
	<h1 class="mb-4">Останні пости</h1>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<article class="mb-4">
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<p><?php the_excerpt(); ?></p>
		</article>
	<?php endwhile; else : ?>
		<p>Постів не знайдено.</p>
	<?php endif; ?>
</main>

<?php get_footer(); ?>
