<?php
$districts = get_terms( [
	'taxonomy'   => 'district',
	'hide_empty' => false,
] );

?>
<form id="realestate-filter-form" method="get" class="realestate-form-stacked">
	<div class="mb-3">
		<label class="form-label"><?php _e( 'Район', 'realestate-api-domain' ); ?></label>
		<select name="district" class="form-select">
			<option value=""><?php _e( 'Всі райони', 'realestate-api-domain' ); ?></option>
			<?php foreach ( $districts as $district ) : ?>
				<option value="<?php echo esc_attr( $district->slug ); ?>">
					<?php echo esc_html( $district->name ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</div>

	<div class="mb-3">
		<label class="form-label"><?php _e( 'Екологічність', 'realestate-api-domain' ); ?></label>
		<select name="ecology" class="form-select">
			<option value=""><?php _e( 'Будь-яка', 'realestate-api-domain' ); ?></option>
			<?php for ( $i = 1; $i <= 5; $i ++ ) : ?>
				<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
			<?php endfor; ?>
		</select>
	</div>

	<div class="mb-3">
		<label class="form-label"><?php _e( 'Поверхів', 'realestate-api-domain' ); ?></label>
		<select name="floors" class="form-select">
			<option value=""><?php _e( 'Будь-яка', 'realestate-api-domain' ); ?></option>
			<?php for ( $i = 1; $i <= 20; $i ++ ) : ?>
				<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
			<?php endfor; ?>
		</select>
	</div>

	<div class="mb-3">
		<label class="form-label"><?php _e( 'Тип будівлі', 'realestate-api-domain' ); ?></label>
		<select name="type" class="form-select">
			<option value=""><?php _e( 'Будь-який', 'realestate-api-domain' ); ?></option>
			<option value="панель">панель</option>
			<option value="цегла">цегла</option>
			<option value="піноблок">піноблок</option>
		</select>
	</div>

	<div class="d-grid">
		<button type="submit" class="btn btn-primary">
			<?php _e( 'Знайти', 'realestate-api-domain' ); ?>
		</button>
	</div>
</form>

