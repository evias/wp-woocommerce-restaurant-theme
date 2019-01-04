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
 * Normalize API responses
 *
 * @since 1.0.0
 */
class EVS_API_Response {

    /**
     * @brief   Respond to request with JSON for single item
     * @detail  Respond to an API request with JSON for a single object response
     */
    static public function sendResponse($data) 
    {
        // Return JSON
        $json = json_encode(['data' => $data]);
        header("Content-Type: application/json");
        header("Content-Length: " . strlen($json));
        echo $json; 
        exit;
    }

}

