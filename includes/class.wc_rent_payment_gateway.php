<?php
/**
 * Implements the Rent Payment gateway for WooCommerce
 * */
class WC_Rent_Payment_Gateway extends WC_Payment_Gateway_CC {
    private $sandbox;
    private $api_url;
    private $api_username;
    private $api_password;
    private $property_code;

    // option key constants
    const OPTION_SANDBOX = 'sandbox';
    const OPTION_API_URL = 'api_url';
    const OPTION_API_USERNAME = 'api_username';
    const OPTION_API_PASSWORD = 'api_password';
    const OPTION_PROPERTY_CODE = 'property_code';

    // meta keys
    const ORDER_META_RENTPAYMENT_TOKEN = 'rentpayment_token';

    // action keys
    const ACTION_PROCESS_SUCCESS_API_RESPONSE = 'wcrp/process_success_api_response';

    public function __construct() {

        $this->id = 'rentpayment';
        $this->icon = apply_filters('woocommerce_rentpayment_icon', '');
        $this->has_fields = true;
        $this->method_title = __('Rent Payment','wc-rent-payment');
        $this->method_description = __('Process credit card payments via rentpayment.com','wc-rent-payment');

        $this->init_form_fields();
        $this->init_settings();

        // Define user set variables for parent vars
        $this->title        = $this->get_option( 'title' );
        $this->description  = $this->get_option( 'description' );

        // Define user set variables for private vars

        $this->sandbox  = $this->get_option( self::OPTION_SANDBOX );
        if ($this->sandbox == 'yes') {
            $this->api_url = 'https://demo.rentpayment.com/api/1';
            $this->api_username = 'actioncorporatemgr';
            $this->api_password = 'aww2aygc';
            $this->property_code = 'JMZG88AI67';

            $this->method_title     .= ' - ' . __('sandbox mode','wc-rent-payment');
            $this->title            .= ' - ' . __('sandbox mode','wc-rent-payment');
        } else {
            $this->api_url = $this->get_option(self::OPTION_API_URL);
            $this->api_username = $this->get_option(self::OPTION_API_USERNAME);
            $this->api_password = $this->get_option(self::OPTION_API_PASSWORD);
            $this->property_code = $this->get_option(self::OPTION_PROPERTY_CODE);
        }

        // Action hooks
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_action( 'woocommerce_credit_card_form_fields', array($this,'credit_card_form_fields'),10,2);
        add_action( self::ACTION_PROCESS_SUCCESS_API_RESPONSE, array($this,'process_success_api_response'), 10,2);

    }

    /**
    * Initialize Gateway Settings Form Fields
    */
    public function init_form_fields() {

        $this->form_fields = apply_filters( 'wc_rentpayment_form_fields', array(

            'enabled' => array(
                'title'   => __( 'Enable/Disable', 'wc-rent-payment' ),
                'type'    => 'checkbox',
                'label'   => __( 'Enable Rent Payment gateway', 'wc-rent-payment' ),
                'default' => 'yes'
            ),

            self::OPTION_API_URL => array(
                'title'   => __( 'API URL', 'wc-rent-payment' ),
                'type'    => 'text',
                'label'   => __( 'Endpoint of the payment gateway API', 'wc-rent-payment' ),
            ),

            self::OPTION_API_USERNAME => array(
                'title'   => __( 'API user name', 'wc-rent-payment' ),
                'type'    => 'text',
                'label'   => __( 'User name required for API authentication', 'wc-rent-payment' ),
            ),

            self::OPTION_API_PASSWORD => array(
                'title'   => __( 'API password', 'wc-rent-payment' ),
                'type'    => 'password',
                'label'   => __( 'Password required for API authentication', 'wc-rent-payment' ),
            ),

            self::OPTION_PROPERTY_CODE => array(
                'title'   => __( 'API Property Code', 'wc-rent-payment' ),
                'type'    => 'text',
                'label'   => __( 'Propery codre required for API payment identification', 'wc-rent-payment' ),
            ),

            self::OPTION_SANDBOX => array(
                'title'   => __( 'Sandbox', 'wc-rent-payment' ),
                'type'    => 'checkbox',
                'label'   => __( 'Enable sandbox mode', 'wc-rent-payment' ),
                'default' => 'yes',
                'desc_tip'    => 'In sandbox mode you can accept test payments without processing any credit cards.',
            ),

            'title' => array(
                'title'       => __( 'Title', 'wc-rent-payment' ),
                'type'        => 'text',
                'description' => __( 'This controls the title for the payment method the customer sees during checkout.', 'wc-rent-payment' ),
                'default'     => __( 'Rent Payment', 'wc-rent-payment' ),
                'desc_tip'    => true,
            ),

            'description' => array(
                'title'       => __( 'Description', 'wc-rent-payment' ),
                'type'        => 'textarea',
                'description' => __( 'Payment method description that the customer will see on your checkout.', 'wc-rent-payment' ),
                'default'     => __( 'Please remit payment to Store Name upon pickup or delivery.', 'wc-rent-payment' ),
                'desc_tip'    => true,
            ),
        ));
    }
    /**
    * Process the payment with the API
    * @param string $order_id
    * @return mixed array of process result
    * */
    public function process_payment( $order_id ) {

        try {

            $order = wc_get_order( $order_id );

            // connect API to process CC

            $Rentpayment_API = new RentPayment_API($this->api_url,$this->api_username,$this->api_password,$this->property_code);

            $CC_params = new rentpayment_CC_params();

            // sanitize card type
            $cardtype = $CC_params->get_cardtype_name($_POST[$this->id.'-card-type']);

            if (false == $cardtype) {
                throw new Exception(__('Credit card cannot be accepted. Please use Visa, Mastercard, Amex or Discover.','wc-rent-payment'));
            }

            $CC_params->number = str_replace(' ', '', $_POST[$this->id.'-card-number']); // cc number sanitized
            $CC_params->expiration = substr($_POST[$this->id.'-card-expiry'],0,2) . '-20' . substr($_POST[$this->id.'-card-expiry'],-2) ; // expiration sanitized
            $CC_params->cardholder = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
            $CC_params->type = $cardtype;
            $CC_params->street = $order->get_billing_address_1();
            $CC_params->city = $order->get_billing_city();
            $CC_params->state = $order->get_billing_state();
            $CC_params->zip = $order->get_billing_postcode();
            $CC_params->country = $order->get_billing_country();
            $CC_params->phone = $order->get_billing_phone();
            $CC_params->email = $order->get_billing_email();
            $CC_params->amount = round($order->get_total(),2) * 100; // amount sanitized
            $CC_params->id = 0;
            $CC_params->firstname = $order->get_billing_first_name();
            $CC_params->lastname = $order->get_billing_last_name();

            $API_response = $Rentpayment_API->CreditCardPayment($CC_params); // XML comes from the API

            do_action(self::ACTION_PROCESS_SUCCESS_API_RESPONSE,$API_response,$order);

            // TODO set appropriate status

            // $order->update_status( 'on-hold', __( 'Awaiting offline payment', 'wc-rent-payment' ) );

            // Reduce stock levels
            wc_reduce_stock_levels($order);

            // Remove cart
            WC()->cart->empty_cart();

            $return = array(
                'result'    => 'success',
                'redirect'  => $this->get_return_url( $order ),
            );

        } catch(Exception $e) {
            $errorMessage = $e->getMessage();
            wc_add_notice( $errorMessage, 'error' );
            $return = array(
                'result'    => 'failure',
                'messages'  => $errorMessage,
            );
        }

        return $return;
    }
    /**
     * Adds a hidden Card Type field to the checkout form
     * @param mixed $fields array of defauld fields
     * @param mixed $gateway_id
     * @return mixed array of fields
     */
    public function credit_card_form_fields($fields,$gateway_id) {
        if ($this->id == $gateway_id) {
            $fields = array_merge(
                array(
                    'card-type-field' => '<input id="'.$this->id.'-card-type" type="hidden" name="'.$this->id.'-card-type">',
                    ),
                $fields
                );
        }
        return $fields;
    }

    /**
     * Process the API response
     * - store the token in order meta
     *
     * @param SimpleXMLElement $response the response object
     * @param WC_order $order the order object
     */
    public function process_success_api_response(SimpleXMLElement $response, WC_order $order) {

        $token = (string)$response->token;

        if (!empty($token)) {
            $order->add_meta_data(self::ORDER_META_RENTPAYMENT_TOKEN,$token,true);
            $order->save_meta_data();
        }

    }
	/**
     * Get gateway icon.
     *
     * @access public
     * @return string
     */
	public function get_icon() {
		$icon  = '<img src="' . WC_HTTPS::force_https_url( WC()->plugin_url() . '/assets/images/icons/credit-cards/visa.svg' ) . '" alt="Visa" width="32" />';
		$icon .= '<img src="' . WC_HTTPS::force_https_url( WC()->plugin_url() . '/assets/images/icons/credit-cards/mastercard.svg' ) . '" alt="MasterCard" width="32" />';
		$icon .= '<img src="' . WC_HTTPS::force_https_url( WC()->plugin_url() . '/assets/images/icons/credit-cards/discover.svg' ) . '" alt="Discover" width="32" />';
		$icon .= '<img src="' . WC_HTTPS::force_https_url( WC()->plugin_url() . '/assets/images/icons/credit-cards/amex.svg' ) . '" alt="Amex" width="32" />';
		// $icon .= '<img src="' . WC_HTTPS::force_https_url( WC()->plugin_url() . '/assets/images/icons/credit-cards/jcb.svg' ) . '" alt="JCB" width="32" />';

		return apply_filters( 'woocommerce_gateway_icon', $icon, $this->id );
	}
    /**
     * Add inline validation script to find the card type
     */
    public function payment_fields(){
        if ($this->sandbox) {
?>
    <p>In sandbox mode you can use test CC numbers listed <a href="https://www.paypalobjects.com/en_AU/vhelp/paypalmanager_help/credit_card_numbers.htm" target="_blank">here</a>.</p>

<?php             
        }

        parent::payment_fields();

?>
<script type='text/javascript'>
jQuery(function ($) {
    jQuery('#<?php echo $this->id?>-card-number').on('payment.cardType', function (e, cardType) {
        if (jQuery('#<?php echo $this->id?>-card-number').hasClass('identified')) {
            jQuery('#<?php echo $this->id?>-card-type').val(cardType);
        } else {
            jQuery('#<?php echo $this->id?>-card-type').val('');
        }
    });
});
</script>


<?php 
    }
} // end WC_Rent_Payment class
?>