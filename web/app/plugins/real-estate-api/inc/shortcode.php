<?php
/**
 * RealEstate Filter Shortcode
 * Use: [realestate_filter]
 */

add_action( 'init', 'realestate_register_shortcode' );
function realestate_register_shortcode() {
	add_shortcode( 'realestate_filter', 'realestate_filter_shortcode' );
}

function realestate_filter_shortcode() {
	ob_start();

	include plugin_dir_path( __FILE__ ) . 'views/filter-form-shortcode.php';

	echo '<div id="realestate-results" class="mt-4"></div>';

	return ob_get_clean();
}
