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



}
