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
 * Check for delivery status
 *
 * @since 1.0.0
 */
class EVS_Status_Listener {

    /**
     * @brief   Get status of delivery
     * @detail  Get true or false when delivery is respectively active or inactive
     */
    public function isDelivering($request) 
    {
        global $wpdb;

        // Read scheduler configuration
        $results = $wpdb->get_results( "SELECT meta_key, meta_value FROM {$wpdb->prefix}postmeta WHERE post_id = " . SCHEDULER_POST_ID, OBJECT );
        $status = false;

        $schedule = null;
        for ($i = 0, $m = count($results); $i < $m; $i++) {
            if ($results[$i]->meta_key !== "woc_hours_meta") {
                continue;
            }

            $schedule = unserialize($results[$i]->meta_value);
            break;
        }

        if (empty($schedule)) {
            return EVS_API_Response::sendResponse(['status' => true, "message" => "ok"]);
        }

        // prepare WooCommerce Open/Close Scheduler data
        $message = $schedule["woc_message"];
        $daysId = [
            "Mon" => 10003,
            "Tue" => 10004,
            "Wed" => 10005,
            "Thu" => 10006,
            "Fri" => 10007,
            "Sat" => 10001,
            "Sun" => 10002,
        ];

        // fetch current date
        $currentDate = new \DateTime("now", new \DateTimeZone("CET")); 
        $currentDay  = $currentDate->format("D"); // textual representation of a day, three letters
        $currentHour = $currentDate->format("g"); // 12-hour format without leading 0
        $currentMins = $currentDate->format("i");

        // read today's schedule
        $scheduleToday = isset($schedule[$daysId[$currentDay]]) ? array_shift($schedule[$daysId[$currentDay]]) : false;

        if (false === $scheduleToday) {
            return EVS_API_Response::sendResponse(['status' => false, "message" => $message]);
        }

        list($start,) = explode(" ", $scheduleToday["open"]);
        list($end,)   = explode(" ", $scheduleToday["close"]);
        list($startHour, $startMins)  = explode(":", $start);
        list($endHour, $endMins)      = explode(":", $end);

        $status = false;
        if (
            ($currentHour > $startHour || ($currentHour === $startHour && $currentMins >= $startMins))
         && ($currentHour < $endHour   || ($currentHour === $endHour && $currentMins >= $endMins))
        ) {
            $status = true;
        }

        $message = $status ? "ok" : $message;
        return EVS_API_Response::sendResponse(['status' => $status, "message" => $message]);
    }

}
