<?php

/**
 * @package    block
 * @subpackage jsinjection
 * @copyright  2019 RCGP
 * @author     Damian Hippisley d.j.hippisley@qmul.ac.uk
 * @date       11/09/18
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @filename   renderer.php
 * @filepath   block/jsinjection/classes/
 * Note: alter the namespace if included in a subfolder for autoloading
 *
 */

/**
 * This is the simple pattern for setting up a renderer perhaps to use in the
 * javascript
 *
 */
class block_jsinjection_renderer extends \core_renderer
{

    public function render_settings_display(\block_jsinjection\output\settings_display $widget){
        $export = $widget->export_for_template($this);
        return $this->render_from_template('block_jsinjection/settings_display', $export);
    }

}
