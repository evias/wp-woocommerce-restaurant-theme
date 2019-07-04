
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

var api_save_delay = function() {

    let delay = parseInt(jQuery("#delivery-delay-input").val());

    jQuery.ajax({
        url: ajaxurl, // Since WP 2.8 ajaxurl is always defined and points to admin-ajax.php
        data: {
            "action": "save_delay",
            "delay": delay
        },
        method: "POST",
        error: function(errorThrown){
            console.log("Error: ", errorThrown);
        }
    });
};

var init_delay_buttons = function() {
    jQuery("#save-delay").click(function(e) {

        api_save_delay();

        return false;
    });
};

jQuery(document).ready(function(e) {
 
    // add event listeners
    init_delay_buttons();
});
