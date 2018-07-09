<?php
/*
Plugin Name: WooCommerce Rent Payment Gateway
Plugin URI: https://github.com/mkaboldy/woocommerce-rent-payment
Description: Adds Rent Payment to WooCommerce payment methods
Version: 0.5
Author: Miklos Kaboldy
WC tested up to: 3.4
Text Domain: wc-rent-payment
 */

if (!defined('ABSPATH')) {
    exit;
}

define( 'WC_RENTPAYMENT_PLUGIN_VERSION','0.5');
define( 'WC_RENTPAYMENT_PLUGIN_PATH' , dirname( __FILE__ ));
define( 'WC_RENTPAYMENT_PLUGIN_URL' , plugins_url('', __FILE__ ));


// Make sure WooCommerce is active

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;

/**
 * Add the gateway to WC Available Gateways
 *
 * @param array $gateways all available WC gateways
 * @return array $gateways all WC gateways + offline gateway
 */
function wc_rent_payment_add_to_gateways( $gateways ) {
	$gateways[] = 'WC_Rent_Payment_Gateway';
	return $gateways;
}

add_filter( 'woocommerce_payment_gateways', 'wc_rent_payment_add_to_gateways' );

/**
 * Adds plugin page links
 *
 * @param array $links all plugin links
 * @return array $links all plugin links + our custom links (i.e., "Settings")
 */
function wc_rent_payment_gateway_plugin_links( $links ) {
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=rentpayment' ) . '">' . __( 'Configure', WC_Rent_Payment_Gateway::TEXTDOMAIN ) . '</a>'
	);
	return array_merge( $plugin_links, $links );
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wc_rent_payment_gateway_plugin_links' );

/**
 * Loads the required classes
 */
function wc_rent_payment_gateway_init() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class.wc_rent_payment_gateway.php';
    require_once plugin_dir_path( __FILE__ ) . 'includes/class.rentpayment_api.php';
}

add_action( 'plugins_loaded', 'wc_rent_payment_gateway_init', 11 );

