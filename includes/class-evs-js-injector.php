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
<style type="text/css">
#alarm-wrapper {
    position: absolute;
    bottom: 30px;
}
</style>
<div id="alarm-wrapper">
    <button id="alarm-activate">Starten</button>
    <span> - </span>
    <button id="alarm-deactivate">Alarm Pause</button>
</div>
<audio id="order-alarm" webkit-playsinline="true" playsinline="true" loop>
    <source src="https://beta.da-antonio.be/wp-content/uploads/2019/01/new.wav" type="audio/wav">
    Your browser does not support the audio element.
</audio>
EOA;

        $javascriptSourceCodeOrder = file_get_contents(dirname(__FILE__) . "/../assets/evs-order-inspector.js");
        echo <<<EOH
<script>
{$javascriptSourceCodeOrder}
</script>
EOH;
    }

    /**
     * @brief   Get the delay writer JS script
     * @detail  Get the HTML for injecting the delay writer JS script
     */
    public function injectDelayWriter()
    {
        $delay = (new EVS_Delay_Writer)->getCurrentDelay();

        echo <<<EOA
<style type="text/css">
#delay-wrapper {
    position: absolute;
    bottom: 30px;
    left: 280px;
}
</style>
<div id="delay-wrapper">
    <input type="text" id="delivery-delay-input" value="{$delay}" />
    <span> (Minuten) </span>
    <button id="save-delay">Speichern</button>
</div>
EOA;

        $javascriptSourceCodeDelay = file_get_contents(dirname(__FILE__) . "/../assets/evs-delay-injector.js");
        echo <<<EOH
<script>
{$javascriptSourceCodeDelay}
</script>
EOH;
    }

}
