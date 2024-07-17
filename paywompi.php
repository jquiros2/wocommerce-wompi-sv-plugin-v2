<?php
/*
Plugin Name: WOMPI - El Salvador
Plugin URI: https://github.com/wompisv/wocommerce-wompi-sv-plugin
Description: Plugin WooCommerce para integrar la pasarela de pago Wompi El Salvador
Version: 2.0.0
Author: WOMPI-El Salvador 
Author URI: https://wompi.sv
*/

// 1. Hook Into WooCommerce. With the code below, my gateway becomes part of WooCommerce payment options.
add_action('plugins_loaded', 'woocommerce_paywompi', 0);
function woocommerce_paywompi(){
    if (!class_exists('WC_Payment_Gateway'))
        return; // if the WC payment gateway class 

    //include(plugin_dir_path(__FILE__) . 'paywompi-gateway.php');
    include_once( 'paywompi-gateway.php' );
    add_filter( 'woocommerce_payment_gateways', 'add_WOMPI_payment_gateway' );
    function add_WOMPI_payment_gateway( $methods ) {
      
      $methods[] = 'WOMPI_Payment_Gateway';
      return $methods;
    }
}

add_filter('woocommerce_payment_gateways', 'add_paywompi');

function add_paywompi($gateways) {
  $gateways[] = 'Pay_Wompi_Gateway';
  return $gateways;
}

/**
 * Custom function to declare compatibility with cart_checkout_blocks feature 
*/
function declare_cart_checkout_blocks_compatibility() {
    // Check if the required class exists
    if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
        // Declare compatibility for 'cart_checkout_blocks'
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, true);
    }
}


// Hook the custom function to the 'before_woocommerce_init' action
add_action('before_woocommerce_init', 'declare_cart_checkout_blocks_compatibility');

// Hook the custom function to the 'woocommerce_blocks_loaded' action
add_action( 'woocommerce_blocks_loaded', 'wompione_register_order_approval_payment_method_type' );

/**
 * Custom function to register a payment method type

 */
function wompione_register_order_approval_payment_method_type() {
    // Check if the required class exists
    if ( ! class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
        return;
    }

    // Include the custom Blocks Checkout class
    require_once plugin_dir_path(__FILE__) . 'class-block.php';

    // Hook the registration function to the 'woocommerce_blocks_payment_method_type_registration' action
    add_action(
        'woocommerce_blocks_payment_method_type_registration',
        function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
            // Register an instance of My_Custom_Gateway_Blocks
            $payment_method_registry->register( new Pay_Wompi_Blocks );
        }
    );
}
?>