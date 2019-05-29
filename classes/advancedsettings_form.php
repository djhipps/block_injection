<?php

/**
 * @package    block
 * @subpackage jsinjection
 * @copyright  2019 RCGP
 * @author     Damian Hippisley d.j.hippisley@qmul.ac.uk
 * @date       07/09/18
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @filename   advancedsettings_form.php.php
 * @filepath   block/jsinjection/classes/
 * Note: alter the namespace if included in a subfolder for autoloading
 *
 */

namespace block_jsinjection;

class advancedsettings_form  extends \moodleform
{

    private static $_blockname = 'jsinjection';
    private static $_pagepatterntype = 'mod-assign-grading';
    private static $_defaultregion = 'side-post';

    /**
     * @throws \coding_exception
     */
    public function definition()
    {
        $mform =& $this->_form;

        $mform->addElement('html', '<h2>'.get_string('settingstitle', 'block_jsinjection').'</h2>');
        //TODO: see the bootstrap components
        $mform->addElement('html', '<p>'.get_string('settingsdesc', 'block_jsinjection').'</p><br>');

        // Get Users Schools
        $mform->addElement('select', 'category', get_string('school', 'block_jsinjection'),
            $this->_getAllSchooCategories(), array('id'=>'course')
        );
        $mform->addRule('category', get_string('categroyrequired', 'block_jsinjection') , 'required');


        $radioarray=array();
        $radioarray[] = $mform->createElement('radio', 'datatype', '', get_string('username', 'block_jsinjection'), 'username', array('checked' => 'checked'));
        $radioarray[] = $mform->createElement('radio', 'datatype', '', get_string('idnumber', 'block_jsinjection'), 'idnumber');
        $radioarray[] = $mform->createElement('radio', 'datatype', '', get_string('usernameandidnumber', 'block_jsinjection'), 'usernameandidnumber');
        $mform->addGroup($radioarray, 'radiodatatype', get_string('displayradiogroup', 'block_jsinjection'), array(' '), false);
        $mform->addRule('radiodatatype', get_string('radiorequired', 'block_jsinjection') , 'required');

        $mform->addElement('submit', 'submit', get_string('editbutton', 'block_jsinjection'), array('id'=>'submit'));

    }

    public function display(){
        global $PAGE;
        parent::display();

        $settings = $this->get_view_settings();
        if(!empty($settings)){
            $renderer = $PAGE->get_renderer('block_jsinjection');
            $widget = new \block_jsinjection\output\settings_display($settings);
            echo $renderer->render_settings_display($widget);
        }

    }



    /**
     * @return array|bool
     * @throws \coding_exception
     */
    private function _getAllSchooCategories(){
        global $CFG;

        if (is_siteadmin()) {
            require_once($CFG->libdir.'/coursecatlib.php');
            $categories = \coursecat::make_categories_list('moodle/category:manage');
        }
        else
        {
            $courses = enrol_get_my_courses();
            $categories = array();
            foreach ($courses as $course) {
                $categories[] = $course->category;
            }

            $categories = array_unique($categories);
            $catIds = '';

            foreach ($categories as $id) {
                if($catIds!==''){
                    $catIds .= ',';
                }
                $catIds .= $id;
            }

      //      TODO: test this.
            $cats = $this->_get_coursecategories_by_user_enrollments($catIds);

            if (empty($cats)) {
                return false;
            }

            $categories = array();
            foreach ($cats as $category) {
                $category->name = $this->_getCategoryName($category->path);
                $categories[$category->id]= $category->name;
            }
        }
        return $categories;
    }

    /**
     * @param $categoryPath
     * @return string
     */
    private function _getCategoryName($categoryPath){
        global $DB;

        $path = explode('/',$categoryPath);
        unset($path[0]);
        $namechunks = array();

        $parent = false;
        //TODO : refactor this!
        foreach ($path as $parentid) {

            $parent = $this->_getCourseCategoriesParentPathnames($parentid);

            $namechunks[] = $parent->name;
        }
        $name = join(' / ', $namechunks);
        return $name;
    }

    /**
     * @param $data
     * @param $messagename
     * @return array
     * @throws \coding_exception
     */
    public function insertBlockVisibiliySettings($data, $messagename){
        global $DB, $USER;


        $categoryName = get_string('unspecifiedcategory', 'block_jsinjection');;
        $category = $this->_getBlockSettings(array('categoryid'=> $data->category));
        if(count($category) === 1){
            $category = current($category);
            $categoryName = $this->_getCategoryName($category->path);
        }

        //try to insert block category or no, there is a check if it already exists
        $this->_insert_block($data->category);

        //check if doesn't already exist, insert
        if(empty($category)){
            $categorypath = $this->_getCourseCategoryPath($data->category);
            $categoryName = $this->_getCategoryName($categorypath->path);

            //TODO: insert block
            $this->_insert_block($data->category);


            $record = new \stdClass();
            $record->categoryid = $data->category;
            $record->datatype = $data->datatype;
            $record->userid = $USER->id;

            try {
                $DB->insert_record('block_jsinjection_conf', $record);
            } catch (\Throwable $e) {
                return array('status'=>false,'message'=>'Caught exception: '. $e->getMessage(). "\n",'type'=>'danger');
            }

            return array(
                'status'=>true,
                'message'=>'"'.$categoryName.' '.$messagename.'" '.get_string('addmessage','block_jsinjection'),
                'type'=>'success');

        } else{
            return array(
                'status'=>false,
                'message'=>'"'.$categoryName.' '.$messagename.'" '.get_string('existmessage','block_jsinjection'),
                'type'=>'warning');
        }


    }


    private function _insert_block($categoryid){
        global $DB, $CFG;
        require_once($CFG->libdir.'/blocklib.php');

        $context = \context_coursecat::instance($categoryid);
        $blockname = 'jsinjection';

        $blockexists = $this->_block_instance_exists($context->id);
        if($blockexists){
            //block already exists for that category context so don't create it
            return false;
        }

        try {
            $blockinstance = new \stdClass;
            $blockinstance->blockname = self::$_blockname;
            $blockinstance->parentcontextid = $context->id;
            $blockinstance->showinsubcontexts = 1;
            $blockinstance->pagetypepattern = self::$_pagepatterntype;
            $blockinstance->subpagepattern = NULL;
            $blockinstance->defaultregion = self::$_defaultregion;
            $blockinstance->defaultweight = 7;
            $blockinstance->configdata = '';
            $blockinstance->timecreated = time();
            $blockinstance->timemodified = $blockinstance->timecreated;
            $blockinstance->id = $DB->insert_record('block_instances', $blockinstance);

            // Ensure the block context is created.
            \context_block::instance($blockinstance->id);

            // If the new instance was created, allow it to do additional setup
            if ($block = block_instance($blockname, $blockinstance)) {
                $block->instance_create();
            }

            return $blockinstance->id;

        }catch (\dml_exception $dml_exception){
            error_log($dml_exception->getMessage());
            return false;
        }
    }

    private function _block_instance_exists($contextid){
        global $DB;

        try {
            $params = array('parentcontextid' => $contextid, 'blockname' => self::$_blockname);
            if($DB->count_records('block_instances', $params)  === 1){
                 return true;
            }
        } catch (\dml_exception $dml_exception){
        }
        return false;

    }


    public function get_view_settings(){
        global $DB;

        $settings = $this->_getBlockSettings();

        if(!empty($settings)){

            foreach ($settings as $setting) {

                //get full path name
                $setting->category = $this->_getCategoryName($setting->path);

                // Get user
                $user = $this->_getUser($setting->userid);
                if($user){
                    $setting->user = $user->firstname.' '.$user->lastname;
                }else{
                    $setting->user = get_string('userunspecified', 'block_jsinjection');;
                }
            }

            return $settings;
        }
        return false;
    }


    public static function delete_setting_block_instance($categoryid){
        global $DB;
        $context = \context_coursecat::instance($categoryid);

        $errors = [];
        try {
            //delete block instance
            $params = array('parentcontextid' => $context->id, 'blockname' => self::$_blockname);
            if(!$DB->delete_records('block_instances', $params)){
                $errors['block_instances_delete_error'] = $context->id;
            }

            if(!$DB->delete_records('block_jsinjection_conf', array('categoryid' => $categoryid))){
                $errors['blindmarking_conf_delete_error'] = $categoryid;
            }

         } catch (\dml_exception $dml_exception){
         }

         return $errors;
    }



    private function _get_coursecategories_by_user_enrollments($catIds){
        global $DB;
        $results = false;
        try {
            $results = $DB->get_records_sql("SELECT * FROM {course_categories}
                WHERE id IN ($catIds) ORDER BY sortorder ASC"
            );
        } catch (\Throwable $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        return $results;
    }

    private function _getCourseCategoriesParentPathnames($parentid){
        global $DB;
        $results = false;
        try {
            $results = $DB->get_record_sql('SELECT name FROM {course_categories}
                        WHERE id = ? ORDER BY sortorder ASC',
                array($parentid)
            );
        } catch (\Throwable $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        return $results;
    }

    private function _getCourseCategoryPath($categoryid){
        global $DB;
        $results = false;
        try {
            $results = $DB->get_record_sql('SELECT path FROM {course_categories}
                        WHERE id = ? ORDER BY sortorder ASC',
                array($categoryid)
            );
        } catch (\Throwable $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        return $results;
    }



    /**
     * @param null $params
     * @return array|bool
     */
    private function _getBlockSettings($params = NULL){
        global $DB;

        try {
            $sql = "SELECT qbc.*, cc.path 
                    FROM {course_categories} cc
                    RIGHT JOIN {block_jsinjection_conf} qbc 
                    ON cc.id = qbc.categoryid";
            if(!empty($params) &&
                (count($params) === 1) &&
                array_key_exists('categoryid', $params)
            ){
                $sql .= " WHERE qbc.categoryid = ?";
            }
            $sql .= " ORDER BY sortorder ASC";


            return $DB->get_records_sql($sql, $params);
        } catch (\dml_exception $dml_exception){

        }
        return false;
    }



    /**
     * @param $userid
     * @return bool|mixed
     */
    private function _getUser($userid){
        global $DB;

        try {
            return $DB->get_record('user', array('id' => $userid));
        }catch (\dml_exception $dml_exception){

        }
        return false;
    }


}