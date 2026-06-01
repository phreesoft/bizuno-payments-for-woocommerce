<?php
/**
 * Plugin Name:       Bizuno Payments for WooCommerce
 * Description:       PayFabric credit-card and Bizuno Purchase Order payment gateways for WooCommerce.
 * Version:           7.4.2
 * Requires at least: 6.5
 * Tested up to:      7.0
 * Requires PHP:      8.0
 * Requires Plugins:  woocommerce
 * Author:            PhreeSoft, Inc./Global Payments
 * Author URI:        https://www.phreesoft.com
 * Text Domain:       bizuno-payments-for-woocommerce
 * License:           AGPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/agpl-3.0.txt
 * WC requires at least: 8.0
 * WC tested up to:   9.4
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }


/*Define live and test gateway host */
!defined('LIVEGATEWAY') && define('LIVEGATEWAY', 'https://www.payfabric.com');
!defined('TESTGATEWAY') && define('TESTGATEWAY', 'https://sandbox.payfabric.com');

/*
* Define log dir, severity level of logging mode and whether enable on-screen debug ouput.
* PLEASE DO NOT USE "DEBUG" LOGGING MODE IN PRODUCTION
*/
!defined('PayFabric_LOG_SEVERITY') && define('PayFabric_LOG_SEVERITY', 'INFO');
!defined('PayFabric_LOG_DIR')      && define('PayFabric_LOG_DIR', dirname(__FILE__) . '/logs');
!defined('PayFabric_DEBUG')        && define('PayFabric_DEBUG', false);

class bizuno_payments
{
    public function __construct()
    {
        // Declare WooCommerce HPOS (custom order tables) compatibility; order access here uses
        // the CRUD API (wc_get_order / get_meta / update_status), which is HPOS-safe.
        add_action( 'before_woocommerce_init', function() {
            if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
            }
        } );
        // Actions
        add_action ( 'plugins_loaded',                                    [ $this, 'bizuno_payments_plugins_loaded' ] );
        add_action ( 'woocommerce_checkout_process',                      [ $this, 'bizuno_payments_payment' ] );
        add_action ( 'woocommerce_checkout_update_order_meta',            [ $this, 'bizuno_payments_update_order_meta' ] );
        add_action ( 'woocommerce_admin_order_data_after_billing_address',[ $this, 'bizuno_payments_order_meta' ], 10, 1 );
        // Filters
        add_filter ( 'woocommerce_payment_gateways',                      [ $this, 'bizuno_payments_add_to_gateways' ] );
        add_filter ( 'woocommerce_available_payment_gateways',            [ $this, 'bizuno_api_disable_purchorder' ], 99, 1);
    }

    public function bizuno_payments_plugins_loaded()
    {
        if ( ! is_plugin_active ( 'woocommerce/woocommerce.php' ) ) { return; }
        require ( plugin_dir_path ( __FILE__ ) . 'purchase_order.php' );
//require ( plugin_dir_path ( __FILE__ ) . 'payment-payfabric/admin/class-payfabric-gateway.php' );
        WC()->frontend_includes();
        if ( class_exists ( 'WC_Payment_Gateway' ) ) { // get instance of WooCommerce for Payfabric
            require ( plugin_dir_path ( __FILE__ ) . 'plugins/payment-payfabric/classes/class-payfabric-gateway-woocommerce.php' );
            Payfabric_Gateway_Woocommerce::get_instance();
        }
    }

    public function bizuno_payments_add_to_gateways( $gateways )
    {
        $gateways[] = 'WC_Gateway_PayFabric';
        $gateways[] = 'WC_Gateway_PurchOrder';
        return $gateways;
    }
    
    public function bizuno_payments_payment()
    {
        // These run on WooCommerce's checkout_process hook, which fires only after
        // WooCommerce has verified the checkout nonce — hence the nonce-ignore annotations.
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified by WooCommerce checkout
        $method = isset( $_POST['payment_method'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_method'] ) ) : '';
        if ( 'custom' !== $method ) { return; }
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified by WooCommerce checkout
        $mobile      = isset( $_POST['mobile'] )      ? sanitize_text_field( wp_unslash( $_POST['mobile'] ) )      : '';
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified by WooCommerce checkout
        $transaction = isset( $_POST['transaction'] ) ? sanitize_text_field( wp_unslash( $_POST['transaction'] ) ) : '';
        if ( '' === $mobile )      { wc_add_notice( __( 'Please add your mobile number', 'bizuno-payments-for-woocommerce' ), 'error' ); }
        if ( '' === $transaction ) { wc_add_notice( __( 'Please add your transaction ID', 'bizuno-payments-for-woocommerce' ), 'error' ); }
    }

    public function bizuno_payments_update_order_meta( $order_id ) // Persist the custom checkout fields on the order
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified by WooCommerce checkout
        $method = isset( $_POST['payment_method'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_method'] ) ) : '';
        if ( 'custom' !== $method ) { return; }
        $order = wc_get_order( $order_id );
        if ( ! $order ) { return; }
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified by WooCommerce checkout
        $mobile      = isset( $_POST['mobile'] )      ? sanitize_text_field( wp_unslash( $_POST['mobile'] ) )      : '';
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified by WooCommerce checkout
        $transaction = isset( $_POST['transaction'] ) ? sanitize_text_field( wp_unslash( $_POST['transaction'] ) ) : '';
        // Use the order CRUD API (HPOS-safe) instead of update_post_meta(), which would write
        // to the wrong place on High-Performance Order Storage stores.
        $order->update_meta_data( 'mobile', $mobile );
        $order->update_meta_data( 'transaction', $transaction );
        $order->save();
    }

    public function bizuno_payments_order_meta( $order ) {
        // Safety check: Ensure $order is a valid WC_Order object
        if ( ! is_a( $order, 'WC_Order' ) ) { return; }
        $method = $order->get_payment_method();  // Modern getter (replaces get_post_meta for _payment_method)
        if ( $method !== 'custom' ) { return; }
        // Use get_meta() for custom fields (stored via $order->update_meta_data() elsewhere)
        $mobile     = $order->get_meta( 'mobile', true );
        $transaction = $order->get_meta( 'transaction', true );
        // Output (unchanged except esc_html wrapping)
        echo '<p><strong>' . esc_html( __( 'Mobile Number', 'bizuno-payments-for-woocommerce' ) ) . ':</strong> ' . esc_html( $mobile ) . '</p>';
        echo '<p><strong>' . esc_html( __( 'Transaction ID', 'bizuno-payments-for-woocommerce' ) ) . ':</strong> ' . esc_html( $transaction ) . '</p>';
    }

    public function bizuno_api_disable_purchorder( $available_gateways ) { // Disable PO Method if the user is not logged in or doesn't have a contact ID link to Bizuno
        $disable = false;
        $user = wp_get_current_user(); // Check to see if user has permission to use this method
        if (empty($user)) { $disable = true; } // not logged in, we're done
        else {
            $cID = (int)get_user_meta( $user->ID, 'bizuno_payment_allow_po', true); // bizuno_wallet_id
            if (empty($cID)) { $disable = true; } // not linked to Bizuno contact, we're done
        }
        if ( $disable ) { unset($available_gateways['purchorder']); }
        return $available_gateways;
    }
}
new bizuno_payments();
