<?php
/**
 * Provides settings inputs for admin area view for the plugin
 * This file is used to markup the admin-facing aspects of the plugin.
 * @since      1.0.0
 * @package    PayFabric_Gateway_Woocommerce
 * @subpackage PayFabric_Gateway_Woocommerce/admin
 */
if (!defined('ABSPATH')) {
    exit;
}
// This file is include()'d from WC_Gateway_PayFabric::init_form_fields(), so the variables
// below are function-local (assigned to $this->form_fields), not true globals. The prefix
// sniff cannot see the include context and reports false positives; disable it for this file.
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
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

/*Define the control parameter value to determine whether the LOG functionality show or not */
$show_log_field = '0';
/*Define the control parameter value to determine whether the AUTH functionality show or not */
$show_auth_fields = '1';
/*Define whether integration mode should be shown or not, 1 means to show, 0 means not */
$integration_show = '1';


$admin_fields_array = array();
$admin_fields_array['enabled'] = array(
    'title' => __('Enable/Disable', 'bizuno-payments-for-woocommerce'),
    'type' => 'checkbox',
    'label' => __('Enable PayFabric gateway', 'bizuno-payments-for-woocommerce'),
    'description' => __('Enable or disable the gateway.', 'bizuno-payments-for-woocommerce'),
    'desc_tip' => true,
    'default' => 'no'
);
$admin_fields_array['title'] = array(
    'title' => __('Title', 'bizuno-payments-for-woocommerce'),
    'type' => 'text',
    'description' => __('The title which the user sees during checkout.', 'bizuno-payments-for-woocommerce'),
    'desc_tip' => true,
    'default' => __('PayFabric', 'bizuno-payments-for-woocommerce')
);

$admin_fields_array['description'] = array(
    'title' => __('Description', 'bizuno-payments-for-woocommerce'),
    'type' => 'textarea',
    'description' => __('The description which the user sees during checkout.', 'bizuno-payments-for-woocommerce'),
    'desc_tip' => true,
    'default' => __("Pay via PayFabric", 'bizuno-payments-for-woocommerce')
);

$admin_fields_array['testmode'] = array(
    'title' => __('PayFabric test mode', 'bizuno-payments-for-woocommerce'),
    'type' => 'checkbox',
    'label' => __('Enable test mode', 'bizuno-payments-for-woocommerce'),
    'description' => __('Enable or disable the test mode for the gateway to test the payment method.', 'bizuno-payments-for-woocommerce'),
    'desc_tip' => true,
    'default' => 'yes'
);
$admin_fields_array['advanced'] = array(
    'title' => __('Advanced options', 'bizuno-payments-for-woocommerce'),
    'type' => 'title',
    'description' => '',
);
$admin_fields_array['api_merchant'] = array(
    'title' => __('Merchant data', 'bizuno-payments-for-woocommerce'),
    'type' => 'title',
    'description' => __('In this section You can set up your merchant data for PayFabric system.', 'bizuno-payments-for-woocommerce')
);
$admin_fields_array['api_merchant_id'] = array(
    'title' => __('Device ID', 'bizuno-payments-for-woocommerce'),
    'type' => 'text',
    'description' => __('Device ID from PayFabric', 'bizuno-payments-for-woocommerce'),
    'desc_tip' => true,
    'default' => ''
);
$admin_fields_array['api_password'] = array(
    'title' => __('Password', 'bizuno-payments-for-woocommerce'),
    'type' => 'password',
    'description' => __('Device password from PayFabric', 'bizuno-payments-for-woocommerce'),
    'desc_tip' => true,
    'default' => ''
);

if ($integration_show) {
    $admin_fields_array['api_payment_modes'] = array(
        'title' => __('Payment mode', 'bizuno-payments-for-woocommerce'),
        'type' => 'select',
        // translators: %1$s: opening <a> tag linking to the setup guide; %2$s: closing </a> tag.
        'description' => sprintf(__('Payment Mode controls the presentation of the Hosted Payment Page (HPP):<br>
            &nbsp;&nbsp;&nbsp;&nbsp;<b>• Direct:</b> HPP shown directly on the checkout page, payment made when placing order. (A theme is required, see %1$sGuide%2$s).<br>
            &nbsp;&nbsp;&nbsp;&nbsp;<b>• Iframe:</b> HPP is inside the shopping site page.<br>
            &nbsp;&nbsp;&nbsp;&nbsp;<b>• Redirect:</b> Shopping site redirects user to the HPP.', 'bizuno-payments-for-woocommerce'), '<a href="https://github.com/PayFabric/WooCommerce-Plugin#readme" target="_blank">', '</a>' ),
        'desc_tip' => false,
        'default' => 2,
        'options' => array(
            2 => __('Direct', 'bizuno-payments-for-woocommerce'),
            0 => __('Iframe', 'bizuno-payments-for-woocommerce'),
            1 => __('Redirect', 'bizuno-payments-for-woocommerce')
        )
    );
}

if ($show_auth_fields) {
    //Purchase or Auth
    $admin_fields_array['api_payment_action'] = array(
        'title' => __('Payment action', 'bizuno-payments-for-woocommerce'),
        'type' => 'select',
        'description' => __('Specify transaction type.', 'bizuno-payments-for-woocommerce'),
        'desc_tip' => true,
        'default' => 0,
        'options' => array(
            __('Purchase', 'bizuno-payments-for-woocommerce'),
            __('Auth', 'bizuno-payments-for-woocommerce')
        )
    );
}
//choose the default paid order status
$admin_fields_array['api_success_status'] = array(
    'title' => __('Success status', 'bizuno-payments-for-woocommerce'),
    'type' => 'select',
    'description' => __('Status of order after successful payment.', 'bizuno-payments-for-woocommerce'),
    'desc_tip' => true,
    'default' => 0,
    'options' => array(
        __('Processing', 'bizuno-payments-for-woocommerce'),
        __('Completed', 'bizuno-payments-for-woocommerce')
    )
);

if ($show_log_field) {
    $admin_fields_array['log_mode'] = array(
        'title' => __('Logging', 'bizuno-payments-for-woocommerce'),
        'type' => 'checkbox',
        'label' => __('Enable log debug', 'bizuno-payments-for-woocommerce'),
        'description' => __('Log payment events, such as gateway transaction callback, if enabled, log file will be found inside: wp-content/uploads/wc-logs', 'bizuno-payments-for-woocommerce'),
        'desc_tip' => false,
        'default' => 'no'
    );
}


return $admin_fields_array;
