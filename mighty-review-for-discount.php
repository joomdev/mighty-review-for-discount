<?php
/**
 * Plugin Name: Mighty Review for Discount
 * Description: Mighty Review For Discount helps you to increase engagement by collecting reviews from your customers and providing coupons.
 * Plugin URI: https://mightythemes.com/product/mighty-review-for-discount
 * Version: 1.0.0
 * Author: MightyThemes1
 * Author URI:  https://mightythemes.com/
 * Text Domain: mighty-rfd
 * WC requires at least: 4.2.0
 * WC tested up to: 5.2
 */

namespace Mighty_RFD;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'MIGHTY_RFD_VERSION', '1.0.0' );
define( 'MIGHTY_RFD_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'MIGHTY_RFD_PLG_URL', plugin_dir_url( __FILE__ ) );
define( 'MIGHTY_RFD_PLG_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Main Mighty RFD Class
 *
 * The init class that runs the Mighty RFD plugin.
 * Intended To make sure that the plugin's minimum requirements are met.
 *
 * You should only modify the constants to match your plugin's needs.
 *
 * Any custom code should go inside Plugin Class in the plugin.php file.
 * @since 1.0.0
 */
final class Mighty_RFD {

    /**
	 * Minimum WooCommerce Version
	 *
	 * @since 1.0.0
	 * @var string Minimum WooCommerce version required to run the plugin.
	 */
	const MINIMUM_WOOCOMMERCE_VERSION = '3.7';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '7.0';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

        // Load translation
		add_action( 'init', [ $this, 'i18n' ] );

		register_activation_hook( __FILE__, [ $this, 'mighty_rfd_activation_redirect' ] );

		// Init Plugin
		add_action( 'plugins_loaded', [ $this, 'init' ] );

		add_action( 'admin_init', [ $this, 'show_user_what_we_got' ] );
	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 * Fired by `init` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function i18n() {
		load_plugin_textdomain( 'mighty-rfd' );
	}

	/**
	 * Activate Mighty RFD.
	 *
	 * Set Mighty RFD activation hook.
	 *
	 * Fired by `register_activation_hook` when the plugin is activated.
	 *
	 * @since 2.0.0
	 * @access public
	 */
	public function mighty_rfd_activation_redirect() {
		add_option( 'activate_mighty_rfd', true );
	}

	public function show_user_what_we_got() {

		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return;
		} elseif ( get_option( 'activate_mighty_rfd', false ) ) {
			
			delete_option( 'activate_mighty_rfd' );
			if( !isset( $_GET['activate-multi'] ) ) {
				wp_safe_redirect( admin_url( 'admin.php?page=mighty-rfd-basic_configuration' ) );
			}
		}
	}

	/**
	 * Initialize the plugin
	 *
	 * Validates that WooCommerce is already loaded.
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed include the plugin class.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {

		// Check if WooCommerce installed and activated
		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return;
		}

		// Check for required WooCommerce version
		if ( ! version_compare( WOOCOMMERCE_VERSION, self::MINIMUM_WOOCOMMERCE_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_woocommerce_version' ] );
			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return;
		}

		// Checking if Pro Version is installed
		if( in_array( 'mighty-review-for-discount-pro/mighty-review-for-discount.php', array_keys( get_plugins() ) ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_pro_installed' ] );
			return;
        }

		// Say hello to my little friend - Helper
		require_once ( MIGHTY_RFD_DIR_PATH . 'classes/class-helper-functions.php' );

        // From the depths, a magical window has opened including our plugin!
		require_once ( MIGHTY_RFD_DIR_PATH . 'classes/mighty-woocommerce.php' );
		
		// Mighty Discount Post Type
		require_once ( MIGHTY_RFD_DIR_PATH . 'classes/class-mighty-discount.php' );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have WooCommerce installed or activated.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: WooCommerce */
			esc_html__( '%1$s requires %2$s to be installed and activated.', 'mighty-rfd' ),
			'<strong>' . esc_html__( 'Mighty Review For Discount', 'mighty-rfd' ) . '</strong>',
			'<strong>' . esc_html__( 'WooCommerce', 'mighty-rfd' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required WooCommerce version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_woocommerce_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: WooCommerce 3: Required WooCommerce version */
			esc_html__( '%1$s requires %2$s version %3$s or greater.', 'mighty-rfd' ),
			'<strong>' . esc_html__( 'Mighty Review For Discount', 'mighty-rfd' ) . '</strong>',
			'<strong>' . esc_html__( 'WooCommerce', 'mighty-rfd' ) . '</strong>',
			self::MINIMUM_WOOCOMMERCE_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '%1$s requires %2$s version %3$s or greater.', 'mighty-rfd' ),
			'<strong>' . esc_html__( 'Mighty Review For Discount', 'mighty-rfd' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'mighty-rfd' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice
	 *
	 * Warning when both FREE and PRO is installed.
	 *
	 * @since 1.0.1
	 * @access public
	 */
	public function admin_notice_pro_installed() {

		$message = sprintf(
			/* translators: 1: Plugin name 2: Pro Version */
			esc_html__( 'Please deactivate the %1$s to use the %2$s version.', 'mighty' ),
			'<strong>' . esc_html__( 'Mighty RFD Free', 'mighty' ) . '</strong>',
			'<strong>' . esc_html__( 'Pro', 'mighty' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}
}

// Instantiate Mighty_RFD.
new Mighty_RFD();
