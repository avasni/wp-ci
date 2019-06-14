<?php
defined( 'ABSPATH' ) || exit;
class SLP_Location_Utilities extends SLP_Base_Object {

	/**
	 * Create the city_state_zip formatted output.
	 */
	public function create_city_state_zip() {
		global $slplus;
		$output = '';
		if ( trim( $slplus->currentLocation->city ) !== '' ) {
			$output = $slplus->currentLocation->city;
			if ( trim( $slplus->currentLocation->state ) !== '' ) {
				$output .= ', ';
			}
		}

		if ( trim( $slplus->currentLocation->state ) !== '' ) {
			$output .= $slplus->currentLocation->state;
			if ( trim( $slplus->currentLocation->zip ) !== '' ) {
				$output .= ' ';
			}
		}

		if ( trim( $slplus->currentLocation->zip ) !== '' ) {
			$output .= $slplus->currentLocation->zip;
		}

		return $output;
	}

	/**
	 * Create the email hyperlink.
	 *
	 * @param string $email
	 *
	 * @return string
	 */
	public function create_email_link( $email ) {
		if ( empty( $email ) ) return '';

		return
			sprintf(
				'<a href="mailto:%s" target="_blank" class="storelocatorlink"><nobr>%s</nobr></a>',
				esc_attr( $email ),
				SLP_Text::get_instance()->get_text( 'label_email' )
			);
	}

	/**
	 * Geocode an address sent in array( 'address' => '...' )
	 *
	 * Allows replacements for Google to hook in via the slp_geocode_address filter.
	 *
	 * Fallback is Google.
	 *
	 * @param array $params
	 *      'address'   required address to geocode
	 *      'region'    region code from Map Domain setting (us,au,...)
	 *      'bounds'    bounds if set
	 *
	 * @return object
	 */
	public function geocode( $params ) {
		$geocode_response = apply_filters( 'slp_geocode_address' , '', $params );

		if ( empty( $params[ 'region' ] ) ) {
			$params[ 'region' ] = SLP_Country_Manager::get_instance()->get_country_code();
		}

		if ( empty( $geocode_response ) ) {
			/** @var  SLP_Google $google */
			$google           = SLP_Google::get_instance();
			$google_json      = $google->geocode( $params['region'] , urldecode( $params['address'] ) );
			$geocode_response = json_decode( $google_json );

			do_action( 'slp_received_google_geocode_response' , $geocode_response , $params );
		}

		return $geocode_response;
	}
}
