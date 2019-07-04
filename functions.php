<?php
/**
 * Copyright 2019 Grégory Saive (greg@evias.be - eVias Services)
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Constants used across the child theme
 * 
 * @since 1.0.0
 */
define("SCHEDULER_POST_ID", 735);

// Include child theme source
require_once dirname( __FILE__ ) . '/includes/class-evs-api-response.php';
require_once dirname( __FILE__ ) . '/includes/class-evs-order-listener.php';
require_once dirname( __FILE__ ) . '/includes/class-evs-delay-writer.php';
require_once dirname( __FILE__ ) . '/includes/class-evs-status-listener.php';
require_once dirname( __FILE__ ) . '/includes/class-evs-js-injector.php';
require_once dirname( __FILE__ ) . '/includes/class-evs-css-injector.php';

// Inject CSS and JS into Backend Theme
add_action('wp_enqueue_scripts', [new EVS_CSS_Injector, "setStylesheet"], PHP_INT_MAX);
add_action('in_admin_footer', [new EVS_JS_Injector, "injectOrderListener"]);
//add_action('in_admin_footer', [new EVS_JS_Injector, "injectDelayWriter"]);

// BACKEND API for orders (wp_ajax.php?action=check_orders)
add_action('wp_ajax_check_orders', [new EVS_Order_Listener, "getCountOrders"]);
add_action('wp_ajax_save_delay', [new EVS_Delay_Writer, "saveDelayForPost"]);
//XXX add_action('wp_ajax_fetch_orders', [new EVS_Order_Listener, "getProcessingOrders"]);

// Register new REST API route for delivery status
add_action('rest_api_init', function () {
    register_rest_route('restaurant/v1', '/delivery', array(
        'methods' => 'GET',
        'callback' => [new EVS_Status_Listener, "isDelivering"],
    ));
});

//
// ----------------------------------------
// eVias Custom Delivery Delays
// ----------------------------------------
//

/**
 * Adds 'Delivery Delay' column header to 'Orders' page immediately after 'Total' column.
 *
 * @param string[] $columns
 * @return string[] $new_columns
 */
function evs_add_delivery_delay_column_header( $columns ) {

    $new_columns = array();

    foreach ( $columns as $column_name => $column_info ) {

        $new_columns[ $column_name ] = $column_info;

        if ( 'order_total' === $column_name ) {
            $new_columns['delivery_delay'] = 'Wartezeit';
        }
    }

    return $new_columns;
}

function evs_add_delivery_delay_column_content( $column ) {
    global $post;
    global $wpdb;

    if ( 'delivery_delay' === $column ) {

        $delay = (new EVS_Delay_Writer)->getCurrentDelay($post->ID);

        echo <<<EOA
<div data-post-id="{$post->ID}">
    <input type="text" class="delivery-delay-input" value="{$delay}" data-post-id="{$post->ID}" />
    <span> (Minuten) </span>
    <button class="save-delay" data-post-id="{$post->ID}">Speichern</button>
</div>
EOA;
    }
}

/**
 * Adjusts the styles for the new delivery_delay column.
 */
function evs_add_delivery_delay_column_styles() {

    $css = '.delivery_delay.column-delivery_delay { width: 20%; }';
    wp_add_inline_style('woocommerce_admin_styles', $css);
}

add_filter('manage_edit-shop_order_columns', 'evs_add_delivery_delay_column_header', 20);
add_action('manage_shop_order_posts_custom_column', 'evs_add_delivery_delay_column_content');
add_action('admin_print_styles', 'evs_add_delivery_delay_column_styles');

?>
