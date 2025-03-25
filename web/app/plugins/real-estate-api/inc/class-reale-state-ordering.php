<?php
/**
 * Custom ordering realestate posts by 'environmental_friendliness'
 */

class RealEstate_Ordering {

	public function __construct() {
		add_action( 'pre_get_posts', [ $this, 'modify_realestate_query' ] );
	}

	public function modify_realestate_query( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		if ( is_post_type_archive( 'realestate' ) ) {
			$query->set( 'meta_key', 'environmental_friendliness' );
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'order', 'DESC' );
		}
	}
}
