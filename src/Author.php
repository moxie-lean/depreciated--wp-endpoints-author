<?php namespace Lean\Endpoints;

use Lean\AbstractEndpoint;

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

	const INVALID_PARAMS = 'ln_invalid_params';

	/**
	 * Callback that creates the data that send to the endpoint.
	 *
	 * @Override
	 *
	 * @param \WP_REST_Request $request The request.
	 * @return array The array with the data of the endpoint
	 */
	public function endpoint_callback( \WP_REST_Request $request ) {
		$params = $request->get_params();

		$id = ( false === $params['id'] ) ? false : absint( $params['id'] );

		$slug = ( false === $params['slug'] ) ? false : trim( $params['slug'], '/' );

		if ( false === $id && false === $slug ) {
			return new \WP_Error( self::INVALID_PARAMS, 'The request must have either an id or a slug', [ 'status' => 400 ] );
		}

		if ( false !== $id ) {
			$user = get_user_by( 'id', $id );
		} else {
			$user = get_user_by( 'slug', $slug );
		}

		if ( $user ) {
			return $this->get_author_data( $user );
		}
		return [];
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
			'slug' => $data->user_nicename,
			'name' => $data->first_name,
			'last_name' => $data->last_name,
			'description' => $data->description,
		];
		return $this->filter_data( $response, $data->ID );
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
				'default' => false,
				'validate_callback' => function ( $id ) {
					return false === $id || intval( $id ) > 0;
				},
			],
			'slug' => [
				'default' => false,
				'sanitize_callback' => function ( $slug, $request, $key ) {
					return false === $slug ? $slug : sanitize_title( $slug );
				},
			],
		];
	}
}
