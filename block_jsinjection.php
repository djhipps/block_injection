<?php
/**
 * Created by PhpStorm.
 * User: damian
 * Date: 03/09/18
 * Time: 11:36
 */


/**
 * Class block_jsinjection
 */
class block_jsinjection extends block_base
{

    /**
     * This prevents it getting added to the normal Add a Block UI
     * selector and forces the user to set the block using the settings
     * on the category level
     * @return array
     */
    public function applicable_formats()
    {
        // for example
        return array(
            'course-view' => true
        );
    }

    function instance_allow_multiple()
    {
        return false;
    }

    /**
     * Config and settings are handled in the plugin
     * @return bool
     */
    function has_config()
    {
        return true;
    }


    /**
     * @throws coding_exception
     */
    public function init()
    {
        $this->title = get_string('jsinjection_title', 'block_jsinjection');
    }


    /**
     * Handles additional display coditions and bootstraps javascript
     *
     * @return null|stdClass|stdObject
     */
    public function get_content()
    {
        global $PAGE;

        $context = $PAGE->context;

        if(empty($this->instance->id)){
            return $this->content;
        }

//        if($this->_hide_this_block_on_multiple_instances($this->instance->id, $context)){
//            return $this->content;
//        }

        if ($this->content !== null) {
            return $this->content;
        }

        try {
            // (optional) create additional conditions to test if block is required
            $conditions = true;

            if ($conditions) {
                // (optoinal) call some classes (sample classes) to get data model
                // $datamodel = new \block_jsinjection\datamodelclass($conditions);
                // $params = $datamodel->some_function();
                $param1 = "Injected parameter";

                //set up some params to pass to js
                $params = array('param1' => $param1);
                //call javascript injection here
                //!note that the injections does not work if the content is returned null before!
                $PAGE->requires->js_call_amd('block_jsinjection/injection', 'init', $params);

                // to hide block automatically, ensure both props below are empty
                $this->content = new stdClass;
                $this->content->text = ""; // probably won't need any text - may for editing only / debugging
                $this->content->footer = '';

            }
        } catch (coding_exception $e) {
            error_log($e->getMessage());
            $this->content = null;
        }


        return $this->content;
    }

    /**
     * @param $blockinstance
     * @return array|bool
     *
     */
    public function get_datatype_setting_for_block($blockinstance){
            global $DB;

            try {
                $sql = "SELECT bqbc.datatype FROM {block_instances} bi
                        JOIN {context} ctx ON bi.parentcontextid = ctx.id
                        JOIN {block_jsinjection_conf} bqbc ON ctx.instanceid = bqbc.categoryid
                        WHERE bi.id = ? AND ctx.contextlevel = 40";
                $params = array($blockinstance);
                return $DB->get_records_sql($sql, $params);
            } catch (\dml_exception $dml_exception){
            }
            return false;

    }


    /**
     * This function determines if there are multiple blocks in the same context
     * and decides which to display based on precedence rules
     * 1. if added to course explicity
     * 2. if deeper in category path than all others
     * 3. lowest depth in cat path
     * @param $blockinstance
     * @param $context
     * @return bool
     */
    private function _hide_this_block_on_multiple_instances($blockinstance, $context){
           if($results = $this->_get_all_jsinjection_blocks()){
               $instances = [];
               foreach ($results as $result) {
                   //this should filter out instances in the cm context only
                   if(substr($context->path, 0, strlen($result->path) +1) === $result->path . '/'){
                       $instances[$result->blockinstanceid] = $result;
                   }
               }
               if(!empty($instances)){
                   //get deepest
                   uasort($instances, function($a, $b) {
                       return $b->depth <=> $a->depth;
                   });
                   $deepest = array_shift($instances);
                   if($deepest->blockinstanceid === $blockinstance) {
                       return false;
                   }
               }
           }
           return true;
    }

    /**
     * @return array|bool
     */
    private function _get_all_jsinjection_blocks(){
        global $DB;

        try {
            $sql = "SELECT bi.id AS blockinstanceid, ctx.path, ctx.depth, if(bqbc.id IS NULL,0,1) AS inconf
                    FROM mdl_block_instances bi
                    JOIN mdl_context ctx ON bi.parentcontextid = ctx.id
                    LEFT JOIN mdl_block_jsinjection_conf bqbc ON ctx.instanceid = bqbc.categoryid
                    WHERE  blockname = 'jsinjection'";
            return $DB->get_records_sql($sql);

        } catch (\dml_exception $dml_exception){
        }
        return false;
    }


}
