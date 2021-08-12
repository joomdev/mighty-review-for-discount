<?php
/**
 * Dashboard
 *
 * Package: Mighty Review For Discount
 * @since 1.0.0
 */
namespace MightyRFD\Classes;

use \MightyRFD\Classes\HelperFunctions;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'DashboardPanel' ) ) {
    class DashboardPanel {

        const PLG_SLUG = 'mighty-rfd';

        const PLG_NONCE = 'mighty_rfd_panel';

        const PAGE_ID = 'mighty-rfd-basic_configuration';
        
        public static function init() {
            
            add_action( 'admin_menu', [ __CLASS__, 'add_menu' ], 22 );

            add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_scripts' ] );

            add_action( 'wp_ajax_mighty_send_test_email', [ __CLASS__, 'send_test_email' ] );

            add_action( 'wp_ajax_mighty_delete_expired_coupons', [ '\MightyRFD\Classes\HelperFunctions', 'delete_expired_coupons' ] );

        }

        public static function add_menu() {

            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            add_menu_page(
                __( 'Mighty Review For Discount', 'mighty-rfd' ),
                __( 'Mighty RFD', 'mighty-rfd' ),
                'manage_options',
                self::PAGE_ID,
                [ __CLASS__, 'generate_basic_configuration' ],
                MIGHTY_RFD_PLG_URL . 'assets/images/mighty-themes-logo.svg',
                99
            );

            add_submenu_page(
                self::PAGE_ID,
                __( 'Discounts', 'mighty-rfd' ),
                __( 'Discounts', 'mighty-rfd' ),
                'manage_options',
                'edit.php?post_type=mighty-discount'
            );

            add_submenu_page(
                self::PAGE_ID,
                __( 'Add Discount', 'mighty-rfd' ),
                __( 'Add Discount', 'mighty-rfd' ),
                'manage_options',
                'post-new.php?post_type=mighty-discount'
            );

            add_submenu_page(
                self::PAGE_ID,
                __( 'Go Pro', 'mighty-rfd' ),
                __( 'Go Pro', 'mighty-rfd' ),
                'manage_options',
                'mighty-rfd-go-pro',
                [ __CLASS__, 'generate_mighty_rfd_go_pro' ]
            );
            
        }

        public static function enqueue_scripts( $hook ) {

            global $pagenow;
            
            if(
                strpos( $hook, self::PLG_SLUG ) !== false ||
                (
                    ( isset( $_REQUEST['post_type'] ) && $_REQUEST['post_type'] == 'mighty-discount' ) ||
                    ( $pagenow == 'post.php' || $pagenow == 'edit.php' || $pagenow == 'post-new.php' ) 
                ) 
            ) {
                // ⚠ Proceed with caution
            } else {
                return;
            }

            wp_enqueue_style(
                'mighty-admin-styles',
                MIGHTY_RFD_PLG_URL . 'assets/css/admin-styles.css',
                null,
                MIGHTY_RFD_VERSION
            );

            // Admin Level Script
            wp_enqueue_script(
                'mighty-admin',
                MIGHTY_RFD_PLG_URL . 'assets/js/admin-scripts.js',
                [ 'jquery' ],
                MIGHTY_RFD_VERSION,
                true // in footer?
            );

            wp_localize_script(
                'mighty-admin',
                'MightyRFD',
                [
                    'nonce' => wp_create_nonce( self::PLG_NONCE ),
                    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                    'mailAction' => 'mighty_send_test_email',
                    'couponDeletionAction' => 'mighty_delete_expired_coupons',
                ]
            );
        }

        private static function load_html( $page, $data = '' ) {
            $file = MIGHTY_RFD_DIR_PATH . 'panel/' . $page . '.php';
            if ( is_readable( $file ) ) {
                include( $file );
            }
        }

        public static function generate_basic_configuration() {

            if( get_option( 'mighty_rfd_basic_configuration' ) ) {
                $basicConfig = get_option( 'mighty_rfd_basic_configuration' );
            } else {
                // Setting up defaults
                $basicConfig = HelperFunctions::$default_basic_configuration;

                update_option( 'mighty_rfd_basic_configuration', $basicConfig );
            }
            
            // Saving data after submission
            if( isset( $_POST['mighty-basic-configuration'] ) ) {
                $basicConfig = [
                    'enable_rfd' => wc_clean( $_POST['enable_rfd'] ) ?? false,
                    'trigger_discount' => wc_clean( $_POST['trigger_discount'] ),
                    'delete_expired_coupons' => wc_clean( $_POST['delete_expired_coupons'] ) ?? false,
                    'single_review_email_type' => wc_clean( $_POST['single_review_email_type'] ),
                    'single_review_email_subject' => str_replace('\\', '', wc_clean( $_POST['single_review_email_subject'] ) ),
                    'single_review_email_content' => str_replace('\\', '', wc_clean( $_POST['single_review_email_content'] ) ),
                    'multiple_review_email_type' => '',
                    'multiple_review_email_subject' => '',
                    'multiple_review_email_content' => '',
                    'reminder_email_type' => wc_clean( $_POST['reminder_email_type'] ),
                    'reminder_email_subject' => str_replace('\\', '', wc_clean( $_POST['reminder_email_subject'] ) ),
                    'reminder_email_content' => str_replace('\\', '', wc_clean( $_POST['reminder_email_content'] ) ),
                    'close_target_email_type' => '',
                    'close_target_email_subject' => '',
                    'close_target_email_content' => ''
                ];
                
                update_option( 'mighty_rfd_basic_configuration', $basicConfig );
            }
            
            self::load_html( 'basic-configuration', $basicConfig );

        }

        public static function generate_review_discount_page() {

            self::load_html( 'review-discounts' );

        }

        public static function generate_new_review_discount_page() {

            self::load_html( 'new-review-discount' );

        }

        public static function generate_mighty_rfd_go_pro() {
            
            self::load_html( 'go-pro' );
            
        }

        public static function send_test_email() {
            
            check_ajax_referer( 'mighty_rfd_panel', 'security' );
            
            if( isset( $_POST['fields'] ) ) {
                $data = $_POST['fields'];
            } else {
                return;
            }

            $mailer = WC()->mailer()->get_emails();
            $configuration = HelperFunctions::get_basic_configuration();
            $triggerEvent = $data['trigger'];
            $email = $data['email'];
            $current_user = wp_get_current_user();
            // Tomorrow date
            $datetime = new \DateTime('tomorrow');
            
            $couponDescription = "<h2>Coupon code: " . $current_user->display_name . "-1622026571</h2>
<i>40% discount on your next product purchase</i><br>
<b>Discount Available: 40% off</b><br>
Expires on: " . $datetime->format('d M, Y');

            $emailDetails['email_subject'] = $configuration[$triggerEvent.'_email_subject'];
            $emailDetails['email_body'] = $configuration[$triggerEvent.'_email_content'];
            $emailDetails['email_type'] = $configuration[$triggerEvent.'_email_type'];
            $emailDetails['email_address'] = $email;
            $emailDetails['tags'] = [
                'site_title' => get_bloginfo( 'name' ),
                'customer_name' => ucfirst( $current_user->display_name ),
                'customer_email' => $email
            ];

            if( $triggerEvent == 'single_review' ) {
                $emailDetails['tags']['coupon_description'] = $couponDescription;
            }

            if( $triggerEvent == 'single_review' || $triggerEvent == 'multiple_review' || $triggerEvent == 'reminder' ) {
                $emailDetails['tags']['product_name'] = "T-shirt";
            }

            if( $triggerEvent == 'close_target' ) {
                $emailDetails['tags']['reviews_left'] = '2';
            }

            // Send the email
		    return $mailer['MIGHTY_Coupon_Email']->trigger( $emailDetails );
            
            wp_die();
        }

    }
}

DashboardPanel::init();