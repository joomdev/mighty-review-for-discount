<?php
/**
 * Mighty Review For Discount
 * Licence Page
 */
    
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

include_once MIGHTY_RFD_DIR_PATH . 'includes/header.php';
?>

<form class="mighty-go-pro" action="" id="mighty-go-pro" method="POST">

    <div class="mighty-licence-box">
        <div class="product-name">Mighty Review For Discount</div>
        
        <div class="licence-controls">
            <input type="text" class="regular-text" name="mrfd-licence-key" value="<?php echo $data['licence'] ?>">

            <?php if( $data['status'] ) : ?>
            <?php wp_nonce_field( 'mighty_rfd_nonce', 'mighty_rfd_nonce' ); ?>
            <input type="submit" name="mighty-deactivate-licence" id="submit" class="button-secondary" value="Deactivate Licence">
            <?php endif; ?>
        </div>

        <?php if( ! empty( $data['licenceMsg'] ) ) : ?>
        <div class="licence-msg">
            <?php echo $data['licenceMsg']; ?>
        </div>
        <?php endif; ?>

    </div>
    <?php wp_nonce_field( 'mighty_rfd_nonce', 'mighty_rfd_nonce' ); ?>
    <p><input type="submit" name="mighty-go-pro" id="submit" class="button button-primary" value="Save Changes"></p>
</form>