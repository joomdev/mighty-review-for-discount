<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="title-box">

    <div class="mighty-brand">

        <div class="brand">
            <img class="logo" src="<?php echo MIGHTY_RFD_PLG_URL . 'assets/images/mighty-rfd-logo.png'; ?>" alt="Mighty Review For Discount logo">
            <span class="mighty-product-name">Mighty Review For Discount | v<?php echo MIGHTY_RFD_VERSION; ?></span>
        </div>

        <a href="https://mightythemes.com" target="_BLANK" class="mighty-more-themes-plugins-button"><span class="dashicons dashicons-cart"></span> More WP Themes &amp; Plugins</a>

    </div>
    
</div>

<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link<?php echo isset( $_GET['page'] ) && $_GET['page'] == 'mighty-rfd-basic_configuration' ? ' active' : ''; ?>" aria-current="page" href="<?php echo admin_url('admin.php?page=mighty-rfd-basic_configuration'); ?>">Basic Configuration</a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link<?php echo ( isset( $pagenow) && $pagenow == 'edit.php' && ( isset( $_REQUEST['post_type'] ) && $_REQUEST['post_type'] == 'mighty-discount' ) ) ? ' active' : ''; ?>" href="<?php echo admin_url('edit.php?post_type=mighty-discount'); ?>">Review Discounts</a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link<?php echo ( isset( $pagenow) && $pagenow == 'post-new.php' && ( isset( $_REQUEST['post_type'] ) && $_REQUEST['post_type'] == 'mighty-discount' ) ) ? ' active' : ''; ?>" href="<?php echo admin_url('post-new.php?post_type=mighty-discount'); ?>">New Review Discount</a>
    </li>

    <li class="nav-item">
        <a class="nav-link<?php echo isset( $_GET['page'] ) && $_GET['page'] == 'mighty-rfd-go-pro' ? ' active' : ''; ?>" href="<?php echo admin_url('admin.php?page=mighty-rfd-go-pro'); ?>">Go Pro 🚀</a>
    </li>

    <div class="docs-link">
        <a target="_blank" href="https://mightythemes.com/docs/docs-category/mighty-rfd"><span class="dashicons dashicons-media-document"></span> Documentation</a>
        <a target="_blank" href="https://mightythemes.com/support/c/mighty-review-for-discount"><span class="dashicons dashicons-editor-help"></span> Help</a>
    </div>
</ul>

