<?php
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    block
 * @subpackage jsinjection
 * @copyright  2019 RCGP
 * @author     Damian Hippisley d.j.hippisley@qmul.ac.uk
 * @date       17/09/18
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @filename   externallib.php.php
 *
 */

//currently not used

//require_once($CFG->libdir . "/externallib.php");

class block_jsinjection_external extends \external_api
{
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_participant_ids_parameters()
    {
        return new external_function_parameters(
            array('participantsmap' => new external_value(PARAM_, 'The welcome message. By default it is "Hello world,"', VALUE_DEFAULT, 'Hello world, '))
        );
    }

    /**
     * Returns welcome message
     * @param string $welcomemessage
     * @return string welcome message
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws restricted_context_exception
     */
    public static function get_participant_ids($welcomemessage = 'Hello world, ')
    {
        global $USER;
        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::hello_world_parameters(),
            array('welcomemessage' => $welcomemessage));
        //Context validation
        //OPTIONAL but in most web service it should present
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);
        //Capability checking
        //OPTIONAL but in most web service it should present
        if (!has_capability('moodle/user:viewdetails', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }
        return \$params['welcomemessage'] . $USER->firstname;;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_participant_ids_returns()
    {
        return new external_value(PARAM_TEXT, 'The welcome message + user first name');
    }
}
