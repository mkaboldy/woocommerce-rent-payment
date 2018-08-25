<?php defined( 'ABSPATH' ) || die; ?>
<?php if (isset($plain_text) && $plain_text) {
          echo "\nWhen processing this order, the payment gateway was in sandbox mode. The credit card was not charged.\n";

      } else {
?>
<div>
    <p>
        <em>When processing this order, the payment gateway was in sandbox mode. The credit card was not charged.</em>
    </p>
</div>
<?php }?>