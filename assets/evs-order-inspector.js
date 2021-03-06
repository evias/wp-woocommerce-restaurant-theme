
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

// globalize the counter to each page load
var current_count_orders = -1;

var get_response_data = function(response) {
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

    return data;
};

var api_check_orders = function() {
   jQuery.ajax({
        url: ajaxurl, // Since WP 2.8 ajaxurl is always defined and points to admin-ajax.php
        data: {
            "action": "check_orders"
        },
        success:function(response) {
            let data = get_response_data(response);

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

var init_buttons = function() {
    jQuery("#alarm-activate").click(function(e) {
        // This button is to fake interaction!!
        //
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        // DO NOT REMOVE THIS EVENT HANDLER.
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        //
        return false;
    });

    jQuery("#alarm-deactivate").click(function(e) {
        e.preventDefault();
        stop_sound();
        return false;
    });

    jQuery(".order-preview").click(function(e) {
        stop_sound();
        return true;
    });
};

var stop_sound = function() {
    var aud = document.getElementById("order-alarm");
    aud.pause();
};

var reload_page = function() {
    window.location.reload();
    return false;
};

var play_sound = function() {
    var aud = document.getElementById("order-alarm");
    aud.play();

    setTimeout(function() {
        stop_sound();
        reload_page();
    }, 90000);
};

jQuery(document).ready(function(e) {
 
    setInterval(function() { api_check_orders() }, 20000);

    // add event listeners
    init_buttons();

    // open the dance..
    api_check_orders();

    setTimeout(function() {
        jQuery("#alarm-activate").click();
    }, 2000);
});
