<?php defined( 'ABSPATH' ) || die; ?>

<div class="field-container">
    <label for="cc-num">Card Number</label>
    <input type="tel" id="cc-num" class="field" placeholder="••••••••••••••••" autocomplete="off"/>
</div>
<div class="field-container half">
    <label for="cc-type">Card Type</label>
    <select id="cc-type" class="field">
        <option>Visa</option>
        <option>MasterCard</option>
        <option>Discover</option>
        <option>AmEx</option>
    </select>
</div>
<div class="field-container half">
    <label for="cc-exp">Expiration</label>
    <input type="text" id="cc-exp" class="field" placeholder="MM/YY" autocomplete="off"/>
</div>
<div class="field-container half">
    <label for="cc-cvc">Security Code</label>
    <input type="text" id="cc-cvc" class="field" placeholder="•••" autocomplete="off"/>
</div>
