<?php
/**
 * Single realestate post partial template
 */

defined( 'ABSPATH' ) || exit;

$id     = get_the_ID();
$coords = get_field( 'location_coordinates' );
?>

<article class="realestate-single" itemscope itemtype="https://schema.org/SingleFamilyResidence">
	<h1 class="mb-4" itemprop="name"><?php the_title(); ?></h1>

	<?php
	$img = get_field( 'image' );
	if ( $img && isset( $img['url'] ) ) :
		?>
		<li class="list-group-item">
			<img src="<?php echo esc_url( $img['url'] ); ?>"
				 alt="<?php echo esc_attr( $img['alt'] ?? '' ); ?>"
				 class="img-fluid mt-2 mb-4" width="600" height="auto"/>
		</li>
	<?php endif; ?>

	<ul class="list-group mb-4">
		<?php if ( $house_name = get_field( 'house_name' ) ) : ?>
			<li class="list-group-item">
				<strong><?php _e( 'Назва будинку', 'understrap-child' ); ?>
					:</strong> <?php echo esc_html( $house_name ); ?>
			</li>
		<?php endif; ?>

		<?php if ( $coords ): ?>
			<li class="list-group-item">
				<strong><?php _e( 'Координати', 'understrap-child' ); ?>:</strong> <?php echo esc_html( $coords ); ?>
			</li>
		<?php endif; ?>

		<?php if ( $floors = get_field( 'number_of_floors' ) ) : ?>
			<li class="list-group-item">
				<strong><?php _e( 'Кількість поверхів', 'understrap-child' ); ?>
					:</strong> <?php echo esc_html( $floors ); ?>
			</li>
		<?php endif; ?>

		<?php if ( $type = get_field( 'building_type' ) ) : ?>
			<li class="list-group-item">
				<strong><?php _e( 'Тип будівлі', 'understrap-child' ); ?>:</strong> <?php echo esc_html( $type ); ?>
			</li>
		<?php endif; ?>

		<?php if ( $eco = get_field( 'environmental_friendliness' ) ) : ?>
			<li class="list-group-item">
				<strong><?php _e( 'Екологічність', 'understrap-child' ); ?>:</strong> <?php echo esc_html( $eco ); ?>
			</li>
		<?php endif; ?>

		<?php
		$terms = get_the_terms( $id, 'district' );
		if ( $terms && ! is_wp_error( $terms ) ) :
			$names = wp_list_pluck( $terms, 'name' );
			?>
			<li class="list-group-item">
				<strong><?php _e( 'Район', 'understrap-child' ); ?>:</strong> <?php echo implode( ', ', $names ); ?>
			</li>
		<?php endif; ?>
	</ul>

	<?php if ( have_rows( 'premises' ) ) : ?>
		<h2 class="mb-3"><?php _e( 'Приміщення', 'understrap-child' ); ?></h2>
		<div class="row">
			<?php while ( have_rows( 'premises' ) ) : the_row(); ?>
				<div class="col-md-6 mb-4">
					<div class="card h-100">
						<div class="card-body">
							<p><strong><?php _e( 'Площа', 'understrap-child' ); ?>
									:</strong> <?php the_sub_field( 'area' ); ?> м²</p>
							<p><strong><?php _e( 'Кількість кімнат', 'understrap-child' ); ?>
									:</strong> <?php the_sub_field( 'number_of_rooms' ); ?></p>
							<p><strong><?php _e( 'Балкон', 'understrap-child' ); ?>
									:</strong> <?php the_sub_field( 'balcony' ); ?></p>
							<p><strong><?php _e( 'Санвузол', 'understrap-child' ); ?>
									:</strong> <?php the_sub_field( 'bathroom' ); ?></p>
						</div>
						<?php
						$room_img = get_sub_field( 'image' );
						if ( $room_img && isset( $room_img['url'] ) ) :
							echo '<img src="' . esc_url( $room_img['url'] ) . '" alt="' . esc_attr( $room_img['alt'] ?? '' ) . '" class="card-img-bottom" width="300" height="auto" />';
						endif;
						?>
					</div>
				</div>
			<?php endwhile; ?>
		</div>
	<?php endif; ?>
	<?php if ( $coords ): ?>
		<div class="mb-4">
			<h5><?php _e( 'Карта розташування', 'understrap-child' ); ?></h5>
			<iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $coords ); ?>&z=15&output=embed"
					width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
		</div>
	<?php endif; ?>

</article>
