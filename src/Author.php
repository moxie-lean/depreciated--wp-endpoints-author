<?php namespace Leean\Endpoints;

use Leean\Endpoint;

class Author extends Endpoint {
	protected $endpoint = '/author';

	public static function init(){
		$author_endpoint = new self();
		$author_endpoint->create();
	}

	public function endpoint_callback(\WP_REST_Request $request){
		return [];
	}

	public function endpoint_args(){
		return [
			'slug' => [
				'required' => false,
				'sanitize_callback' => function ( $param, $request, $key ) {
					return sanitize_text_field( $param );
				},
			],
		];
	}
}

