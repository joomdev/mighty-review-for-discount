<?php
/**
 * Mighty Review For Discount
 * New Review Discount Page
 */
    
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$allProducts = \MightyRFD\Classes\HelperFunctions::get_products_ids();
$allCategories = \MightyRFD\Classes\HelperFunctions::get_product_categories();
?>

<div class="mt-rfd-blocks">

    <div class="mt-rfd-blocks">

        <div class="mt-rfd-block">
            <h2 class="block-heading">Review Discount Options</h2>

            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="coupon_description">Coupon Description</label>
                        </th>
                        <td>
                            <textarea class="regular-text" name="coupon_description" id="coupon_description" cols="30" rows="10"><?php echo get_post_meta( $post->ID, 'mighty_coupon_description', true ); ?></textarea>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="triggering_event">Triggering Event</label>
                        </th>
                        <td>
                            <select name="triggering_event" id="triggering_event">
                                <option value="single_review" <?php selected( get_post_meta( $post->ID, 'mighty_triggering_event', true ), 'single_review' ); ?>>Single Review</option>
                                <option value="multiple_review" <?php selected( get_post_meta( $post->ID, 'mighty_triggering_event', true ), 'multiple_review' ); ?>>Multiple Review</option>
                            </select>
                            <p class="description">Choose when coupon will be sent to the users</p>
                        </td>
                    </tr>

                    <?php
                    $multipleReviewEnable = get_post_meta( $post->ID, 'mighty_triggering_event', true ) == 'multiple_review';
                    ?>
                    
                    <tr class="multiple-reviews" valign="top" style="display: <?php echo $multipleReviewEnable ? 'table-row' : 'none' ?>">
                        <th scope="row" class="titledesc">
                            <label for="number_of_reviews_required">Number of Reviews Required</label>
                        </th>
                        <td>
                            <input class="regular-text" name="number_of_reviews_required" id="number_of_reviews_required" type="number" value="<?php echo get_post_meta( $post->ID, 'mighty_number_of_reviews_required', true ); ?>" >
                        </td>
                    </tr>

                    <tr class="multiple-reviews" valign="top" style="display: <?php echo $multipleReviewEnable ? 'table-row' : 'none' ?>">
                        <th scope="row" class="titledesc">
                            <label for="send_email_notif">Send Email Notification to Achieve Target</label>
                        </th>
                        <td>
                            <input name="send_email_notif" id="send_email_notif" type="checkbox" value="1" <?php checked( 1, get_post_meta( $post->ID, 'mighty_send_email_notif', true ), true ); ?>>
                            <p class="description">Send an email from a certain amount of review to get to the required quantity to encourage users to write more reviews</p>
                        </td>
                    </tr>

                    <?php $reviewNotifEnable = get_post_meta( $post->ID, 'mighty_send_email_notif', true ); ?>

                    <tr class="multiple-reviews reviews-required" valign="top" style="display: <?php echo $multipleReviewEnable && $reviewNotifEnable ? 'table-row' : 'none' ?>">
                        <th scope="row" class="titledesc">
                            <label for="reviews_required_for_notif">How Many Reviews Required to Start Sending Notification </label>
                        </th>
                        <td>
                            <input class="regular-text" name="reviews_required_for_notif" id="reviews_required_for_notif" type="number" value="<?php echo get_post_meta( $post->ID, 'mighty_reviews_required_for_notif', true ); ?>" >
                        </td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="discount_type">Discount Type</label>
                        </th>
                        <td>
                            <select name="discount_type" id="discount_type">
                                <option value="fixed_cart" <?php selected( get_post_meta( $post->ID, 'mighty_discount_type', true ), 'fixed_cart' ); ?>>Fixed Cart Amount</option>
                                <option value="fixed_product" <?php selected( get_post_meta( $post->ID, 'mighty_discount_type', true ), 'fixed_product' ); ?>>Fixed Product Amount</option>
                                <option value="percent" <?php selected( get_post_meta( $post->ID, 'mighty_discount_type', true ), 'percent' ); ?>>Percentage</option>
                            </select>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="coupon_amount">Coupon Amount/Percentage</label>
                        </th>
                        <td>
                            <input class="regular-text" name="coupon_amount" id="coupon_amount" type="text" value="<?php echo get_post_meta( $post->ID, 'mighty_coupon_amount', true ); ?>" >
                        </td>
                    </tr>

                    <tr class="max-discount" valign="top" style="display: <?php echo get_post_meta( $post->ID, 'mighty_discount_type', true ) == 'percent' ? 'table-row' : 'none' ?>">
                        <th scope="row" class="titledesc">
                            <label for="max_discount">Max Discount</label>
                        </th>
                        <td>
                            <input class="regular-text" name="max_discount" id="max_discount" type="text" value="<?php echo get_post_meta( $post->ID, 'mighty_max_discount', true ); ?>" >
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="expire_after_days">Expire After Days</label>
                        </th>
                        <td>
                            <input class="regular-text" name="expire_after_days" id="expire_after_days" type="text" value="<?php echo get_post_meta( $post->ID, 'mighty_expire_after_days', true ); ?>" >
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="enable_free_shipping">Enable Free Shipping</label>
                        </th>
                        <td>
                            <input name="enable_free_shipping" id="enable_free_shipping" type="checkbox" value="1" <?php checked( 1, get_post_meta( $post->ID, 'mighty_enable_free_shipping', true ), true ); ?>>
                            <p class="description">Enable this if the coupon grants free shipping. The free shipping method must be enabled and be set to require <b>a valid free shipping coupon</b>. See the <b>Free Shipping Requires</b> setting.</p>
                        </td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="only_send_to_verified_users">Only Send to Verified Users</label>
                        </th>
                        <td>
                            <input name="only_send_to_verified_users" id="only_send_to_verified_users" type="checkbox" value="1" <?php checked( 1, get_post_meta( $post->ID, 'mighty_only_send_to_verified_users', true ), true ); ?>>
                            <p class="description">Checks whether the customer purchased the specific product.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="single_use_only">Single Use Only?</label>
                        </th>
                        <td>
                            <input name="single_use_only" id="single_use_only" type="checkbox" value="1" <?php checked( 1, get_post_meta( $post->ID, 'mighty_single_use_only', true ), true ); ?>>
                            <p class="description">Enable if coupon should be used once. Once used, it will be expired. </p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="club_with_offers">Club with other offers?</label>
                        </th>
                        <td>
                            <input name="club_with_offers" id="club_with_offers" type="checkbox" value="1" <?php checked( 1, get_post_meta( $post->ID, 'mighty_club_with_offers', true ), true ); ?>>
                            <p class="description">Enable if the coupon cannot be used in conjunction with other coupons.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="exclude_sale_items">Exclude sale items</label>
                        </th>
                        <td>
                            <input name="exclude_sale_items" id="exclude_sale_items" type="checkbox" value="1" <?php checked( 1, get_post_meta( $post->ID, 'mighty_exclude_sale_items', true ), true ); ?>>
                            <p class="description">Enable if  the coupon should not apply to items on sale. Per-item coupons will only work if the item is not on sale. Per-cart coupons will only work if there are items in the cart that are not on sale.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="minimum_spending_amount">Minimum amount to spend</label>
                        </th>
                        <td>
                            <input class="regular-text" name="minimum_spending_amount" id="minimum_spending_amount" type="number" value="<?php echo get_post_meta( $post->ID, 'mighty_minimum_spending_amount', true ); ?>">
                            <p class="description">This field allows you to set minimum subtotal needed to use the discount coupon.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="maximum_spending_amount">Maximum amount to spend</label>
                        </th>
                        <td>
                            <input class="regular-text" name="maximum_spending_amount" id="maximum_spending_amount" type="number" value="<?php echo get_post_meta( $post->ID, 'mighty_maximum_spending_amount', true ); ?>">
                            <p class="description">This field allows you to set maximum subtotal allowed when using the coupon code.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="included_products">Products</label>
                        </th>
                        <td>
                            <select id="included_products" class="mighty-select2 regular-text" name="included_products[]" multiple="multiple">
                                <?php
                                    $selectedIncludedProducts = explode( ',', get_post_meta( $post->ID, 'mighty_included_products', true ) );
                                    foreach( $allProducts as $productId ) {
                                        $product = wc_get_product( $productId );
                                        echo '<option '. selected( in_array( $productId, $selectedIncludedProducts), true ) .' value="' . $productId . '">'. $product->get_formatted_name() .'</option>';
                                    }
                                ?>
                            </select>
                            <p class="description">Products that the coupon will be applied to, or that need to be in the cart in order for the <b>Fixed Cart Amount</b> to be applied.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="excluded_products">Exclude products</label>
                        </th>
                        <td>
                            <select id="excluded_products" class="mighty-select2 regular-text" name="excluded_products[]" multiple="multiple">
                                <?php
                                    $selectedExcludedProducts = explode( ',', get_post_meta( $post->ID, 'mighty_excluded_products', true ) );
                                    foreach( $allProducts as $productId ) {
                                        $product = wc_get_product( $productId );
                                        echo '<option '. selected( in_array( $productId, $selectedExcludedProducts), true ) .' value="' . $productId . '">'. $product->get_formatted_name() .'</option>';
                                    }
                                ?>
                            </select>
                            <p class="description">Products that the coupon will not be applied to, or that can not be in the cart in order for the <b>Fixed Cart Amount</b> to be applied.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="included_categories">Product Categories</label>
                        </th>
                        <td>
                            <select id="included_categories" class="mighty-select2 regular-text" name="included_categories[]" multiple="multiple">
                                <?php
                                    $selectedIncludedCategories = explode( ',', get_post_meta( $post->ID, 'mighty_included_categories', true ) );
                                    foreach( $allCategories as $category ) {
                                        echo '<option '. selected( in_array( $category->term_id, $selectedIncludedCategories), true ) .' value="' . $category->term_id . '">'. $category->name .'</option>';
                                    }
                                ?>
                            </select>
                            <p class="description">Products categories that the coupon will be applied to, or that need to be in the cart in order for the <b>Fixed Cart Amount</b> to be applied.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="excluded_categories">Exclude Categories</label>
                        </th>
                        <td>
                            <select id="excluded_categories" class="mighty-select2 regular-text" name="excluded_categories[]" multiple="multiple">
                                <?php
                                    $selectedExcludedCategories = explode( ',', get_post_meta( $post->ID, 'mighty_excluded_categories', true ) );
                                    foreach( $allCategories as $category ) {
                                        echo '<option '. selected( in_array( $category->term_id, $selectedExcludedCategories), true ) .' value="' . $category->term_id . '">'. $category->name .'</option>';
                                    }
                                ?>
                            </select>
                            <p class="description">Products categories that the coupon will not be applied to, or that can not be in the cart in order for the <b>Fixed Cart Amount</b> to be applied.</p>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

    </div>

</div>