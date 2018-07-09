<?php defined( 'ABSPATH' ) || die; ?>
<p><em>A token has been saved from the last CC transaction of this customer. You can use it to charge the same CC without knowing the CC number, expiration or security code.</em></p>

<?php require WC_RENTPAYMENT_PLUGIN_PATH . '/templates/token-fields.php'; ?>

<?php require WC_RENTPAYMENT_PLUGIN_PATH . '/templates/charge-amount-field.php'; ?>
<div class="field-container">
    <button type="button" id="charge-token-btn" class="button button-primary button-large field">Charge</button>
    <img id="charge-token-spinner" src="./images/spinner.gif" style="display:none"/>
</div>