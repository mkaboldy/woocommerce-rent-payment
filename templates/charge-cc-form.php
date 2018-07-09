<?php defined( 'ABSPATH' ) || die; ?>

<?php require WC_RENTPAYMENT_PLUGIN_PATH . '/templates/card-fields.php'; ?>

<?php require WC_RENTPAYMENT_PLUGIN_PATH . '/templates/charge-amount-field.php'; ?>

<div class="field-container">
    <button type="button" id="charge-cc-btn" class="button button-primary button-large field">Charge</button>
    <img id="charge-cc-spinner" src="./images/spinner.gif" style="display:none" />
</div>