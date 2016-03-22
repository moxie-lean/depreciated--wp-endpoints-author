<?php namespace Leean\Endpoints;

use Leean\AbstractEndpoint;
use Leean\Endpoints\Filters;

/**
 * Class that creates and endpoint with the data associated with the author
 * in his WP Profile.
 *
 * @since 0.1.0
 */
class Author extends AbstractEndpoint {
	/**
	 * Slug of the new endpoint.
	 *
	 * @Override
	 * @var string
	 */
	protected $endpoint = '/author';

	/**
	 * Static method as user interface for the class that creates a new object
	 * of this class to make sure we can access to instance properties and methods.
	 *
	 * @since 0.1.0
	 */
	public static function init() {
		$author_endpoint = new self();
		$author_endpoint->create();
	}

	/**
	 * Callback that creates the data that send to the endpoint.
	 *
	 * @override
	 *
	 * @param \WP_REST_Request $request The request.
	 * @return array The array with the data of the endpoint
	 */
	public function endpoint_callback( \WP_REST_Request $request ) {
		$user_id = $request->get_param( 'id' );
		$response = [];
		$user = get_user_by( 'id', $user_id );
		if ( $user ) {
			$response = $this->get_author_data( $user );
		}
		return $response;
	}

	/**
	 * Fill an array with the data of a user object, and applies a filter:
	 * 'ln_endpoints_data_author' to the response in order to add or modify the
	 * data.
	 *
	 * @param \WP_User $user The user from where to retrieve the info.
	 * @return Array an array with the basic informatiion from the profile
	 */
	private function get_author_data( \WP_User $user ) {
		$data = get_userdata( $user->ID );
		$response = [
			'id' => $data->ID,
			'email' => $data->user_email,
			'first_name' => $data->first_name,
			'last_name' => $data->last_name,
			'description' => $data->description,
		];
		$filter = $this->get_filter_name();
		return apply_filters( $filter, $response, $data->ID );
	}

	/**
	 * Defines the arguments used on the accepted on the endpoint and the callback
	 * to sanitize the value.
	 *
	 * @Override
	 */
	public function endpoint_args() {
		return [
			'id' => [
				'required' => true,
				'sanitize_callback' => function ( $author_id, $request, $key ) {
					return absint( $author_id );
				},
			],
		];
	}
}
