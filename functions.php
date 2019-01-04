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
require_once dirname( __FILE__ ) . '/includes/class-evs-js-injector.php';
require_once dirname( __FILE__ ) . '/includes/class-evs-css-injector.php';

// Inject CSS and JS into Backend Theme
add_action('wp_enqueue_scripts', 'EVS_CSS_Injector::setStylesheet', PHP_INT_MAX);
add_action('in_admin_footer', 'EVS_JS_Injector::getOrderListener');

// Register admin-ajax.php?action=XX calls
add_action('wp_ajax_check_orders', 'EVS_Order_Listener::getCountOrders' );
add_action('wp_ajax_is_delivering', 'EVS_Status_Listener::isDelivering' );

// Register new REST API route for delivery status
add_action('rest_api_init', function () {
    register_rest_route('restaurant/v1', '/delivery', array(
        'methods' => 'GET',
        'callback' => 'is_delivering',
    ));
});
?>
