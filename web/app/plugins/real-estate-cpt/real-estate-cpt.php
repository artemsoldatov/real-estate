<?php
/*
Plugin Name: Real Estate CPT
Description: Register custom post type and taxonomy
Version: 1.0
Author: Artem Soldatov
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'REALESTATE_CPT_DOMAIN' ) ) {
	define( 'REALESTATE_CPT_DOMAIN', 'realestate-cpt-domain' );
}

// Register Post Type
function register_realestate_post_type(): void {
	$labels = array(
		'name'               => __( 'Об’єкти нерухомості', REALESTATE_CPT_DOMAIN ),
		'singular_name'      => __( 'Об’єкт нерухомості', REALESTATE_CPT_DOMAIN ),
		'add_new'            => __( 'Додати новий', REALESTATE_CPT_DOMAIN ),
		'add_new_item'       => __( 'Додати новий об’єкт', REALESTATE_CPT_DOMAIN ),
		'edit_item'          => __( 'Редагувати об’єкт', REALESTATE_CPT_DOMAIN ),
		'new_item'           => __( 'Новий об’єкт', REALESTATE_CPT_DOMAIN ),
		'view_item'          => __( 'Переглянути об’єкт', REALESTATE_CPT_DOMAIN ),
		'all_items'          => __( 'Всі об’єкти', REALESTATE_CPT_DOMAIN ),
		'search_items'       => __( 'Шукати об’єкт', REALESTATE_CPT_DOMAIN ),
		'not_found'          => __( 'Нічого не знайдено', REALESTATE_CPT_DOMAIN ),
		'not_found_in_trash' => __( 'Нічого не знайдено у кошику', REALESTATE_CPT_DOMAIN ),
		'menu_name'          => __( 'Об’єкти нерухомості', REALESTATE_CPT_DOMAIN ),
	);

	$args = array(
		'labels'       => $labels,
		'public'       => true,
		'has_archive'  => true,
		'rewrite'      => array( 'slug' => 'realestate' ),
		'menu_icon'    => 'dashicons-building',
		'supports'     => array( 'title' ),
		'show_in_rest' => true,
	);

	register_post_type( 'realestate', $args );
}

// Register taxonomy
function register_district_taxonomy(): void {
	$labels = array(
		'name'          => __( 'Райони', REALESTATE_CPT_DOMAIN ),
		'singular_name' => __( 'Район', REALESTATE_CPT_DOMAIN ),
		'search_items'  => __( 'Шукати район', REALESTATE_CPT_DOMAIN ),
		'all_items'     => __( 'Всі райони', REALESTATE_CPT_DOMAIN ),
		'parent_item'   => __( 'Батьківський район', REALESTATE_CPT_DOMAIN ),
		'edit_item'     => __( 'Редагувати район', REALESTATE_CPT_DOMAIN ),
		'update_item'   => __( 'Оновити район', REALESTATE_CPT_DOMAIN ),
		'add_new_item'  => __( 'Додати новий район', REALESTATE_CPT_DOMAIN ),
		'new_item_name' => __( 'Новий район', REALESTATE_CPT_DOMAIN ),
		'menu_name'     => __( 'Райони', REALESTATE_CPT_DOMAIN ),
	);

	$args = array(
		'labels'       => $labels,
		'hierarchical' => true,
		'public'       => true,
		'show_in_rest' => true,
		'rewrite'      => array( 'slug' => 'district' ),
	);

	register_taxonomy( 'district', array( 'realestate' ), $args );
}

add_action( 'init', 'register_realestate_post_type' );
add_action( 'init', 'register_district_taxonomy' );
