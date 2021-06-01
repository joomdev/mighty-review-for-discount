<?php

namespace MightyRFD\Classes;

if ( ! defined( 'ABSPATH' ) ) exit;

class HelperFunctions {

    public static $default_basic_configuration = [
        'enable_rfd' => 1,
        'trigger_discount' => 'posted',
        'delete_expired_coupons' => 1,
        'single_review_email_type' => 'html',
        'single_review_email_subject' => "You've been awarded a coupon from {site_title}",
        'single_review_email_content' => 
"Hi {customer_name},

You've reviewed {product_name}!
And for that we've awarded you a coupon:

{coupon_description}

Thank you,
{site_title}",
'reminder_email_type' => 'html',
        'reminder_email_subject' => 'Review reminder from {site_title}',
        'reminder_email_content' => 
"Hi {customer_name},

You've just bought {product_name}
Let us know your review to get a coupon.

Thank you,
{site_title}",
        'multiple_review_email_type' => '',
        'multiple_review_email_subject' => "",
        'multiple_review_email_content' => "",
        'close_target_email_type' => '',
        'close_target_email_subject' => "",
        'close_target_email_content' => "",

    ];

    public static function get_configuration_option( $option ) {

        return get_option( 'mighty_rfd_basic_configuration' )[$option] ?? '';
        
    }

    public static function get_basic_configuration() {

        $data = get_option( 'mighty_rfd_basic_configuration', self::$default_basic_configuration );
        return $data;

    }

    public static function get_products_ids() {

        $all_ids = get_posts([
            'post_type' => 'product',
            'numberposts' => -1,
            'post_status' => 'publish',
            'fields' => 'ids',
        ]);

       return $all_ids;

    }

    public static function get_product_categories() {

        $categories = get_terms( ['taxonomy' => 'product_cat', 'hide_empty' => false] );

        return $categories;
        
    }

    public static function delete_expired_coupons() {

        $args = [
			'posts_per_page' => -1,
			'post_type'      => 'shop_coupon',
			'post_status'    => 'publish',
			'meta_query'     => [
				'relation'   => 'AND',
				[
					'key'     => 'date_expires',
					'value'   => current_time( 'timestamp' ),
					'compare' => '<='
				],
				[
					'key'     => 'date_expires',
					'value'   => '',
					'compare' => '!='
				]
			]
		];
	
		$coupons = get_posts( $args );
	
		if ( ! empty( $coupons ) ) {
	
			foreach ( $coupons as $coupon ) {
				wp_trash_post( $coupon->ID );
			}

		}

    }
    
}