<?php
/**
 * Created by PhpStorm.
 * User: damian
 * Date: 04/09/18
 * Time: 14:29
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $settings = new admin_externalpage('block_jsinjection', get_string('pluginname', 'block_jsinjection'), $CFG->wwwroot . '/blocks/jsinjection/advancedsettings.php');

}
