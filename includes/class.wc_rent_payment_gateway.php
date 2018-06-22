<?php
/**
 * Implements the Rent Payment gateway for WooCommerce
 * */
class WC_Rent_Payment_Gateway extends WC_Payment_Gateway {
    private $sandbox;
    private $api_url;
    private $api_username;
    private $api_password;
    private $property_code;

    // option constants
    public static $OPTION_SANDBOX = 'sandbox';
    public static $OPTION_API_URL = 'api_url';
    public static $OPTION_API_USERNAME = 'api_username';
    public static $OPTION_API_PASSWORD = 'api_password';
    public static $OPTION_PROPERTY_CODE = 'property_code';

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

        $this->sandbox  = $this->get_option( self::$OPTION_SANDBOX );
        if ($this->sandbox == 'yes') {
            $this->api_url = 'https://demo.rentpayment.com/api/1';
            $this->api_username = 'actioncorporatemgr';
            $this->api_password = 'aww2aygc';
            $this->property_code = 'JMZG88AI67';

            $this->method_title     .= ' - ' . __('sandbox mode','wc-rent-payment');
            $this->title            .= ' - ' . __('sandbox mode','wc-rent-payment');
        } else {
            $this->api_url = $this->get_option(self::$OPTION_SANDBOX);
            $this->api_username = $this->get_option(self::$OPTION_API_USERNAME);
            $this->api_password = $this->get_option(self::$OPTION_API_PASSWORD);
            $this->property_code = $this->get_option(self::$OPTION_PROPERTY_CODE);
        }

        // Actions
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

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

            self::$OPTION_API_URL => array(
                'title'   => __( 'API URL', 'wc-rent-payment' ),
                'type'    => 'text',
                'label'   => __( 'Endpoint of the payment gateway API', 'wc-rent-payment' ),
            ),

            self::$OPTION_API_USERNAME => array(
                'title'   => __( 'API user name', 'wc-rent-payment' ),
                'type'    => 'text',
                'label'   => __( 'User name required for API authentication', 'wc-rent-payment' ),
            ),

            self::$OPTION_API_PASSWORD => array(
                'title'   => __( 'API password', 'wc-rent-payment' ),
                'type'    => 'password',
                'label'   => __( 'Password required for API authentication', 'wc-rent-payment' ),
            ),

            self::$OPTION_PROPERTY_CODE => array(
                'title'   => __( 'API Property Code', 'wc-rent-payment' ),
                'type'    => 'text',
                'label'   => __( 'Propery codre required for API payment identification', 'wc-rent-payment' ),
            ),

            self::$OPTION_SANDBOX => array(
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

            // Reduce stock levels
            $order->reduce_order_stock();

            // Remove cart
            WC()->cart->empty_cart();

        } catch(Exception $e) {

        }

        // Return thankyou redirect
        return array(
            'result'    => 'success',
            'redirect'  => $this->get_return_url( $order )
        );
    }
} // end WC_Rent_Payment class
?>