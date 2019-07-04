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
     * @brief   Save new delivery global delay
     * @detail  Save a new delivery global delay
     */
    public function saveGlobalDelay() 
    {
        global $wpdb;

        // read delay from request
        $delay = isset($_POST['delay']) ? (int) $_POST['delay'] : 45;

        // save latest delay
        $cnt = $wpdb->insert("wp_posts", [
            'post_content' => $delay,
            'post_status' => 'publish',
            'comment_status' => 'closed',
            'ping_status' => 'open',
            'post_date' => current_time('mysql', 1),
            'post_date_gmt' => current_time('mysql', 1),
            'post_modified' => current_time('mysql', 1),
            'post_modified_gmt' => current_time('mysql', 1),
            'post_type' => 'evs_delay'
            // no-parent-id to set post_parent = 0
        ]);

        if ($cnt === false || !$cnt) {
            return EVS_API_Response::sendResponse(['status' => 'error', 'message' => 'Could not update delivery delay.']);
        }

        return EVS_API_Response::sendResponse(['status' => 'OK']);
    }

    /**
     * @brief   Save new delivery delay for a Post ID
     * @detail  Save a new delivery delay for a Post ID
     */
    public function saveDelayForPost() 
    {
        global $wpdb;

        // read delay from request
        $delay = isset($_POST['delay']) ? (int) $_POST['delay'] : 45;
        $postId = isset($_POST['pid']) ? (int) $_POST['pid'] : 0;

        // save latest delay
        $cnt = $wpdb->insert("wp_posts", [
            'post_content' => $delay,
            'post_status' => 'publish',
            'comment_status' => 'closed',
            'ping_status' => 'open',
            'post_date' => current_time('mysql', 1),
            'post_date_gmt' => current_time('mysql', 1),
            'post_modified' => current_time('mysql', 1),
            'post_modified_gmt' => current_time('mysql', 1),
            'post_parent' => $postId,
            'post_type' => 'evs_delay'
        ]);

        if ($cnt === false || !$cnt) {
            return EVS_API_Response::sendResponse(['status' => 'error', 'message' => 'Could not update delivery delay.']);
        }

        return EVS_API_Response::sendResponse(['status' => 'OK']);
    }

    /**
     * @brief   Get delivery delay for said Post
     * @detail  Read latest delivery delay for said Post (or global)
     */
    public function getCurrentDelay($postId) 
    {
        global $wpdb;

        // read delay
        $results = $wpdb->get_results("
            SELECT post_content as current_delay
            FROM {$wpdb->prefix}posts 
            WHERE 
                post_type = 'evs_delay'
                AND post_parent = " . $postId . "
            ORDER BY post_date DESC
            LIMIT 1
        ", OBJECT );

        if (empty($results)) {
            // retry for global delay
            $results = $wpdb->get_results("
                SELECT post_content as current_delay
                FROM {$wpdb->prefix}posts 
                WHERE 
                    post_type = 'evs_delay'
                    AND post_parent = 0
                ORDER BY post_date DESC
                LIMIT 1
            ", OBJECT );
        }

        $delay = 45;
        if (!empty($results)) {
            $delay = $results[0]->current_delay;
        }

        return (int) $delay;
    }

    /**
     * Adds 'Delivery Delay' column header to 'Orders' page immediately after 'Total' column.
     *
     * @note Wordpress Filter
     * @param string[] $columns
     * @return string[] $new_columns
     */
    public function addAdminColumnHeader($columns) {

        $new_columns = array();

        foreach ( $columns as $column_name => $column_info ) {

            $new_columns[ $column_name ] = $column_info;

            if ( 'order_total' === $column_name ) {
                $new_columns['delivery_delay'] = 'Wartezeit';
            }
        }

        return $new_columns;
    }

    /**
     * Adds 'Delivery Delay' column *content* to 'Orders' page immediately after 'Total' column.
     *
     * @note Wordpress Action
     * @param string[] $columns
     * @return string[] $new_columns
     */
    public function addAdminColumnContent($column) {
        global $post;
        global $wpdb;

        if ( 'delivery_delay' === $column ) {

            $delay = (new EVS_Delay_Writer)->getCurrentDelay($post->ID);

            echo <<<EOA
<div data-post-id="{$post->ID}">
    <input type="text" class="delivery-delay-input" value="{$delay}" data-post-id="{$post->ID}" />
    <span> (Minuten) </span>
    <button class="save-delay" data-post-id="{$post->ID}">Speichern</button>
</div>
EOA;
        }
    }

    /**
     * Adjusts the styles for the new delivery_delay column.
     */
    function addAdminColumnStyles() {

        $css = '.column-delivery_delay { width: 20%; }';
        wp_add_inline_style('woocommerce_admin_styles', $css);
    }
}
