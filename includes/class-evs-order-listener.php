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
 * Check for available orders
 *
 * @since 1.0.0
 */
class EVS_Order_Listener {

    /**
     * @brief   Get the orders count
     * @detail  Get the total orders count
     */
    public function getCountOrders() 
    {
        global $wpdb;

        // Read total number of orders
        $results = $wpdb->get_results("
            SELECT count(*) as cnt_orders
            FROM {$wpdb->prefix}posts 
            WHERE 
                post_type = 'shop_order'
        ", OBJECT );

        $count = 0;
        if (!empty($results)) {
            $count   = $results[0]->cnt_orders;
        }

        return EVS_API_Response::sendResponse(['count' => $count]);
    }
    
}
