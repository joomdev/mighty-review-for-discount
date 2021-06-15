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
                                <option value="" disabled>Multiple Reviews [PRO]</option>
                            </select>
                            <p class="description">Choose when coupon will be sent to the users</p>
                        </td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="discount_type">Discount Type</label>
                        </th>
                        <td>
                            <select name="discount_type" id="discount_type">
                                <option value="percent" <?php selected( get_post_meta( $post->ID, 'mighty_discount_type', true ), 'percent' ); ?>>Percentage</option>
                                <option value="" disabled>Fixed Cart Amount [PRO]</option>
                                <option value="" disabled>Fixed Product Amount [PRO]</option>
                            </select>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="coupon_amount">Percentage</label>
                        </th>
                        <td>
                            <input class="regular-text" name="coupon_amount" id="coupon_amount" type="number" value="<?php echo get_post_meta( $post->ID, 'mighty_coupon_amount', true ); ?>" >
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="expire_after_days">Expire After Days</label>
                        </th>
                        <td>
                            <input class="regular-text" name="expire_after_days" id="expire_after_days" type="number" value="<?php echo get_post_meta( $post->ID, 'mighty_expire_after_days', true ); ?>" >
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="enable_free_shipping">Enable Free Shipping [PRO]</label>
                        </th>
                        <td>
                            <input name="enable_free_shipping" id="enable_free_shipping" type="checkbox" disabled>
                            <p class="description">Enable this if the coupon grants free shipping. The free shipping method must be enabled and be set to require <b>a valid free shipping coupon</b>. See the <b>Free Shipping Requires</b> setting.</p>
                        </td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="only_send_to_verified_users">Only Send to Verified Users [PRO]</label>
                        </th>
                        <td>
                            <input name="only_send_to_verified_users" id="only_send_to_verified_users" type="checkbox" disabled>
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
                            <label for="individual_use_only">Individual Use Only?</label>
                        </th>
                        <td>
                            <input name="individual_use_only" id="individual_use_only" type="checkbox" value="1" <?php checked( 1, get_post_meta( $post->ID, 'mighty_individual_use_only', true ), true ); ?>>
                            <p class="description">Enable if the coupon cannot be used in conjunction with other coupons.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="exclude_sale_items">Exclude sale items [PRO]</label>
                        </th>
                        <td>
                            <input name="exclude_sale_items" id="exclude_sale_items" type="checkbox" disabled>
                            <p class="description">Enable if  the coupon should not apply to items on sale. Per-item coupons will only work if the item is not on sale. Per-cart coupons will only work if there are items in the cart that are not on sale.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="minimum_spending_amount">Minimum amount to spend [PRO]</label>
                        </th>
                        <td>
                            <input class="regular-text" name="minimum_spending_amount" id="minimum_spending_amount" type="number" disabled>
                            <p class="description">This field allows you to set minimum subtotal needed to use the discount coupon.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="maximum_spending_amount">Maximum amount to spend [PRO]</label>
                        </th>
                        <td>
                            <input class="regular-text" name="maximum_spending_amount" id="maximum_spending_amount" type="number" disabled>
                            <p class="description">This field allows you to set maximum subtotal allowed when using the coupon code.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="included_products">Products [PRO]</label>
                        </th>
                        <td>
                            <select id="included_products" class="regular-text" name="included_products[]" disabled></select>
                            <p class="description">Products that the coupon will be applied to, or that need to be in the cart in order for the <b>Fixed Cart Amount</b> to be applied.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="excluded_products">Exclude products [PRO]</label>
                        </th>
                        <td>
                            <select id="excluded_products" class="regular-text" name="excluded_products[]" disabled></select>
                            <p class="description">Products that the coupon will not be applied to, or that can not be in the cart in order for the <b>Fixed Cart Amount</b> to be applied.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="included_categories">Product Categories [PRO]</label>
                        </th>
                        <td>
                            <select id="included_categories" class="regular-text" name="included_categories[]" disabled></select>
                            <p class="description">Products categories that the coupon will be applied to, or that need to be in the cart in order for the <b>Fixed Cart Amount</b> to be applied.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="excluded_categories">Exclude Categories [PRO]</label>
                        </th>
                        <td>
                            <select id="excluded_categories" class="regular-text" name="excluded_categories[]" disabled></select>
                            <p class="description">Products categories that the coupon will not be applied to, or that can not be in the cart in order for the <b>Fixed Cart Amount</b> to be applied.</p>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

    </div>

</div>