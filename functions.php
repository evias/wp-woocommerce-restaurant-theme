<?php

// Action: add child theme styles and scripts
add_action( 'wp_enqueue_scripts', 'enqueue_child_theme_styles', PHP_INT_MAX);
function enqueue_child_theme_styles() {
  wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

// This would normally be enqueued as a file, but for the sake of ease we will just print to the footer
function add_orders_ajax_call(){ ?>
 
<script>
jQuery(document).ready(function($) {
 
    $.ajax({
        url: ajaxurl, // Since WP 2.8 ajaxurl is always defined and points to admin-ajax.php
        data: {
            'action': 'check_orders', // This is our PHP function below
        },
        success:function(data) {
			// This outputs the result of the ajax request (The Callback)
            window.alert(data);
        },  
        error: function(errorThrown){
            window.alert(errorThrown);
        }
    }); 
});
</script>
<?php } 

add_action('in_admin_footer', 'add_orders_ajax_call');

function check_orders() {
 
	global $wpdb;

    $results = $wpdb->get_results( "SELECT count(*) FROM {$wpdb->prefix}posts WHERE post_type = 'shop_order'", OBJECT );
    print_r($results); 
	exit;
}
 
// This bit is a special action hook that works with the WordPress AJAX functionality. 
add_action( 'wp_ajax_check_orders', 'check_orders' );
?>
