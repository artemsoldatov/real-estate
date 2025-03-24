<?php
if ( defined( 'WP_CLI' ) && WP_CLI ) {

	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );

	class RE_Seeder_Command extends WP_CLI_Command {

		/**
		 * Generate fish "realestate" posts
		 *
		 * Use it: wp re-seeder generate
		 */
		public function generate( $args, $assoc_args ) {

			$num_posts = 10;

			for ( $i = 1; $i <= $num_posts; $i ++ ) {

				// Create a post of type "realestate"
				$post_title = "Будинок #{$i}";
				$post_id    = wp_insert_post( array(
					'post_type'   => 'realestate',
					'post_title'  => $post_title,
					'post_status' => 'publish',
				) );

				if ( is_wp_error( $post_id ) ) {
					WP_CLI::error( "Error of creating: " . $post_id->get_error_message() );
					continue;
				}

				// Generate random values for fields
				$floors         = rand( 1, 20 );
				$building_types = array( 'панель', 'цегла', 'піноблок' );
				$building_type  = $building_types[ array_rand( $building_types ) ];
				$env_friendly   = rand( 1, 5 );

				// Generate random coordinates
				do {
					$lat = rand(443800, 523800) / 10000.0;
					$lng = rand(231000, 392000) / 10000.0;
				} while ( $lat < 45.0 && $lng > 33.5 );

				$coordinates = $lat . ", " . $lng;

				// Update ACF fields
				update_field( 'house_name', $post_title, $post_id );
				update_field( 'location_coordinates', $coordinates, $post_id );
				update_field( 'number_of_floors', $floors, $post_id );
				update_field( 'building_type', $building_type, $post_id );
				update_field( 'environmental_friendliness', $env_friendly, $post_id );

				// Load main image
				$main_image_url = "https://dummyimage.com/600x400/ccc/000.png&text=House+{$i}";
				$main_image_id  = media_sideload_image( $main_image_url, $post_id, null, 'id' );
				if ( ! is_wp_error( $main_image_id ) ) {
					update_field( 'image', $main_image_id, $post_id );
				} else {
					WP_CLI::warning( "Error of main image loading {$post_id}: " . $main_image_id->get_error_message() );
				}

				// Generate repeater data
				$premises = array();
				for ( $j = 1; $j <= 2; $j ++ ) {
					$area     = rand( 30, 150 );
					$rooms    = rand( 1, 10 );
					$balcony  = ( rand( 0, 1 ) === 1 ) ? 'так' : 'ні';
					$bathroom = ( rand( 0, 1 ) === 1 ) ? 'так' : 'ні';

					// Load image for each premise
					$room_image_url = "https://dummyimage.com/300x200/ddd/000.png&text=Room+{$j}";
					$room_image_id  = media_sideload_image( $room_image_url, $post_id, null, 'id' );
					if ( is_wp_error( $room_image_id ) ) {
						WP_CLI::warning( "Error image loading of premises {$post_id}, ряд {$j}: " . $room_image_id->get_error_message() );
						$room_image_id = 0;
					}

					$premises[] = array(
						'area'            => $area,
						'number_of_rooms' => $rooms,
						'balcony'         => $balcony,
						'bathroom'        => $bathroom,
						'image'           => $room_image_id,
					);
				}
				update_field( 'premises', $premises, $post_id );

				// Add taxonomy 'district'
				$districts = array( 'Київський', 'Шевченківський', 'Солом’янський', 'Подільський' );

				// Check the terms
				foreach ( $districts as $district_name ) {
					if ( ! term_exists( $district_name, 'district' ) ) {
						wp_insert_term( $district_name, 'district' );
					}
				}

				// Add random district to this post
				$random_district = $districts[ array_rand( $districts ) ];
				wp_set_object_terms( $post_id, $random_district, 'district' );

				WP_CLI::log( "Created post {$post_id}: {$post_title}" );
			}

			WP_CLI::success( "Created {$num_posts} posts realestate." );
		}
	}

	WP_CLI::add_command( 're-seeder', 'RE_Seeder_Command' );
}
