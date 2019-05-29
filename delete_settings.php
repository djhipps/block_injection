<?php

/**
 * @package    block
 * @subpackage jsinjection
 * @copyright  2019 RCGP
 * @author     Damian Hippisley d.j.hippisley@qmul.ac.uk
 * @date       13/09/18
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @filename   delete_settings.php
 *
 */


define('AJAX_SCRIPT', true);

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    require_once(__DIR__.'/../../config.php');
    require_once(__DIR__.'/../../lib/formslib.php');

    if(isset($_REQUEST['categoryid']))
    {
        $asf = new \block_jsinjection\advancedsettings_form();
        $error = $asf->delete_setting_block_instance($_REQUEST['categoryid']);
        if(empty($error)){
            echo json_encode('Config and block successfully deleted.');
        }else{
            echo json_encode($error);
        }
    }

}