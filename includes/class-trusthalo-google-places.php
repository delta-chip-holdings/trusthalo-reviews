<?php
/**
 * Server-side Google Places API client.
 *
 * @package TrustHaloReviews
 */

defined( 'ABSPATH' ) || exit;

class TrustHalo_Google_Places {
	const OPTION_NAME = 'trusthalo_settings';

	/**
	 * Request-scoped response reuse. Google Maps content is not persisted.
	 *
	 * @var array
	 */
	private static $request_cache = array();

	/**
	 * Retrieve sanitized plugin settings.
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = get_option( self::OPTION_NAME, array() );

		return wp_parse_args(
			is_array( $settings ) ? $settings : array(),
			array(
				'api_key'  => '',
				'place_id' => '',
			)
		);
	}

	/**
	 * Get normalized business and review data.
	 *
	 * @param bool $force_refresh Whether to bypass cached data.
	 * @return array|WP_Error
	 */
	public function get_reviews( $force_refresh = false ) {
		$settings = $this->get_settings();
		$api_key  = trim( $settings['api_key'] );
		$place_id = trim( $settings['place_id'] );

		if ( '' === $api_key || '' === $place_id ) {
			return new WP_Error(
				'trusthalo_not_configured',
				__( 'Add a Google Places API key and Place ID in TrustHalo settings.', 'trusthalo-reviews' )
			);
		}

		if ( ! $force_refresh && isset( self::$request_cache[ $place_id ] ) ) {
			return self::$request_cache[ $place_id ];
		}

		$url = 'https://places.googleapis.com/v1/places/' . rawurlencode( $place_id );
		$response = wp_remote_get(
			$url,
			array(
				'timeout' => 15,
				'headers' => array(
					'X-Goog-Api-Key'   => $api_key,
					'X-Goog-FieldMask' => 'id,displayName,rating,userRatingCount,reviews,googleMapsUri,attributions',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status_code = (int) wp_remote_retrieve_response_code( $response );
		$body        = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $status_code || ! is_array( $body ) ) {
			$message = __( 'Google Places could not return this business. Check the API key, Place ID, billing, and API restrictions.', 'trusthalo-reviews' );

			if ( isset( $body['error']['message'] ) ) {
				$message = sanitize_text_field( $body['error']['message'] );
			}

			return new WP_Error( 'trusthalo_google_error', $message, array( 'status' => $status_code ) );
		}

		$data = $this->normalize_response( $body );
		self::$request_cache[ $place_id ] = $data;

		return $data;
	}

	/**
	 * Normalize only the fields the renderer needs.
	 *
	 * @param array $body Google Places API response.
	 * @return array
	 */
	private function normalize_response( $body ) {
		$reviews      = array();
		$attributions = array();

		if ( ! empty( $body['reviews'] ) && is_array( $body['reviews'] ) ) {
			foreach ( $body['reviews'] as $review ) {
				$author = isset( $review['authorAttribution'] ) && is_array( $review['authorAttribution'] )
					? $review['authorAttribution']
					: array();

				$reviews[] = array(
					'author_name' => isset( $author['displayName'] ) ? sanitize_text_field( $author['displayName'] ) : __( 'Google reviewer', 'trusthalo-reviews' ),
					'author_uri'  => isset( $author['uri'] ) ? esc_url_raw( $author['uri'] ) : '',
					'author_photo' => isset( $author['photoUri'] ) ? esc_url_raw( $author['photoUri'] ) : '',
					'rating'      => isset( $review['rating'] ) ? min( 5, max( 0, (float) $review['rating'] ) ) : 0,
					'text'        => isset( $review['text']['text'] ) ? sanitize_textarea_field( $review['text']['text'] ) : '',
					'text_language' => isset( $review['text']['languageCode'] ) ? sanitize_key( $review['text']['languageCode'] ) : '',
					'original_language' => isset( $review['originalText']['languageCode'] ) ? sanitize_key( $review['originalText']['languageCode'] ) : '',
					'relative_time' => isset( $review['relativePublishTimeDescription'] ) ? sanitize_text_field( $review['relativePublishTimeDescription'] ) : '',
					'publish_time' => isset( $review['publishTime'] ) ? sanitize_text_field( $review['publishTime'] ) : '',
					'review_uri'   => isset( $review['googleMapsUri'] ) ? esc_url_raw( $review['googleMapsUri'] ) : '',
					'flag_uri'     => isset( $review['flagContentUri'] ) ? esc_url_raw( $review['flagContentUri'] ) : '',
					'visit_date'   => $this->format_visit_date( isset( $review['visitDate'] ) ? $review['visitDate'] : array() ),
				);
			}
		}

		if ( ! empty( $body['attributions'] ) && is_array( $body['attributions'] ) ) {
			foreach ( $body['attributions'] as $attribution ) {
				if ( empty( $attribution['provider'] ) ) {
					continue;
				}

				$attributions[] = array(
					'provider' => sanitize_text_field( $attribution['provider'] ),
					'uri'      => isset( $attribution['providerUri'] ) ? esc_url_raw( $attribution['providerUri'] ) : '',
				);
			}
		}

		return array(
			'place_id'     => isset( $body['id'] ) ? sanitize_text_field( $body['id'] ) : '',
			'business_name'=> isset( $body['displayName']['text'] ) ? sanitize_text_field( $body['displayName']['text'] ) : __( 'Google Reviews', 'trusthalo-reviews' ),
			'rating'       => isset( $body['rating'] ) ? (float) $body['rating'] : 0,
			'review_count' => isset( $body['userRatingCount'] ) ? absint( $body['userRatingCount'] ) : 0,
			'maps_uri'     => isset( $body['googleMapsUri'] ) ? esc_url_raw( $body['googleMapsUri'] ) : '',
			'reviews'      => $reviews,
			'attributions' => $attributions,
		);
	}

	/**
	 * Format an optional visit month supplied for certain regions.
	 *
	 * @param mixed $visit_date Google Date object.
	 * @return string
	 */
	private function format_visit_date( $visit_date ) {
		if ( ! is_array( $visit_date ) || empty( $visit_date['year'] ) || empty( $visit_date['month'] ) ) {
			return '';
		}

		$year  = absint( $visit_date['year'] );
		$month = absint( $visit_date['month'] );

		if ( $month < 1 || $month > 12 ) {
			return '';
		}

		return wp_date( 'F Y', gmmktime( 0, 0, 0, $month, 1, $year ) );
	}
}
