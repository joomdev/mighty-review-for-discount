<?php
/**
 * Mighty Review For Discount
 * HomePage
 */
    
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

include_once MIGHTY_RFD_DIR_PATH . 'includes/header.php';
?>

<form action="" id="mighty-basic-configuration" method="POST">
    <div class="mt-rfd-blocks">
        
        <div class="mt-rfd-block">
            <h2 class="block-heading">Review For Discounts Settings</h2>

            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="enable_rfd">Enable Mighty Review For Discounts</label>
                        </th>
                        <td>
                            <input name="enable_rfd" id="enable_rfd" type="checkbox" value="1" <?php checked( 1, $data['enable_rfd'], true ); ?>>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="trigger_discount">Trigger Discount </label>
                        </th>
                        <td>
                            <select name="trigger_discount" id="trigger_discount">
                                <option value="posted" <?php selected( $data['trigger_discount'], 'posted' ); ?>>When review posted</option>
                                <option value="" disabled>When review approved <span class="pro-tag">[PRO]</span></option>
                            </select>
                            <p class="description">Choose when coupon will be sent to the users</p>
                        </td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="delete_expired_coupons">Delete Expired Coupons Automatically</label>
                        </th>
                        <td>
                            <input name="delete_expired_coupons" id="delete_expired_coupons" type="checkbox" value="1" <?php checked( 1, $data['delete_expired_coupons'], true ); ?>> 
                            <p class="description">Created by Mighty Review For Discount</p>
                            <p><button class="button delete-expired-coupons">Delete expired coupons</button></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-rfd-block">
            <h2 class="block-heading">Review Reminder Email</h2>

            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="reminder_email_type">Email Type</label>
                        </th>
                        <td>
                            <select name="reminder_email_type" id="reminder_email_type">
                                <option value="plain" <?php selected( $data['reminder_email_type'], 'plain' ); ?>>Plain</option>
                                <option value="html" <?php selected( $data['reminder_email_type'], 'html' ); ?>>HTML</option>
                            </select>
                            <p class="description">Choose which email format you want to use</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="reminder_email_subject">Email Subject</label>
                        </th>
                        <td>
                            <input class="regular-text" name="reminder_email_subject" id="reminder_email_subject" type="text" value="<?php echo esc_attr( $data['reminder_email_subject'] ); ?>">
                            <br>
                            <span class="description">Use Shortcodes:<br><b>{site_title}</b> | <b>{customer_name}</b></span>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="reminder_email_content">Email Content</label>
                        </th>
                        <td>
                            <textarea class="regular-text" name="reminder_email_content" id="reminder_email_content" cols="30" rows="10"><?php echo esc_attr( $data['reminder_email_content'] ); ?></textarea>
                            <br>
                            <span class="description">Use Shortcodes:<br><b>{site_title}</b> | <b>{customer_name}</b> | <b>{customer_email}</b> | <b>{product_name}</b></span>
                        </td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="reminder_email_test_email">Test Email</label>
                        </th>
                        <td>
                            <input class="regular-text" name="reminder_email_test_email" id="reminder_email_test_email" type="email">
                            <p><button class="button test-email" data-trigger="reminder" data-target="reminder_email_test_email">Send Test Email</button></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-rfd-block">
            <h2 class="block-heading">Email Settings for the Single Review</h2>

            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="single_review_email_type">Email Type</label>
                        </th>
                        <td>
                            <select name="single_review_email_type" id="single_review_email_type">
                                <option value="plain" <?php selected( $data['single_review_email_type'], 'plain' ); ?>>Plain</option>
                                <option value="html" <?php selected( $data['single_review_email_type'], 'html' ); ?>>HTML</option>
                            </select>
                            <p class="description">Choose which email format you want to use</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="single_review_email_subject">Email Subject</label>
                        </th>
                        <td>
                            <input class="regular-text" name="single_review_email_subject" id="single_review_email_subject" type="text" value="<?php echo esc_attr( $data['single_review_email_subject'] ); ?>">
                            <br>
                            <span class="description">Use Shortcodes:<br><b>{site_title}</b> | <b>{customer_name}</b></b></span>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="single_review_email_content">Email Content</label>
                        </th>
                        <td>
                            <textarea class="regular-text" name="single_review_email_content" id="single_review_email_content" cols="30" rows="10"><?php echo esc_attr( $data['single_review_email_content'] ); ?></textarea>
                            <br>
                            <span class="description">Use Shortcodes:<br><b>{site_title}</b> | <b>{customer_name}</b> | <b>{customer_email}</b> | <b>{product_name}</b> | <b>{coupon_description}</b></span>
                        </td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="single_review_test_email">Test Email</label>
                        </th>
                        <td>
                            <input class="regular-text" name="single_review_test_email" id="single_review_test_email" type="email">
                            <p><button class="button test-email" data-trigger="single_review" data-target="single_review_test_email">Send Test Email</button></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-rfd-block">
            <h2 class="block-heading">Email Settings for the Multiple Reviews <span class="pro-tag">[PRO]</span></h2>

            <table class="form-table">
                <p>Available in Pro. <a target="_blank" href="https://mightythemes.com/product/mighty-review-for-discount">Upgrade today</a>.</p>
            </table>
        </div>

        <div class="mt-rfd-block">
            <h2 class="block-heading">Email Settings for Customer Close to the Target <span class="pro-tag">[PRO]</span></h2>

            <table class="form-table">
                <p>Available in Pro. <a target="_blank" href="https://mightythemes.com/product/mighty-review-for-discount">Upgrade today</a>.</p>
            </table>
        </div>
        
    </div>

    <p><input type="submit" name="mighty-basic-configuration" id="submit" class="button button-primary" value="Save Changes"></p>
</form>