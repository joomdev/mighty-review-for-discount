<?php
namespace MightyRFD;

/**
 * Class Mighty_Discount
 *
 * Contains Custom Post Type for discounts
 *
 * @since 1.0.0
 */
class Mighty_Discount
{

    const POST_TYPE = 'mighty-discount';

    private static $_instance = null;

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {

        add_action('init', [$this, 'register_discount_post']);

        add_filter( 'manage_mighty-discount_posts_columns', [ $this, 'set_custom_columns' ] );

        add_action( "manage_" . self::POST_TYPE . "_posts_custom_column", [ $this, 'add_column_data' ], 10, 2 );

        add_action( 'admin_notices', [ $this, 'add_header' ] );

        add_action( 'add_meta_boxes', [ $this, 'add_metabox' ] );

        add_action( 'save_post', [ $this, 'save_metabox' ], 10, 2 );

    }

    public function register_discount_post() {

        $labels = [
            'name' => _x( 'Mighty Discount', 'Post Type General Name', 'mighty-rfd' ),
            'singular_name' => _x( 'Mighty Discount', 'Post Type Singular Name', 'mighty-rfd' ),
            'view_item' => __( 'View Discount', 'mighty-rfd' ),
            'add_new_item' => __( 'Add New Discount', 'mighty-rfd' ),
            'add_new' => __( 'Add New Discount', 'mighty-rfd' ),
            'new_item' => __( 'New Discount', 'mighty-rfd' ),
            'edit_item' => __( 'Edit Discount', 'mighty-rfd' ),
            'update_item' => __( 'Update Discount', 'mighty-rfd' ),
            'search_items' => __( 'Search Discount', 'mighty-rfd' ),
            'not_found' => __( 'Not Found', 'mighty-rfd' ),
            'not_found_in_trash' => __( 'Not found in Trash', 'mighty-rfd' ),
        ];

        // Set other options for Custom Post Type
        $args = [
            'labels' => $labels,
            'supports' => [ 'title' ],
            'hierarchical' => false,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_nav_menus' => false,
            'has_archive' => true,
            'exclude_from_search' => true,
            'customType' => self::POST_TYPE,
            'map_meta_cap' => true,
            'rewrite' => false,
            'publicly_queryable' => false,
            'query_var' => false,
        ];

        // Registering your Custom Post Type
        register_post_type( self::POST_TYPE, $args );

    }
    
    public function set_custom_columns() {

        $columns = [
            'cb' => '<input type="checkbox" />',
            'title' => esc_html__( 'Title', 'mighty-rfd' ),
            'description' => esc_html__( 'Description', 'mighty-rfd' ),
            'coupon_type' => esc_html__( 'Coupon Type', 'mighty-rfd' ),
            'trigger' => esc_html__( 'Trigger', 'mighty-rfd' ),
            'status' => esc_html__( 'Status', 'mighty-rfd' ),
            'date' => esc_html__( 'Date', 'mighty-rfd' ),
        ];

        return $columns;

    }

    public function add_column_data( $column, $post_id ) {

        foreach ( get_post_meta( $post_id ) as $key => $value ) {
			$discountMeta[$key] = $value[0];
		}

        switch( $column ) {

            case 'description': echo $discountMeta['mighty_coupon_description'];
            break;

            case 'coupon_type' : echo ucwords( str_replace("_", " ", $discountMeta['mighty_discount_type'] ) );
            break;

            case 'trigger': echo ucwords( str_replace("_", " ", $discountMeta['mighty_triggering_event'] ) );
            break;

            case 'status': echo ucfirst( get_post_status( $post_id ) );
            break;

        }

    }

    public function add_header() {

        global $pagenow;

        if( ( $pagenow == 'edit.php' || $pagenow == 'post-new.php' || $pagenow == 'post.php' ) && ( isset( $_REQUEST['post_type'] ) && $_REQUEST['post_type'] == self::POST_TYPE ) ) {

            include_once MIGHTY_RFD_DIR_PATH . 'includes/header.php';

        }

    }

    public function add_metabox() {

        add_meta_box(
            'mighty-rfd-metabox',
            __( 'Add New Review Discount', 'mighty-rfd' ),
            [ $this, 'mighty_metabox_callback' ],
            self::POST_TYPE,
            'normal',
            'default'
        );
        
    }

    public function mighty_metabox_callback( $post ) {
        
        wp_nonce_field( 'mighty_metabox_data', 'mighty_metabox_nonce' );

        include_once MIGHTY_RFD_DIR_PATH . 'panel/new-review-discount.php';

    }

    /**
     * Handles saving the meta box.
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @return null
     */
    public function save_metabox( $post_id, $post ) {

        // Add nonce for security and authentication.
        $nonce_name   = isset( $_POST['mighty_metabox_nonce'] ) ? $_POST['mighty_metabox_nonce'] : '';
        $nonce_action = 'mighty_metabox_data';
 
        // Check if nonce is valid.
        if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
            return;
        }
 
        // Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
 
        // Check if not an autosave.
        if ( wp_is_post_autosave( $post_id ) ) {
            return;
        }
 
        // Check if not a revision.
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }

        // Getting data for new discount
        $discountData = [
            'mighty_coupon_description' => wc_clean( $_POST['coupon_description'] ),
            'mighty_triggering_event' => wc_clean( $_POST['triggering_event'] ),
            'mighty_discount_type' => wc_clean( $_POST['discount_type'] ),
            'mighty_coupon_amount' => wc_format_decimal( $_POST['coupon_amount'] ),
            'mighty_expire_after_days' => wc_clean( $_POST['expire_after_days'] ),
            'mighty_enable_free_shipping' => '',
            'mighty_only_send_to_verified_users' => '',
            'mighty_single_use_only' => $_POST['single_use_only'] ?? false,
            'mighty_individual_use_only' => $_POST['individual_use_only'] ?? false,
            'mighty_exclude_sale_items' => '',
            'mighty_minimum_spending_amount' => '',
            'mighty_maximum_spending_amount' => '',
            'mighty_included_products' => [],
            'mighty_excluded_products' => [],
            'mighty_included_categories' => [],
            'mighty_excluded_categories' => []
        ];

        // updating data
        foreach( $discountData as $key => $value ) {
            update_post_meta( $post_id, $key, $value );
        }

    }

}

// Instantiate Mighty_Discount Class
Mighty_Discount::instance();
