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
 * Save current delivery delay
 *
 * @since 1.0.0
 */
class EVS_Delay_Writer {

    /**
     * @brief   Save new delivery delay
     * @detail  Save a new delivery delay
     */
    public function saveCurrentDelay() 
    {
        global $wpdb;

        // read delay from request
        $delay = isset($_POST['delay']) ? (int) $_POST['delay'] : 45;

        // save latest delay
        $cnt = $wpdb->update("wp_posts", [
            'post_content' => $delay,
            'post_modified' => current_time('mysql', 1),
            'post_modified_gmt' => current_time('mysql', 1),
        ], [
            'ID' => CURRENT_DELAY_POST_ID,
        ]);

        if ($cnt === false || !$cnt) {
            return EVS_API_Response::sendResponse(['status' => 'error', 'message' => 'Could not update delivery delay.']);
        }

        return EVS_API_Response::sendResponse(['status' => 'OK']);
    }

    /**
     * @brief   Get delivery delay
     * @detail  Read latest delivery delay
     */
    public function getCurrentDelay() 
    {
        global $wpdb;

        // read delay
        $results = $wpdb->get_results("
            SELECT post_content as current_delay
            FROM {$wpdb->prefix}posts 
            WHERE 
                post_type = 'evs_delay'
        ", OBJECT );

        $delay = 45;
        if (!empty($results)) {
            $delay = $results[0]->current_delay;
        }

        return (int) $delay;
    }
}
