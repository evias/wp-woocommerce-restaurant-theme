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
    public function injectOrderListener()
    {
        // <iframe src="/wp-content/uploads/2019/01/new.wav" allow="autoplay" style="display:none" id="order-alarm-iframe"></iframe> 
        echo <<<EOA
<br />
<hr />
<br />
<button id="alarm-activate">Starten</button>
<br />
<button id="alarm-deactivate">Alarm Pause</button>

<audio id="order-alarm" webkit-playsinline="true" playsinline="true" loop>
    <source src="https://beta.da-antonio.be/wp-content/uploads/2019/01/new.wav" type="audio/wav">
    Your browser does not support the audio element.
</audio>
EOA;

        $javascriptSourceCode = file_get_contents(dirname(__FILE__) . "/../assets/evs-order-inspector.js");
        echo <<<EOH
<script>
{$javascriptSourceCode}
</script>
EOH;
    }

}
