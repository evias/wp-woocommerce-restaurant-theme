<?php

// Action: add child theme styles and scripts
add_action( 'wp_enqueue_scripts', 'enqueue_child_theme_styles', PHP_INT_MAX);
function enqueue_child_theme_styles() {
  wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

// This would normally be enqueued as a file, but for the sake of ease we will just print to the footer
function add_orders_ajax_call(){ ?>
 
<script>
// globalize the counter to each page load
var current_count_orders = -1;

var api_check_orders = function() {
   $.ajax({
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

add_action('in_admin_footer', 'add_orders_ajax_call');

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
 
// This bit is a special action hook that works with the WordPress AJAX functionality. 
add_action( 'wp_ajax_check_orders', 'check_orders' );
?>
