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

// :warning: 
// /!\ Needs execution of following SQL:
/**
mysql > insert into wp_posts 
    (post_author,
    post_date,
    post_date_gmt,
    post_content,
    post_title,
    post_excerpt,
    post_status,
    comment_status,
    ping_status,
    post_password,
    post_name,
    to_ping,
    pinged,
    post_modified,
    post_modified_gmt,
    post_content_filtered,
    post_parent,
    guid,
    menu_order,
    post_type,
    post_mime_type,
    comment_count) 
values 
    (1, now(), now(), '', 
    'Wartezeiten', '', 'publish', 'closed',
    'open', '', 'Wartezeiten', '',
    '', now(), now(), '',
    0, 'https://beta.da-antonio.be/?post_type=woc_hour&#038;p=???', 0, 'evs_delay',
    '', 0);

mysql > select ID from wp_posts where post_title = 'Wartezeiten' limit 1;
mysql > update wp_posts set guid = 'https://beta.da-antonio.be/?post_type=woc_hour&#038;p=1649' where ID = 1649;
 **/
define("CURRENT_DELAY_POST_ID", 1649);

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

// BACKEND API for orders (wp_ajax.php?action=check_orders)
add_action('wp_ajax_check_orders', [new EVS_Order_Listener, "getCountOrders"]);
add_action('wp_ajax_save_delay', [new EVS_Delay_Writer, "saveCurrentDelay"]);
//XXX add_action('wp_ajax_fetch_orders', [new EVS_Order_Listener, "getProcessingOrders"]);

// Register new REST API route for delivery status
add_action('rest_api_init', function () {
    register_rest_route('restaurant/v1', '/delivery', array(
        'methods' => 'GET',
        'callback' => [new EVS_Status_Listener, "isDelivering"],
    ));
});
?>
