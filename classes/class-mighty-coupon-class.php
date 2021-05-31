<?php

namespace MightyRFD;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * A custom Mighty Coupon WooCommerce Email class
 *
 * @since 1.0.0
 * @extends \WC_Email
 */
class MIGHTY_Coupon_Email extends \WC_Email {

    public function __construct() {

        $this->id = 'mighty_review_coupon';
        $this->title = __( 'Review Coupon', 'mighty-rfd' );
        $this->description = __( 'Mighty Review Coupon emails are sent when a customer review single or multiple products.', 'might-rfd' );

        $this->template_html  = 'includes/emails/coupon-email.php';
        $this->template_plain = 'includes/emails/plain/coupon-email.php';
        $this->template_base  = MIGHTY_RFD_DIR_PATH;

        $this->customer_email = true;

        // Calling parent constructor to load any other defaults not explicity defined here
        parent::__construct();
    }

    /**
     * Determine if the email should actually be sent and setup email merge variables
     *
     * @since 1.0.0
     */
    public function trigger( $emailDetails ) {
        
        $this->heading = $emailDetails['email_subject'];
        $this->subject = $this->reformat_details( $emailDetails['email_subject'], $emailDetails['tags'] );
        $this->mail_body = $this->reformat_details( $emailDetails['email_body'], $emailDetails['tags'] );
        $this->recipient = $emailDetails['email_address'];
        $this->email_type = $emailDetails['email_type'];

        if ( ! $this->get_recipient() ) {
            return;
        }

        // all done, send the email!
        $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), '' );
        
    }

    /**
     * get_content_html function.
     *
     * @since 1.0.0
     * @return string
     */
    public function get_content_html() {

        ob_start();

        wc_get_template( $this->template_html, [
            'email' => $this,
            'email_heading' => $this->get_heading(),
            'mail_body' => $this->mail_body,
            'sent_to_admin' => false,
            'plain_text' => false
        ], false, $this->template_base );

        return ob_get_clean();

    }


    /**
     * get_content_plain function.
     *
     * @since 1.0.0
     * @return string
     */
    public function get_content_plain() {

        ob_start();

        wc_get_template( $this->template_plain, [
            'email_heading' => $this->get_heading(),
            'mail_body'     => $this->mail_body,
            'sent_to_admin' => false,
            'plain_text'    => true
        ], false, $this->template_base );

        return ob_get_clean();

    }

    /**
     * Replaces the tags in the string with valid data
     *
     * @since 1.0.0
     */
    public function reformat_details( $data, $tags ) {

        if ( preg_match_all( "/{([^\}]*)\}/", $data, $matches ) ) {
            foreach ( $matches[0] as $match ) {
                $data = str_replace( $match, $tags[str_replace([ '{', '}' ], '', $match)], $data);
            }
        }

        return nl2br( $data );

    }

}