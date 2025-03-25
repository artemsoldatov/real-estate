<?php
/**
 * Plugin Name: Real Estate API
 * Description: Provides REST API endpoints and UI for Real Estate CPT
 * Version: 2.1
 * Author: Artem Soldatov
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Autoload required files
require_once plugin_dir_path( __FILE__ ) . 'inc/class-real-estate-api.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/class-reale-state-ordering.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/shortcode.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/widget.php';

// Init core classes
new RealEstate_API();
new RealEstate_Ordering();

// Register shortcode
add_shortcode( 'realestate_filter', 'render_realestate_filter_form' );

// Register widget
add_action( 'widgets_init', function () {
	register_widget( 'RealEstate_Filter_Widget' );
} );

// Assets
function realestate_enqueue_scripts() {
	wp_enqueue_script(
		'realestate-filter-js',
		plugin_dir_url( __FILE__ ) . 'assets/realestate-filter.js',
		array( 'jquery' ),
		'2.1',
		true
	);
}

add_action( 'wp_enqueue_scripts', 'realestate_enqueue_scripts' );
