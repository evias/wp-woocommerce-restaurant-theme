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
class EVS_CSS_Injector {

    /**
     * @brief   Inject the child theme CSS
     * @detail  Inject the child theme CSS
     */
    static public function setStylesheet()
    {
        wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
    }

}
