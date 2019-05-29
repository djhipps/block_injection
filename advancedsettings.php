<?php
/**
 * @project    qmplus34
 * @package    blocks
 * @subpackage jsinjection
 * @copyright  2019 RCGP
 * @author     Damian HIppisley d.j.hippisley@qmul.ac.uk
 * @date       07/09/18
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @filename   advancedsettings.php
 *
 */

require(__DIR__.'/../../config.php');

//require_once(__DIR__.'/lib.php');
//require(__DIR__.'/edit_form.php');

$context = \context_system::instance();
$PAGE->set_context($context);

require_login();

if (!isloggedin()) {
    //redirect to moodle login page
    echo 'Not Logged In';
    redirect(new moodle_url('/login/index.php'));
}

$PAGE->set_pagelayout('admin');
$PAGE->set_url('/blocks/jsinjection/advancedsettings.php');
$PAGE->set_title(get_string('settingstitle', 'block_jsinjection'));
$PAGE->set_heading(get_string('settingstitle', 'block_jsinjection'));

$PAGE->requires->js_call_amd('block_jsinjection/settings', 'init');


echo $OUTPUT->header();

// check who can access this page
$capability = 'block/jsinjection:editsettings';
$access = new \block_jsinjection\access($USER->id, $capability);
$usercatcheck = $access->user_has_capability_in_any_category_context();

if(($usercatcheck === false) &&
    !has_capability('block/jsinjection:editsettings', $context)){
    throw new required_capability_exception($context, $capability, 'nopermissions', '');
}

$asform = new \block_jsinjection\advancedsettings_form();

if($asform->is_cancelled())
{
    //TODO: return to
} elseif ($asformdata = $asform->get_data())
{

    $messagename = get_string('activitysummarymessage', 'block_jsinjection');
    $results = $asform->insertBlockVisibiliySettings($asformdata, $messagename);

    echo html_writer::div($results['message'], 'alert alert-'.$results['type']);

    $asform->display();


} else
{
    $asform->display();

}

echo $OUTPUT->footer();

//TODO: display current settings







