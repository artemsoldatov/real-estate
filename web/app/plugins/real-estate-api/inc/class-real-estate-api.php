<?php

/**
 * Class RealEstate_API
 * This class registers custom REST API routes for the "realestate" CPT
 *
 */
class RealEstate_API {
	public function __construct() {
		// Register
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Registers all the custom REST routes
	 */
	public function register_routes() {
		// /wp-json/realestate/v1/objects
		register_rest_route( 'realestate/v1', '/objects', array(
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_objects' ),
				'permission_callback' => '__return_true',
			),
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'create_object' ),
				'permission_callback' => '__return_true',
			),
		) );

		// /wp-json/realestate/v1/objects/<id>
		register_rest_route( 'realestate/v1', '/objects/(?P<id>\d+)', array(
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_object' ),
				'permission_callback' => '__return_true',
			),
			array(
				'methods'             => 'PUT',
				'callback'            => array( $this, 'update_object' ),
				'permission_callback' => '__return_true',
			),
			array(
				'methods'             => 'DELETE',
				'callback'            => array( $this, 'delete_object' ),
				'permission_callback' => '__return_true',
			),
		) );

		// XML parsing: /wp-json/realestate/v1/import-xml
		register_rest_route( 'realestate/v1', '/import-xml', array(
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'import_xml_objects' ),
				'permission_callback' => '__return_true',
			),
		) );
	}

	/**
	 * GET /objects
	 * Returns a list of "realestate" posts
	 * Optionally filtered by district, ecology, floors, building type
	 * Getting posts by number of page (paged param)
	 *
	 * Example: ?district=подільський&ecology=3&floors=5&type=цегла
	 */
	public function get_objects( $request ) {
		$paged = (int) $request->get_param( 'paged' ) ?: 1;
		$args  = array(
			'post_type'      => 'realestate',
			'paged'          => $paged,
//			'posts_per_page' => - 1,
			'posts_per_page' => 5,
		);

		// Taxonomy district filter
		if ( $district = $request->get_param( 'district' ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'district',
				'field'    => 'slug',
				'terms'    => $district,
			);
		}

		// Meta filters
		$meta_query = [];

		// ecology
		if ( $eco = $request->get_param( 'ecology' ) ) {
			$meta_query[] = array(
				'key'   => 'environmental_friendliness',
				'value' => $eco,
			);
		}

		// floors
		if ( $floors = $request->get_param( 'floors' ) ) {
			$meta_query[] = array(
				'key'   => 'number_of_floors',
				'value' => $floors,
			);
		}

		// type
		if ( $type = $request->get_param( 'type' ) ) {
			$meta_query[] = array(
				'key'   => 'building_type',
				'value' => $type,
			);
		}

		if ( ! empty( $meta_query ) ) {
			$args['meta_query'] = $meta_query;
		}

		$query = new WP_Query( $args );
		$data  = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$id = get_the_ID();

				$fields = get_fields( $id );

				$district_terms = get_the_terms( $id, 'district' );
				$districts      = ( ! is_wp_error( $district_terms ) && $district_terms ) ? wp_list_pluck( $district_terms, 'name' ) : [];

				$data[] = array(
					'id'       => $id,
					'title'    => get_the_title(),
					'fields'   => $fields,
					'district' => $districts,
					'link'     => get_permalink( $id ),
				);
			}
			wp_reset_postdata();
		}

		return array(
			'items'        => $data,
			'total_pages'  => $query->max_num_pages,
			'current_page' => $paged,
		);

	}


	/**
	 * POST /objects
	 * Creates a new "realestate" post
	 */
	public function create_object( $request ) {
		$params = $request->get_json_params();

		if ( empty( $params['title'] ) ) {
			return new WP_Error( 'no_title', 'Title is required', array( 'status' => 400 ) );
		}

		$post_id = wp_insert_post( array(
			'post_type'   => 'realestate',
			'post_title'  => sanitize_text_field( $params['title'] ),
			'post_status' => 'publish',
		) );

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		if ( function_exists( 'update_field' ) ) {
			$acf_fields = array(
				'house_name',
				'location_coordinates',
				'number_of_floors',
				'building_type',
				'environmental_friendliness',
			);

			foreach ( $acf_fields as $field ) {
				if ( isset( $params[ $field ] ) ) {
					update_field( $field, $params[ $field ], $post_id );
				}
			}

			if ( isset( $params['image'] ) ) {
				$image_id = is_array( $params['image'] ) && isset( $params['image']['ID'] ) ? (int) $params['image']['ID'] : (int) $params['image'];
				update_field( 'image', $image_id, $post_id );
			}

			if ( isset( $params['premises'] ) && is_array( $params['premises'] ) ) {
				$repeater = [];

				foreach ( $params['premises'] as $room ) {
					$repeater[] = array(
						'area'            => $room['area'] ?? '',
						'number_of_rooms' => $room['number_of_rooms'] ?? '',
						'balcony'         => $room['balcony'] ?? '',
						'bathroom'        => $room['bathroom'] ?? '',
						'image'           => isset( $room['image'] ) ? ( is_array( $room['image'] ) ? $room['image']['ID'] : $room['image'] ) : '',
					);
				}

				update_field( 'premises', $repeater, $post_id );
			}
		}

		if ( ! empty( $params['district'] ) ) {
			// ID, slug or name
			wp_set_object_terms( $post_id, $params['district'], 'district' );
		}

		return array(
			'success' => true,
			'id'      => $post_id,
		);
	}


	/**
	 * GET /objects/<id>
	 * Returns a single "realestate" post by ID
	 */
	public function get_object( $request ) {
		$id   = (int) $request['id'];
		$post = get_post( $id );

		if ( ! $post || $post->post_type !== 'realestate' ) {
			return new WP_Error( 'not_found', 'Object not found', array( 'status' => 404 ) );
		}

		$data = [
			'id'    => $id,
			'title' => get_the_title( $id ),
		];

		if ( function_exists( 'get_field' ) ) {
			$data['house_name']                 = get_field( 'house_name', $id );
			$data['location_coordinates']       = get_field( 'location_coordinates', $id );
			$data['number_of_floors']           = get_field( 'number_of_floors', $id );
			$data['building_type']              = get_field( 'building_type', $id );
			$data['environmental_friendliness'] = get_field( 'environmental_friendliness', $id );

			$image         = get_field( 'image', $id );
			$data['image'] = is_array( $image ) ? $image : null;

			if ( have_rows( 'premises', $id ) ) {
				$premises = [];

				while ( have_rows( 'premises', $id ) ) {
					the_row();
					$premises[] = [
						'area'            => get_sub_field( 'area' ),
						'number_of_rooms' => get_sub_field( 'number_of_rooms' ),
						'balcony'         => get_sub_field( 'balcony' ),
						'bathroom'        => get_sub_field( 'bathroom' ),
						'image'           => get_sub_field( 'image' ),
					];
				}

				$data['premises'] = $premises;
			}
		}

		$terms = get_the_terms( $id, 'district' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$data['district'] = wp_list_pluck( $terms, 'slug' );
		}

		return $data;
	}

	/**
	 * PUT /objects/<id>
	 * Updates an existing "realestate" post
	 */
	public function update_object( $request ) {
		$id   = (int) $request['id'];
		$post = get_post( $id );

		if ( ! $post || $post->post_type !== 'realestate' ) {
			return new WP_Error( 'not_found', 'Object not found', array( 'status' => 404 ) );
		}

		$params = $request->get_json_params();

		if ( ! empty( $params['title'] ) ) {
			wp_update_post( [
				'ID'         => $id,
				'post_title' => sanitize_text_field( $params['title'] ),
			] );
		}
		// Update fields
		if ( function_exists( 'update_field' ) ) {
			if ( isset( $params['house_name'] ) ) {
				update_field( 'house_name', sanitize_text_field( $params['house_name'] ), $id );
			}
			if ( isset( $params['location_coordinates'] ) ) {
				update_field( 'location_coordinates', sanitize_text_field( $params['location_coordinates'] ), $id );
			}
			if ( isset( $params['number_of_floors'] ) ) {
				update_field( 'number_of_floors', sanitize_text_field( $params['number_of_floors'] ), $id );
			}
			if ( isset( $params['building_type'] ) ) {
				update_field( 'building_type', sanitize_text_field( $params['building_type'] ), $id );
			}
			if ( isset( $params['environmental_friendliness'] ) ) {
				update_field( 'environmental_friendliness', (int) $params['environmental_friendliness'], $id );
			}
			if ( isset( $params['image'] ) ) {
				$image_id = is_array( $params['image'] ) && isset( $params['image']['ID'] )
					? (int) $params['image']['ID']
					: (int) $params['image'];
				update_field( 'image', $image_id, $id );
			}
			// Update fields of repeater
			if ( isset( $params['premises'] ) && is_array( $params['premises'] ) ) {
				$repeater = [];
				foreach ( $params['premises'] as $room ) {
					$repeater[] = [
						'area'            => $room['area'] ?? '',
						'number_of_rooms' => $room['number_of_rooms'] ?? '',
						'balcony'         => $room['balcony'] ?? '',
						'bathroom'        => $room['bathroom'] ?? '',
						'image'           => isset( $room['image']['ID'] ) ? (int) $room['image']['ID'] : (int) $room['image'],
					];
				}
				update_field( 'premises', $repeater, $id );
			}
		}

		// Update taxonomy
		if ( isset( $params['district'] ) ) {
			$districts = is_array( $params['district'] ) ? $params['district'] : [ $params['district'] ];
			wp_set_object_terms( $id, array_map( 'sanitize_title', $districts ), 'district' );
		}

		return [
			'success' => true,
			'id'      => $id,
		];
	}


	/**
	 * DELETE /objects/<id>
	 * Delete a "realestate" post permanently
	 */
	public function delete_object( $request ) {
		$id   = $request['id'];
		$post = get_post( $id );

		// Check post
		if ( ! $post || $post->post_type !== 'realestate' ) {
			return new WP_Error( 'not_found', 'Object not found', array( 'status' => 404 ) );
		}

		// Force delete
		$result = wp_delete_post( $id, true );
		if ( ! $result ) {
			return new WP_Error( 'delete_failed', 'Failed to delete object', array( 'status' => 500 ) );
		}

		return array( 'success' => true );
	}

	/**
	 * POST /objects/import-xml
	 * Parsing XML body to create multiple "realestate" posts
	 */
	public function import_xml_objects( $request ) {
		$xml_content = $request->get_body();
		if ( empty( $xml_content ) ) {
			return new WP_Error( 'no_xml', 'No XML content provided', array( 'status' => 400 ) );
		}

		try {
			$xml = new SimpleXMLElement( $xml_content );
		} catch ( Exception $e ) {
			return new WP_Error( 'invalid_xml', 'Failed to parse XML', array( 'status' => 400 ) );
		}

		$created = [];

		foreach ( $xml->object as $obj ) {
			$title   = (string) $obj->title ?: 'Untitled';
			$post_id = wp_insert_post( [
				'post_type'   => 'realestate',
				'post_title'  => sanitize_text_field( $title ),
				'post_status' => 'publish',
			] );

			if ( is_wp_error( $post_id ) ) {
				continue;
			}

			if ( isset( $obj->district ) ) {
				$district_name = (string) $obj->district;
				$term          = term_exists( $district_name, 'district' );

				if ( ! $term ) {
					$term = wp_insert_term( $district_name, 'district' );
				}

				if ( ! is_wp_error( $term ) ) {
					$term_id = is_array( $term ) ? $term['term_id'] : $term;
					wp_set_post_terms( $post_id, [ $term_id ], 'district' );
				}
			}

			if ( function_exists( 'update_field' ) ) {
				update_field( 'house_name', (string) $obj->house_name, $post_id );
				update_field( 'location_coordinates', (string) $obj->location_coordinates, $post_id );
				update_field( 'number_of_floors', (string) $obj->number_of_floors, $post_id );
				update_field( 'building_type', (string) $obj->building_type, $post_id );
				update_field( 'environmental_friendliness', (string) $obj->environmental_friendliness, $post_id );
				if ( ! empty( $obj->image ) ) {
					require_once ABSPATH . 'wp-admin/includes/image.php';
					require_once ABSPATH . 'wp-admin/includes/file.php';
					require_once ABSPATH . 'wp-admin/includes/media.php';

					$main_image_url = (string) $obj->image;
					$main_image_id  = media_sideload_image( $main_image_url, $post_id, null, 'id' );

					if ( ! is_wp_error( $main_image_id ) ) {
						update_field( 'image', $main_image_id, $post_id );
					}
				}

				// Repeater field
				if ( $obj->premises && $obj->premises->room ) {
					$repeater = [];

					foreach ( $obj->premises->room as $room ) {
						$image_url = (string) $room->image;
						$image_id  = null;

						if ( ! empty( $image_url ) ) {
							$image_id = media_sideload_image( $image_url, $post_id, null, 'id' );
							if ( is_wp_error( $image_id ) ) {
								$image_id = null;
							}
						}

						$repeater[] = [
							'area'            => (float) $room->area,
							'number_of_rooms' => (int) $room->number_of_rooms,
							'balcony'         => (string) $room->balcony,
							'bathroom'        => (string) $room->bathroom,
							'image'           => $image_id,
						];
					}
					update_field( 'premises', $repeater, $post_id );
				}
			}

			$created[] = $post_id;
		}

		return [
			'success'     => true,
			'created_ids' => $created,
		];
	}
}
