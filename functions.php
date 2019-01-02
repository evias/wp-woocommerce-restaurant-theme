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

define("SCHEDULER_POST_ID", 735);


// Action: add child theme styles and scripts
function enqueue_child_theme_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

// This would normally be enqueued as a file, but for the sake of ease we will just print to the footer
function add_orders_ajax_call(){ ?>

<script>
// globalize the counter to each page load
var current_count_orders = -1;

var api_check_orders = function() {
   jQuery.ajax({
        url: ajaxurl, // Since WP 2.8 ajaxurl is always defined and points to admin-ajax.php
        data: {
            'action': 'check_orders'
        },
        success:function(data) {
            if (typeof data == 'string') {
                try {
                    data = JSON.parse(data);
                }
                catch(e) { console.log("Error in JSON: ", e); return false; }
            }

            let cnt = parseInt(data.count);

            console.log("Count: ", cnt);

            if (current_count_orders < 0) {
                current_count_orders = cnt;
                return false; // fresh reload
            }

            if (cnt > current_count_orders) {
                console.log("Time to bell!!! RIIINNGG");
                //XXX ring <audio>

                current_count_orders = cnt;
            }
        },  
        error: function(errorThrown){
            console.log("Error: ", errorThrown);
        }
    });
};

jQuery(document).ready(function($) {
 
    setInterval(api_check_orders, 20000);

    // open the dance..
    api_check_orders();
});
</script>
<?php } 

function check_orders() {
 
    global $wpdb;

    // Read total number of orders
    $results = $wpdb->get_results( "SELECT count(*) as cnt_orders FROM {$wpdb->prefix}posts WHERE post_type = 'shop_order'", OBJECT );

    $json = "{}";
    if (!empty($results)) {
        $count   = $results[0]->cnt_orders;
        $json    = json_encode(['count' => $count]); 
    }

    // Return JSON
    header("Content-Type: application/json");
    header("Content-Length: " . strlen($json));
    echo $json; 
    exit;
}

function is_delivering() {
 
    global $wpdb;

    // Read total number of orders
    $results = $wpdb->get_results( "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE post_id = " . SCHEDULER_POST_ID, OBJECT );

    $isOpened = false;
    if (!empty($results)) {
        $result  = $results[0]->meta_value;
        $array   = unserialize($result);

        var_dump($array);die;
    }

    // Return JSON
    header("Content-Type: application/json");
    header("Content-Length: " . strlen($json));
    echo '{ "status": ' . ($isOpened ? "true" : "false") . ' }'; 
    exit;
}

add_action( 'wp_enqueue_scripts', 'enqueue_child_theme_styles', PHP_INT_MAX);
add_action('in_admin_footer', 'add_orders_ajax_call');
add_action( 'wp_ajax_check_orders', 'check_orders' );
add_action( 'wp_ajax_is_delivering', 'is_delivering' );
?>
