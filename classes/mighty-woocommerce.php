<?php
namespace MightyRFD;

use \MightyRFD\Classes\HelperFunctions;

/**
 * Class Mighty_Woocommerce
 * 
 * Contains settings/modification for WooCommerce
 * 
 * @since 1.0.0
 */
class Mighty_Woocommerce {

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {

		add_filter( 'plugin_row_meta', [ $this, 'plugin_meta_links' ], 10, 2 );
		add_filter( 'plugin_action_links_' . MIGHTY_RFD_PLG_BASENAME, [ $this, 'plugin_action_links' ] );

		// Including Admin Widget and update options
		$this->create_mighty_dashboard_panel();

		// if( ! current_user_can('administrator') && HelperFunctions::get_configuration_option( 'enable_rfd' ) ) {
		if( true ) {
			// Hook after review submission/approve
			if ( HelperFunctions::get_configuration_option( 'trigger_discount' ) == 'posted' ) {
				add_action( 'comment_post', [ $this, 'after_review_posted' ], 10, 2 ); // after review posted
			} else {
				add_action( 'transition_comment_status', [ $this, 'after_review_approved' ], 10, 3); // after review approved
			}
		}

		// Adding coupon class
		add_filter( 'woocommerce_email_classes', [ $this, 'add_mighty_woocommerce_coupon_email' ] );

		// Purchase completion hook
		add_action( 'woocommerce_order_status_processing', [ $this, 'woo_payment_complete' ] );

		// Expired Coupons Deletion
		if( HelperFunctions::get_configuration_option( 'delete_expired_coupons' ) ) {

			add_action( 'init', [ '\MightyRFD\Classes\HelperFunctions', 'delete_expired_coupons' ] );

		}

	}

	public function create_mighty_dashboard_panel() {
		
		require_once ( MIGHTY_RFD_DIR_PATH . 'classes/panel.php' );

	}

	/**
	 * Plugin action seconday links
	 * 
	 * Adds action links to the plugin secondary column
	 */
	public function plugin_meta_links( $links, $file ) {

		$currentScreen = get_current_screen();
		
		if( $currentScreen->id === "plugins" && MIGHTY_RFD_PLG_BASENAME == $file ) {

			$links[] = '<a target="_blank" href="#">' . esc_html__( 'Documentation', 'mighty-rfd' ) . '</a>';
			$links[] = '<a target="_blank" href="https://www.youtube.com/channel/UC6TOMaD5I2YTmf4mzHV5Yig">' . __( 'Video Tutorials', 'mighty-rfd' ) . '</a>';

		}

		return $links;
	}

	/**
	 * Plugin action links.
	 *
	 * Adds action links to the plugin primary column
	 */
	public function plugin_action_links( $links ) {
		$settings_link = sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'admin.php?page=mighty-rfd-basic_configuration' ), __( 'Settings', 'mighty-rfd' ) );

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * After Comment/Review Posted.
	 */
	public function after_review_posted( $comment_ID ) {
		
		$commentObj = get_comment( $comment_ID );

		if( $commentObj->comment_type == 'review'  ) {

			$this->validate_review_for_discount( $commentObj );
			
		}
		
	}

	/**
	 * After Comment/Review Approveds.
	 */
	public function after_review_approved( $new_status, $old_status, $commentObj ) {

		if( $commentObj->comment_type == 'review'  ) {

			if( $old_status != $new_status ) {

				if( $new_status == 'approved' ) {

					$this->validate_review_for_discount( $commentObj );
	
				}
			}		
		}
	}

	public function validate_review_for_discount( $commentObj ) {

		# Getting mighty-discount Published posts
		$query = new \WP_Query([
			'post_type' => 'mighty-discount',
			'post_status' => 'publish',
			'posts_per_page' => -1, // retrieve all posts
		]);
		
		# Getting Post Meta
		while ( $query->have_posts() ) {
			$query->the_post();
			$discountId = get_the_ID();

			$triggerEvent = get_post_meta( $discountId, 'mighty_triggering_event', true );
			$verifiedPurchaseEnable = get_post_meta( $discountId, 'mighty_only_send_to_verified_users', true );
			
			// Checking for verified purchase
			if( $verifiedPurchaseEnable ) {
				if ( wc_customer_bought_product( $commentObj->comment_author_email, $commentObj->user_id, $commentObj->comment_post_ID ) ) {
					// Let it go...
				} else {
					return;
				}
			}

			// For single review
			if( $triggerEvent == 'single_review' ) {
				$this->single_event_trigger( $commentObj, $discountId, $triggerEvent );
			}
			
			// For multiple review
			if( $triggerEvent == 'multiple_review' ) {
				$this->multiple_event_trigger( $commentObj, $discountId, $triggerEvent );
			}

		}
		
		wp_reset_query();

	}

	public function single_event_trigger( $commentObj, $discountId, $triggerEvent ) {

		$allComments = get_comments( [ 'post_id' => $commentObj->comment_post_ID ] );
		$reviewCount = 0;
		
		// Check for single review posted
		foreach( $allComments as $comment ) {

			if( $commentObj->user_id == $comment->user_id ) {
				++$reviewCount;
			}

		}
		
		if( $reviewCount == 1 ) {
			$couponObj = $this->create_coupon( $discountId, $commentObj );

			$this->send_email( $couponObj, $commentObj, $triggerEvent );
		}

	}

	public function multiple_event_trigger( $commentObj, $discountId, $triggerEvent ) {

		$reviewsRequired = get_post_meta( $discountId, 'mighty_number_of_reviews_required', true );
		$sendEmailNotif = get_post_meta( $discountId, 'mighty_send_email_notif', true );
		$reviewsRequiredForNotif = get_post_meta( $discountId, 'mighty_reviews_required_for_notif', true );

		// WP_Comment_Query arguments
		$args = [
			// 'status' => 'approve', // TODO: to get approved comments
			'type' => 'review',
			'author_email' => $commentObj->comment_author_email,
		];

		// The Comment Query
		$commentQuery = new \WP_Comment_Query( $args );

		if ( empty( $commentQuery ) ) {
			return;
		}

		$allComments = $commentQuery->comments;
		$uniqueCommentsCount = count( array_unique( array_column( $allComments, 'comment_post_ID' ) ) );

		// Send coupon if multiple reviews received
		if( $uniqueCommentsCount == $reviewsRequired ) {

			$couponObj = $this->create_coupon( $discountId, $commentObj );

			$this->send_email( $couponObj, $commentObj, $triggerEvent );

		} else if( $sendEmailNotif && $uniqueCommentsCount == $reviewsRequiredForNotif ) {
			// sending mail after reaching certain threshold
			$couponObj = $this->create_coupon( $discountId, $commentObj );

			$this->send_email( $couponObj, $commentObj, 'close_target' );

			// Deleting after sending threshold mail
			$couponObj->delete();

		}
	}

	public function create_coupon( $discountId, $commentObj ) {

		// Getting Discount Meta
		foreach ( get_post_meta( $discountId ) as $key => $value ) {
			$discountMeta[$key] = $value[0];
		}

		$userDetails = get_userdata( $commentObj->user_id );
		$userName = $userDetails ? $userDetails->display_name : $commentObj->comment_author;
		$date = new \DateTime();
		$currentDate = $date->getTimestamp();
		$couponCode = $userName . '-' . $currentDate;

		# Creating Coupon
		$wc_coupon = new \WC_Coupon();

		// Set the coupon data
		$wc_coupon->set_code( $couponCode );
		$wc_coupon->set_description( $discountMeta['mighty_coupon_description'] );
		$wc_coupon->set_discount_type( $discountMeta['mighty_discount_type'] );
		$wc_coupon->set_amount(  floatval( $discountMeta['mighty_coupon_amount'] ) );
		$wc_coupon->set_individual_use( $discountMeta['mighty_single_use_only'] );
		$wc_coupon->set_usage_limit( 1 );
		$wc_coupon->set_usage_limit_per_user( 1 );
		$wc_coupon->set_exclude_sale_items( $discountMeta['mighty_exclude_sale_items'] );
		$wc_coupon->set_date_expires( strtotime( '+' . $discountMeta['mighty_expire_after_days'] .' day' ) );
		$wc_coupon->set_free_shipping( $discountMeta['mighty_enable_free_shipping'] );
		$wc_coupon->set_product_ids( explode( ',', $discountMeta['mighty_included_products'] ) );
		$wc_coupon->set_excluded_product_ids( explode( ',', $discountMeta['mighty_excluded_products'] ) );
		$wc_coupon->set_product_categories( explode( ',', $discountMeta['mighty_included_categories'] ) );
		$wc_coupon->set_excluded_product_categories( explode( ',', $discountMeta['mighty_excluded_categories'] ) );
		$wc_coupon->set_minimum_amount( $discountMeta['mighty_minimum_spending_amount'] );
		$wc_coupon->set_maximum_amount( $discountMeta['mighty_maximum_spending_amount'] );
		$wc_coupon->set_email_restrictions( $userDetails->user_email );
		$wc_coupon->update_meta_data( 'coupon_created_by', 'mighty-rfd' );
		if( $discountMeta['mighty_triggering_event'] == 'multiple_review' ) {
			$wc_coupon->update_meta_data( 'number_of_reviews_required', $discountMeta['mighty_number_of_reviews_required'] );
			$wc_coupon->update_meta_data( 'send_email_notif', $discountMeta['mighty_send_email_notif'] );
			$wc_coupon->update_meta_data( 'reviews_required_for_notif', $discountMeta['mighty_reviews_required_for_notif'] );
		}

		// Save the coupon
		$wc_coupon->save();

		return $wc_coupon;

	}

	public function send_email( $couponObj, $commentObj, $triggerEvent ) {

		$mailer = WC()->mailer()->get_emails();
		
		$configuration = HelperFunctions::get_basic_configuration();

		$emailDetails['email_subject'] = $configuration[$triggerEvent.'_email_subject'];
		$emailDetails['email_body'] = $configuration[$triggerEvent.'_email_content'];
		$emailDetails['email_type'] = $configuration[$triggerEvent.'_email_type'];
		$emailDetails['email_address'] = $commentObj->comment_author_email;
		$emailDetails['tags'] = [
			'site_title' => get_bloginfo( 'name' ),
			'customer_name' => ucfirst( $commentObj->comment_author ),
			'customer_email' => $commentObj->comment_author_email
		];

		if( $triggerEvent == 'single_review' || ( $triggerEvent == 'multiple_review' || $triggerEvent == 'close_target' ) ) {
			$emailDetails['tags']['coupon_description'] = $this->get_coupon_description( $couponObj );
			if( $triggerEvent == 'multiple_review' ) {
				$emailDetails['tags']['total_reviews'] = $couponObj->get_meta( 'number_of_reviews_required' );
			}
		}

		if( $triggerEvent == 'single_review' || $triggerEvent == 'multiple_review' || $triggerEvent == 'reminder' ) {
			$product = wc_get_product( $commentObj->comment_post_ID );
			$emailDetails['tags']['product_name'] = "<a href='" . get_permalink( $commentObj->comment_post_ID ) . "'>" . $product->get_name() . "</a>";
		}

		if( $triggerEvent == 'close_target' && $couponObj->get_meta( 'send_email_notif' ) ) {
			$emailDetails['tags']['reviews_left'] = $couponObj->get_meta( 'number_of_reviews_required' ) - $couponObj->get_meta( 'reviews_required_for_notif' );
		}

		// Send the email
		return $mailer['MIGHTY_Coupon_Email']->trigger( $emailDetails );

	}

	/**
     *  Add a custom email to the list of emails WooCommerce should load
     *
     * @since 1.0.0
     * @param array $email_classes available email classes
     * @return array filtered available email classes
     */
    public function add_mighty_woocommerce_coupon_email( $email_classes ) {

        // include custom email class
        require_once ( MIGHTY_RFD_DIR_PATH . 'classes/class-mighty-coupon-class.php' );
        
        $email_classes['MIGHTY_Coupon_Email'] = new MIGHTY_Coupon_Email();

        return $email_classes;
        
    }

	/**
     *  Returns formatted details about the given coupon
     *
     * @since 1.0.0
     * @param array $couponObj
     * @return array HTML formatted string
     */
	public function get_coupon_description( $couponObj ) {

		// Getting discount amount
		if( $couponObj->get_discount_type() == 'percent' ) {
			$couponDiscount = $couponObj->get_amount() . "% off";
		} else {
			$couponDiscount = get_woocommerce_currency() . $couponObj->get_amount() . " off"; 
		}

		// Getting coupon conditions
		if( $couponObj->get_minimum_amount() || $couponObj->get_maximum_amount()  ) {
			$conditions = "<b>Conditions:</b><ul>";

			if( $couponObj->get_minimum_amount() ) {
				$conditions .= "<li>Minimum purchase of " . get_woocommerce_currency() . $couponObj->get_minimum_amount() . ".</li>";
			}

			if( $couponObj->get_maximum_amount() ) {
				$conditions .= "<li>Maximum purchase of " . get_woocommerce_currency() . $couponObj->get_maximum_amount() . ".</li>";
			}

			if( $couponObj->get_individual_use() ) {
				$conditions .= "<li>This coupon can't be used with another coupon.</li>";
			}

			if( $couponObj->get_exclude_sale_items() ) {
				$conditions .= "<li>This coupon can't be used on items on sale.</li>";
			}

			$conditions .= "</ul>";
		}

		// Products Allowed
		if( ! empty( $couponObj->get_product_ids() ) || ! empty( $couponObj->get_product_categories() ) ) {

			$restrictions = "<b>Valid for:</b><ul>";

			if( ! empty( $couponObj->get_product_ids() ) ) {
				// Products
				$restrictions .= 'Products: ';
				foreach ( $couponObj->get_product_ids() as $productId ) {
					$product = wc_get_product( $productId );
					$restrictions .= "<li><a href='" . get_permalink( $productId ) . "'>" . $product->get_name() . "</a></li>";
				}
			}

			if( ! empty( $couponObj->get_product_categories() ) ) {
				// Categories
				$restrictions .= 'Categories: ';
				foreach ( $couponObj->get_product_categories() as $productCatId ) {
					$term = get_term_by( 'id', $productCatId, 'product_cat' );
					$restrictions .= "<li><a href='" . get_category_link( $term->term_id ) . "'>" . $term->name . "</a></li>";
				}
			}

			$restrictions .= "</ul>";

		}

		// Products Disallowed
		if( ! empty( $couponObj->get_excluded_product_ids() ) || ! empty( $couponObj->get_excluded_product_categories() ) ) {

			$restrictions .= "<b>Invalid for:</b><ul>";
			if( ! empty( $couponObj->get_excluded_product_ids() ) ) {
				// Products
				$restrictions .= 'Products: ';
				foreach ( $couponObj->get_excluded_product_ids() as $productId ) {
					$product = wc_get_product( $productId );
					$restrictions .= "<li><a href='" . get_permalink( $productId ) . "'>" . $product->get_name() . "</a></li>";
				}
			}

			if( ! empty( $couponObj->get_excluded_product_categories() ) ) {
				// Categories
				$restrictions .= 'Categories: ';
				foreach ( $couponObj->get_excluded_product_categories() as $productCatId ) {
					$term = get_term_by( 'id', $productCatId, 'product_cat' );
					$restrictions .= "<li><a href='" . get_category_link( $term->term_id ) . "'>" . $term->name . "</a></li>";
				}
			}

			$restrictions .= "</ul>";

		}

		$expiryDate = "<b>Expires on: </b>" . $couponObj->get_date_expires()->date_i18n( 'd M, Y' );

		// Details
		$couponDescription = "<h2>Coupon code: " . $couponObj->get_code() . "</h2>
		<i>" . $couponObj->get_description() . "</i><br>
		<b>Discount Available: $couponDiscount</b><br>
		$conditions
		$restrictions
		$expiryDate";

		return $couponDescription;
	}

	public function woo_payment_complete( $order_id ) {
		
		$order = wc_get_order( $order_id );
		$products = $order->get_items();
		$userObj = $order->get_user();
		$triggerEvent = 'reminder';
		$mailer = WC()->mailer()->get_emails();
		
		$productList = '<ul>';
		foreach ( $products as $product ) {
			$productList .= "<li><a href='" . get_permalink( $product->get_id() ) . "'>" . $product->get_name() . "</a></li>";
		}
		$productList .= '</ul>';

		if( $userObj ) {
			
			$configuration = HelperFunctions::get_basic_configuration();

			$emailDetails['email_subject'] = $configuration[$triggerEvent.'_email_subject'];
			$emailDetails['email_body'] = $configuration[$triggerEvent.'_email_content'];
			$emailDetails['email_type'] = $configuration[$triggerEvent.'_email_type'];
			$emailDetails['email_address'] = $userObj->user_email;
			$emailDetails['tags'] = [
				'site_title' => get_bloginfo( 'name' ),
				'customer_name' => ucfirst( $userObj->user_nicename ),
				'customer_email' => $userObj->user_email
			];
			
			$emailDetails['tags']['product_name'] = $productList;

			// Send the email
			return $mailer['MIGHTY_Coupon_Email']->trigger( $emailDetails );
		}
		
	}

}

// Instantiate Mighty_Woocommerce Class
Mighty_Woocommerce::instance();
