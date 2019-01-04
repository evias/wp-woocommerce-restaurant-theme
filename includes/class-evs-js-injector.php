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

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Inject custom scripts
 *
 * @since 1.0.0
 */
class EVS_JS_Injector {

    /**
     * @brief   Get the orders listener JS script
     * @detail  Get the HTML for injecting the orders listener JS script
     */
    static public function injectOrderListener()
    {
        echo <<<EOA
<audio id="order-alarm" webkit-playsinline="true" playsinline="true" autoplay="" muted>
    <source src="/wp-content/uploads/2019/01/new.wav" type="audio/wav">
    Your browser does not support the audio element.
</audio>
EOA;

        echo <<<EOH
<script>
// globalize the counter to each page load
var current_count_orders = -1;

var api_check_orders = function() {
   jQuery.ajax({
        url: ajaxurl, // Since WP 2.8 ajaxurl is always defined and points to admin-ajax.php
        data: {
            "action": "check_orders"
        },
        success:function(response) {
            let data = response;
            if (typeof response == "string") {
                try {
                    response = JSON.parse(data);
                    data = response.data;
                }
                catch(e) { console.log("Error in JSON: ", e); return false; }
            } else {
                data = response.data || null;
            }

            if (! data) {
                current_count_orders = 0;
                return false;
            }

            let cnt = parseInt(data.count);

            if (current_count_orders < 0) {
                current_count_orders = cnt;
                return false; // fresh reload
            }

            if (cnt > current_count_orders) {
                console.log("Time to bell!!! RIIINNGG");
                play_sound();
                current_count_orders = cnt;
            }
        },
        error: function(errorThrown){
            console.log("Error: ", errorThrown);
        }
    });
};

var play_sound = function() {
    // fake-interact with website first
//    var btn = document.getElementById('interact-button');
//    btn.click();

    let a = new Audio("/wp-content/uploads/2019/01/new.wav");
    a.play();
/*
    // now play sound
    setTimeout(function() { document.getElementById("order-alarm").play(); }, 500);
*/
};

jQuery(document).ready(function(e) {
 
    setInterval(function() { api_check_orders() }, 20000);

    // open the dance..
    api_check_orders();
});
</script>
EOH;
    }

}
