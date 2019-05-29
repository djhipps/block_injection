<?php

/**
 * @package    block
 * @subpackage jsinjection
 * @copyright  2019 RCGP
 * @author     Damian Hippisley d.j.hippisley@qmul.ac.uk
 * @date       11/09/18
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @filename   settings_display.php
 * @filepath   block/jsinjection/classes/
 * Note: alter the namespace if included in a subfolder for autoloading
 *
 */

namespace block_jsinjection\output;

defined('MOODLE_INTERNAL') || die();

use renderer_base;
use renderable;
use templatable;
use stdClass;

class settings_display implements templatable, renderable
{

    public $settings;
    public $datatypedisplay = ["username" => "Username",
                               "idnumber" => "Idnumber",
                               "usernameandidnumber" => "Both username and idnumber",
    ];

    public function __construct($settings)
    {

        $this->settings = $settings;
    }


    /**
     * Function to export the renderer data in a format that is suitable for a
     * mustache template. This means:
     * 1. No complex types - only stdClass, array, int, string, float, bool
     * 2. Any additional info that is required for the template is pre-calculated (e.g. capability checks).
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output)
    {
        $export = new stdClass();

        $export->settings = [];
        foreach($this->settings as $setting){
            $set = new stdClass();
            $set->category = $setting->category;
            $set->datatype = $this->datatypedisplay[$setting->datatype];
            $set->user = $setting->user;
            $set->categoryid = $setting->categoryid;
            array_push($export->settings, $set);
        }

        return $export;
    }
}