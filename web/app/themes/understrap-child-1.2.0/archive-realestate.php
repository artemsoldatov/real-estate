<?php
/**
 * Archive page for Real Estate objects
 */

defined( 'ABSPATH' ) || exit;

get_header();
$container = get_theme_mod( 'understrap_container_type' );
?>

<div class="wrapper" id="archive-wrapper">
	<div class="<?php echo esc_attr( $container ); ?> py-5" id="content" tabindex="-1">

		<h1 class="mb-4 text-center"><?php post_type_archive_title(); ?></h1>

		<?php if ( have_posts() ) : ?>
			<div class="row">
				<?php while ( have_posts() ) : the_post(); ?>
					<div class="col-md-4 mb-4">
						<div class="card h-100">
							<?php
							$image = get_field( 'image' );
							if ( $image && isset( $image['url'] ) ) :
								echo '<img src="' . esc_url( $image['url'] ) . '" class="card-img-top" alt="">';
							endif;
							?>
							<div class="card-body">
								<h5 class="card-title"><?php the_title(); ?></h5>
								<?php if ( $name = get_field( 'house_name' ) ) : ?>
									<p><strong>Назва:</strong> <?php echo esc_html( $name ); ?></p>
								<?php endif; ?>
								<a href="<?php the_permalink(); ?>" class="btn btn-sm btn-outline-primary">Переглянути</a>
							</div>
						</div>
					</div>
				<?php endwhile; ?>
			</div>

			<div class="mt-4 text-center">
				<?php understrap_pagination(); ?>
			</div>

		<?php else : ?>
			<p class="text-muted">Об'єктів не знайдено</p>
		<?php endif; ?>

	</div><!-- #content -->
</div><!-- #archive-wrapper -->

<?php get_footer(); ?>
