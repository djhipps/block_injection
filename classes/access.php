<?php

/**
 * @package    block
 * @subpackage jsinjection
 * @copyright  2019 RCGP
 * @author     Damian Hippisley d.j.hippisley@qmul.ac.uk
 * @date       10/09/18
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @filename   access.php.php
 * @filepath   block/jsinjection/classes/
 * Note: alter the namespace if included in a subfolder for autoloading
 *
 */

namespace block_jsinjection;

/**
 * Class access
 * @package block_jsinjection
 */
class access
{
    public $acceptedroleids = [];
    public $userid = 0;
    public $capability = '';

    /**
     * access constructor.
     * @param $userid
     * @param $capability
     * @param array $acceptedroleids
     *
     */
    public function __construct($userid, $capability, $acceptedroleids = [36,37])
    {
        $this->userid = $userid;
        $this->capability = $capability;
        if($acceptedroleids){
            $this->acceptedroleids = $acceptedroleids;
        }
    }

    /**
     * Accepted roles that this class wishes to check
     *
     * @param $acceptroleids
     *
     */
    public function set_acceptedroleids($acceptroleids){
        $this->acceptedroleids = $acceptroleids;
    }

    /**
     * This function checks the user has a role in
     * least one categroy. If then checks the role has
     * that capability.
     *
     * @return bool
     * @throws \coding_exception
     */
    public function user_has_capability_in_any_category_context(){
        global $CFG, $USER;

        if($this->userid === $USER->id){
            $user = $USER;
        }else{
            $user = $this->_get_user_by_id($this->userid);
        }

        require_once($CFG->libdir .'/accesslib.php');

        $accessdata = $this->_get_user_roles_sitewide_accessdata($this->userid);

        //just one postive, give access, e.g. admin in category role
        foreach($accessdata as $accessdatum){
            if(($accessdatum['contextlevel'] === CONTEXT_COURSECAT) &&
               in_array($accessdatum['roleid'], $this->acceptedroleids)){
                   //check that this context has cap
                   $context = \context::instance_by_id($accessdatum['contextid']);
                   if(has_capability($this->capability, $context, $user)){
                       return true;
                   }
            }
        }

        return false;
    }

    /**
     * @param $userid
     * @return mixed
     */
    private function _get_user_roles_sitewide_accessdata($userid) {
        global $CFG, $DB;

        require_once($CFG->libdir .'/accesslib.php');

        $accessdata = get_empty_accessdata();

        // Preload every assigned role.
        $sql = "SELECT ctx.path, ctx.contextlevel, ra.roleid, ra.contextid
              FROM {role_assignments} ra
              JOIN {context} ctx ON ctx.id = ra.contextid
             WHERE ra.userid = :userid";

        $rs = false;
        try{
            $rs = $DB->get_recordset_sql($sql, array('userid' => $userid));
        }catch (\dml_exception $dml_exception){

        }

        foreach ($rs as $ra) {
            // RAs leafs are arrays to support multi-role assignments...
            $accessdata['ra'][$ra->path] = array('roleid' => (int)$ra->roleid,
                                                 'contextlevel' => (int)$ra->contextlevel,
                                                 'contextid' => (int)$ra->contextid);
        }

        $rs->close();

        return $accessdata['ra'];
    }

    /**
     * Error -  require the attention of a programmer.
     * Error objects thrown from the PHP engine fall into this category, as they generally result from coding errors such as providing a parameter of the wrong type to a function or a parse error in a file.
     * Exception should be used for conditions that can be safely handled at runtime where another action can be taken and execution can continue.
     * @param $userid
     * @return bool|mixed
     */
    private function _get_user_by_id($userid){
        global $DB;

        try {
            return $DB->get_record('user', array('id' => $userid));
        } catch (\dml_exception $dml_exception){

        }
        return false;
    }

}