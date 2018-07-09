<?php defined( 'ABSPATH' ) || die; ?>

<div id="wc-rent-payment">
    <div id="charge">
        <div id="charge-notice" style="display:none;"></div>
        <input name="action" type="hidden" value="<?php echo WC_Rent_Payment_Gateway::AJAX_CHARGE; ?>" />
        <input name="order_id" type="hidden" value="<?php echo $post->ID?>" />
        <?php wp_nonce_field( WC_Rent_Payment_Gateway::KEY_NONCE_CHARGE); ?>
        <div id="tabs">
            <ul>
                <li>
                    <a href="#tab-token">Token</a>
                </li>
                <li>
                    <a href="#tab-cc">Credit Card</a>
                </li>
                </ul>
            <div id="tab-token">
                <?php
                if ($token) {
                    require WC_RENTPAYMENT_PLUGIN_PATH . '/templates/charge-token-form.php';
                } else {
                    echo __('Token not available.',WC_Rent_Payment_Gateway::TEXTDOMAIN);
                }
                ?>
            </div>
            <div id="tab-cc">
                <?php require WC_RENTPAYMENT_PLUGIN_PATH . '/templates/charge-cc-form.php'; ?>
            </div>
        </div>
    </div>
</div>
