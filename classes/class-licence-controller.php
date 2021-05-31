<?php

namespace MightyRFD\Classes;

if ( ! defined( 'ABSPATH' ) ) exit;

class LicenceController {

    public function __construct() {
        add_action( 'admin_notices', [ $this, 'mrfdActivatorAdminNotices' ] );
    }

    public static function activateLicence( $licence ) {

        $api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $licence,
			'item_name'  => urlencode( MRFD_ITEM_NAME ),
			'url'        => home_url()
        );
        
        $response = wp_remote_post( MRFD_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}

		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {

				switch( $license_data->error ) {

					case 'expired' :

						$message = sprintf(
							__( 'Your license key expired on %s.' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'disabled' :
					case 'revoked' :

						$message = __( 'Your license key has been disabled.' );
						break;

					case 'missing' :

						$message = __( 'Invalid license.' );
						break;

					case 'invalid' :
					case 'site_inactive' :

						$message = __( 'Your license is not active for this URL.' );
						break;

					case 'item_name_mismatch' :

						$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), MRFD_ITEM_NAME );
						break;

					case 'no_activations_left':

						$message = __( 'Your license key has reached its activation limit.' );
						break;

					default :

						$message = __( 'An error occurred, please try again.' );
						break;
				}

            } else {
				$expiryDate = date_create( $license_data->expires );
				$data = [
					'status' => true,
					'message' => 'Your license key expires on ' . date_format( $expiryDate, 'd M, Y' ),
				];
                return $data;
            }

        }

		$data = [
			'status' => false,
			'message' => $message,
		];

		return $data;

    }
    
    /**
     * Catch errors from the activation method
     */
    public function mrfdActivatorAdminNotices() {
        if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

            switch( $_GET['sl_activation'] ) {

                case 'false':
                    $message = urldecode( $_GET['message'] );
                    ?>
                    <div class="error">
                        <p><?php echo $message; ?></p>
                    </div>
                    <?php
                    break;

                case 'true':
                default:
                    // Custom success message here for successful activation
                    break;

            }
        }
    }

	public static function deactivateLicence( $licence ) {

		$api_params = array(
			'edd_action'  => 'deactivate_license',
			'license'     => $licence,
			'item_name'   => urlencode( MRFD_ITEM_NAME ),
			'url'         => home_url()
		);

		$response = wp_remote_post( MRFD_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}

		} else {

			$licence_data = json_decode( wp_remote_retrieve_body( $response ) );

			if( $licence_data->license == 'deactivated' ) {
				return true;
			} else {
				return false;
			}

		}

	}

}

new LicenceController();