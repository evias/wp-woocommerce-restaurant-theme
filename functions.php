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
defined("SCHEDULER_POST_ID") || define("SCHEDULER_POST_ID", 735);
defined("HOLIDAY_POST_ID") || define("HOLIDAY_POST_ID", 1754);

// :warning: Define holiday schedule!!
// Yearly summer holiday : 22.07.2019 to 08.08.2019
defined("HOLIDAY_YEAR_START") || define("HOLIDAY_YEAR_START", 2019);
defined("HOLIDAY_YEAR_END") || define("HOLIDAY_YEAR_END", 2019);
defined("HOLIDAY_MONTH_START") || define("HOLIDAY_MONTH_START", 7);
defined("HOLIDAY_MONTH_END") || define("HOLIDAY_MONTH_END", 8);
defined("HOLIDAY_DAY_START") || define("HOLIDAY_DAY_START", 22);
defined("HOLIDAY_DAY_END") || define("HOLIDAY_DAY_END", 8);

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
add_action('in_admin_footer', [new EVS_JS_Injector, "injectDelayWriter"]);

// Custom delivery delay Admin Order Page overwrite
add_filter('manage_edit-shop_order_columns', [new EVS_Delay_Writer, 'addAdminColumnHeader'], 20);
add_action('manage_shop_order_posts_custom_column', [new EVS_Delay_Writer, 'addAdminColumnContent']);
add_action('admin_print_styles', [new EVS_Delay_Writer, 'addAdminColumnStyles']);

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

?>
