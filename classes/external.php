<?php
// This file is part of Moodle - http://moodle.org/
//
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
 * This is the external API for the supporter plugin.
 *
 * @package    tool_supporter
 * @copyright  2017 Benedikt Schneider, Klara Saary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_supporter;

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->dirroot/webservice/externallib.php");
require_once("$CFG->dirroot/course/lib.php");
require_once("$CFG->dirroot/user/lib.php");
require_once("$CFG->libdir/adminlib.php");
require_once("$CFG->libdir/coursecatlib.php");

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;
use invalid_parameter_exception;

/**
 * Class external defines several functions to prepare data for further use
 * @package tool_supporter
 * @copyright  2017 Benedikt Schneider, Klara Saary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {

    /**
     * Returns description of input parameters
     * @return external_function_parameters
     */
    public static function create_new_course_parameters() {
        return new external_function_parameters (
        array (
            'shortname' => new external_value ( PARAM_TEXT, 'The short name of the course to be created' ),
            'fullname' => new external_value ( PARAM_TEXT, 'The full name of the course to be created' ),
            'visible' => new external_value ( PARAM_BOOL, 'Toggles visibility of course' ),
            'categoryid' => new external_value ( PARAM_INT, 'ID of category the course should be created in' ),
            'activate_self_enrol' => new external_value ( PARAM_BOOL, 'Toggles if self_enrolment should be activated' ),
            'self_enrol_password' => new external_value ( PARAM_TEXT, 'Passowrd of self enrolment' ),

        ));
    }

    /**
     * Wrap the core function create_new_course.
     * @param string $shortname Desired shortname. Has to be unique or error is returned
     * @param string $fullname Desired fullname
     * @param int $visible Visibility
     * @param int $categoryid Id of the category
     * @return array Course characteristics
     */
    public static function create_new_course($shortname, $fullname, $visible, $categoryid, $activate_self_enrol, $self_enrol_password) {

        global $DB, $CFG;

        $catcontext = \context_coursecat::instance($categoryid);
        self::validate_context($catcontext);
        \require_capability('moodle/course:create', $catcontext);

        $array = array (
            'shortname' => $shortname,
            'fullname' => $fullname,
            'visible' => $visible,
            'categoryid' => $categoryid,
            'activate_self_enrol' => $activate_self_enrol,
            'self_enrol_password' => $self_enrol_password
        );

        // Parameters validation.
        $params = self::validate_parameters(self::create_new_course_parameters (), $array );

        $data = new \stdClass();
        $data->shortname = $params ['shortname'];
        $data->fullname = $params ['fullname'];
        $data->category = $params ['categoryid'];
        $data->visible = $params ['visible'];

        if (trim($params['shortname']) == '') {
            throw new invalid_parameter_exception('Invalid short name');
        }
        if (trim($params['fullname']) == '') {
            throw new invalid_parameter_exception('Invalid full name');
        }
        if ($DB->record_exists('course', array('shortname' => $data->shortname))) {
            throw new invalid_parameter_exception('shortnametaken already taken');
        }

        // Set Start date to 1.4. or 1.10.
        if (strpos($params['shortname'], 'WiSe') !== false) {
            $arrayaftersemester = explode('WiSe', shortname);
            $year = substr($arrayaftersemester[1], 1, 4);
            $data->startdate = mktime(24, 0, 0, 10, 1, $year); // Syntax: hour, minute, second, month, day, year.
        } else if (strpos($shortname, 'SoSe') !== false) {
            $arrayaftersemester = explode('SoSe', $shortname);
            $year = substr($arrayaftersemester[1], 1, 4);
            $data->startdate = mktime(24, 0, 0, 4, 1, $year);
        } else {
            $data->startdate = time();
        }

        $data->enddate = strtotime("+6 month", $data->startdate);

        $createdcourse = create_course($data);

        if ($activate_self_enrol) {
            $self_enrolment = $DB->get_record("enrol", array ('courseid' => $createdcourse->id, 'enrol' => 'self'), $fields='*');

            if(empty($self_enrolment)) {
                // If self enrolment is NOT activated for new courses, add one.
                $plugin = enrol_get_plugin('self');
                $plugin->add_instance($createdcourse, array("password"=>$self_enrol_password));
            } else {
                // If self enrolment is activated for new courses, activaten and update it.
                $self_enrolment->status = 0; // 0 is active!
                $self_enrolment->password = $self_enrol_password; // The PW is safed as plain text
                $DB->update_record("enrol", $self_enrolment);
            }
        }

        $returndata = array(
            'id' => $createdcourse->id,
            'category' => $createdcourse->category,
            'fullname' => $createdcourse->fullname,
            'shortname' => $createdcourse->shortname,
            'startdate' => $createdcourse->startdate,
            'visible' => $createdcourse->visible,
            'timecreated' => $createdcourse->timecreated,
            'timemodified' => $createdcourse->timemodified
        );

        return $returndata;
    }

    /**
     * Specifies the return value
     * @return external_single_structure the created course
     */
    public static function create_new_course_returns() {
        return new external_single_structure (
            array (
                'id' => new external_value ( PARAM_INT, 'The id of the newly created course' ),
                'category' => new external_value ( PARAM_INT, 'The category of the newly created course' ),
                'fullname' => new external_value ( PARAM_TEXT, 'The fullname of the newly created course' ),
                'shortname' => new external_value ( PARAM_TEXT, 'The shortname of the newly created course' ),
                'startdate' => new external_value ( PARAM_INT, 'The startdate of the newly created course' ),
                'visible' => new external_value ( PARAM_BOOL, 'The visible of the newly created course' ),
                'timecreated' => new external_value ( PARAM_INT, 'The id of the newly created course' ),
                'timemodified' => new external_value ( PARAM_INT, 'The id of the newly created course' )
        ));
    }

    // ------------------------------------------------------------------------------------------------------------------------

    /**
     * Returns description of input parameters
     * @return external_function_parameters
     */
    public static function enrol_user_into_course_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value (PARAM_INT, 'The id of the user to be enrolled'),
                'courseid' => new external_value (PARAM_INT, 'The id of the course to be enrolled into'),
                'roleid' => new external_value (PARAM_INT, 'The id of the role the user should be enrolled with')
        ));
    }

    /**
     * Wrap the core function enrol_user_into_course.
     * Enrols a user into a course
     *
     * @param int $userid Id of the user to enrol
     * @param int $courseid Id of course to enrol into
     * @param int $roleid Id of the role with which the user should be enrolled
     *
     * @return array Course info user was enrolled to
     */
    public static function enrol_user_into_course($userid, $courseid, $roleid) {
        global $DB;
        global $CFG;
        require_once("$CFG->dirroot/enrol/manual/externallib.php");

        $context = \context_course::instance($courseid);
        self::validate_context($context);
        // Check that the user has the permission to manual enrol.
        \require_capability('enrol/manual:enrol', $context);

        $params = array(
            'userid' => $userid,
            'courseid' => $courseid,
            'roleid' => $roleid
        );

        // Parameters validation.
        $params = self::validate_parameters(self::enrol_user_into_course_parameters(), $params);

        $enrolment = array('courseid' => $courseid, 'userid' => $userid, 'roleid' => $roleid);
        $enrolments[] = $enrolment;
        \enrol_manual_external::enrol_users($enrolments);

        $course = self::get_course_info($courseid);

        return $course;
    }

    /**
     * Specifies the return values
     *
     * @return external_single_structure returns a course
     */
    public static function enrol_user_into_course_returns() {
        return self::get_course_info_returns();
    }

    // ------------------------------------------------------------------------------------------------------------------------

    /**
     * Returns description of input parameters
     * @return external_function_parameters
     */
    public static function get_user_information_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value ( PARAM_INT, 'The id of the user' )
        ));
    }

    /**
     * Wrap the core function get_user_information.
     *
     * Gets and transforms the information of the given user
     * @param int $userid The id of the user
     */
    public static function get_user_information($userid) {
        global $DB, $CFG, $USER;

        $context = \context_system::instance();
        self::validate_context($context);
        \require_capability('moodle/user:viewdetails', $context);

        // Parameters validation.
        $params = self::validate_parameters(self::get_user_information_parameters (), array('userid' => $userid));

        $userinformation = user_get_users_by_id(array('userid' => $userid));
        // Important output: id, username, firstname, lastname, email, timecreated, timemodified, lang [de, en], auth [manual].

        $userinformationarray = [];
        foreach ($userinformation as $info) {
            // Example: Monday, 15-Aug-05 15:52:01 UTC.
            $info->timecreated = date('d.m.Y m:h', $info->timecreated);
            $info->timemodified = date('d.m.Y m:h', $info->timemodified);
            $info->lastlogin = date('d.m.Y m:h', $info->lastlogin);
            // Cast as an array.
            $userinformationarray[] = (array)$info;
        }
        $data['userinformation'] = $userinformationarray[0]; // We only retrieved one user.

        $usercourses = enrol_get_users_courses($userid, false, $fields = '*');

        // Get assignable roles with correct role name.
        $coursecontext = \context_course::instance(1);
        $assignableroles = \get_assignable_roles($coursecontext);

        $categories = $DB->get_records("course_categories", $conditions=null, $sort='sortorder ASC', $fields='id, name, parent, depth, path');
        // Used for unenrolling users.
        $user_enrolments = $DB->get_records_sql('SELECT e.courseid, ue.id FROM {user_enrolments} ue, {enrol} e WHERE e.id = ue.enrolid AND ue.userid = ?', array($userid));

        $all_level_ones = [];
        $all_level_twos = [];

        foreach ($usercourses as $course) {
            if ($course->category != 0) {
                $category = $categories[$course->category];
                $path_array = explode("/", $category->path);
                if (isset($path_array[1])) {
                    $path_array[1] = $categories[$path_array[1]]->name;
                    $course->level_one = $path_array[1];
                } else {
                    $course->level_one = "";
                }
                if (isset($path_array[2])) {
                    $path_array[2] = $categories[$path_array[2]]->name;
                    $course->level_two = $path_array[2];
                } else {
                    $course->level_two = "";
                }

                // Get the used Roles the user is enrolled as (teacher, student, ...).
                $context = \context_course::instance($course->id);
                $usedroles = get_user_roles($context, $userid);
                $course->roles = [];
                foreach ($usedroles as $role) {
                    $course->roles[] = $assignableroles[$role->roleid];
                }

                // Used for unenrolling users.
                $course->enrol_id = $user_enrolments[$course->id]->id;

                $courses_array[] = (array)$course;
            }
        }
        if (empty($courses_array)) {$courses_array = array();} // Initialize array if there are no courses.
        $data['userscourses'] = $courses_array;

        $data['uniquelevelones'] = [];
        $data['uniqueleveltwoes'] = [];
        foreach ($categories as $category) {
            if ($category->depth == 1) {
                array_push($data['uniquelevelones'], $category->name);
            }
            if ($category->depth == 2) {
                array_push($data['uniqueleveltwoes'], $category->name);
            }
        }

        // Filters should only appear once in the dropdown-menues.
        $data['uniquelevelones'] = array_filter(array_unique($data['uniquelevelones']));
        $data['uniqueleveltwoes'] = array_filter(array_unique($data['uniqueleveltwoes']));

        $context = \context_system::instance();
        if (\has_capability('moodle/user:loginas', $context) ) {
            $link = $CFG->wwwroot."/course/loginas.php?id=1&user=".$data['userinformation']['id']."&sesskey=".$USER->sesskey;
            $data['loginaslink'] = $link;
        } else {
            $data['loginaslink'] = false;
        }

        $link = $CFG->wwwroot."/user/profile.php?id=".$data['userinformation']['id'];
        $data['profilelink'] = $link;

        $link = $CFG->wwwroot."/admin/user.php?delete=".$data['userinformation']['id']."&sesskey=".$USER->sesskey;
        $data['deleteuserlink'] = $link;

        $link = $CFG->wwwroot."/user/editadvanced.php?id=".$data['userinformation']['id'];
        $data['edituserlink'] = $link;

        if (\has_capability('moodle/user:update', $context) ) {
            $data['isallowedtoupdateusers'] = true;
        } else {
            $data['isallowedtoupdateusers'] = false;
        }

        return array($data);
    }

    /**
     * Specifies the return values
     *
     * @return external_multiple_structure the user's courses and information
     */
    public static function get_user_information_returns() {
        return new external_multiple_structure (new external_single_structure (array (
            'userinformation' => new external_single_structure ( array (
                'id' => new external_value (PARAM_INT, 'id of the user'),
                'username' => new external_value (PARAM_TEXT, 'username of the user'),
                'firstname' => new external_value (PARAM_TEXT, 'firstname of the user'),
                'lastname' => new external_value (PARAM_TEXT, 'lastname of the user'),
                'email' => new external_value (PARAM_TEXT, 'email of the user'),
                'timecreated' => new external_value (PARAM_TEXT, 'timecreated of the user as date'),
                'timemodified' => new external_value (PARAM_TEXT, 'timemodified of the user as date'),
                'lastlogin' => new external_value (PARAM_TEXT, 'last login of the user as date'),
                'lang' => new external_value (PARAM_TEXT, 'lang of the user'),
                'auth' => new external_value (PARAM_TEXT, 'auth of the user')
            )),
            'userscourses' => new external_multiple_structure (new external_single_structure (array (
                'id' => new external_value (PARAM_INT, 'id of course'),
                'category' => new external_value (PARAM_INT, 'category id of the course'),
                'shortname' => new external_value (PARAM_TEXT, 'short name of the course'),
                'fullname' => new external_value (PARAM_TEXT, 'long name of the course'),
                'startdate' => new external_value (PARAM_INT, 'starting date of the course'),
                'visible' => new external_value(PARAM_INT, 'Is the course visible'),
                'level_one' => new external_value (PARAM_TEXT, 'the parent category name of the course'),
                'level_two' => new external_value (PARAM_TEXT, 'the direkt name of the course category'),
                'roles' => new external_multiple_structure (new external_value(PARAM_TEXT, 'array with roles for each course')),
                'enrol_id' => new external_value (PARAM_INT, 'id of user enrolment')
                // Additional information which could be added: idnumber, sortorder, defaultgroupingid, groupmode, groupmodeforce,
                // And: ctxid, ctxpath, ctsdepth, ctxinstance, ctxlevel.
            ))),
            'loginaslink' => new external_value(PARAM_TEXT, 'The link to login as the user', VALUE_OPTIONAL),
            'profilelink' => new external_value(PARAM_TEXT, 'The link to the users profile page'),
            'edituserlink' => new external_value(PARAM_TEXT, 'The link to edit the user'),
            'deleteuserlink' => new external_value(PARAM_TEXT, 'The link to delete the user, confirmation required'),
            'uniquelevelones' => new external_multiple_structure (
                    new external_value(PARAM_TEXT, 'array with unique first level categories')),
            'uniqueleveltwoes' => new external_multiple_structure (
                    new external_value(PARAM_TEXT, 'array with unique second level categories')),
            'isallowedtoupdateusers' => new external_value(PARAM_BOOL, "Is the user allowed to update users' globally?")
        )));
    }

    // ------------------------------------------------------------------------------------------------------------------------

    /**
     * Returns description of input parameters
     * @return external_function_parameters
     */
    public static function get_users_parameters() {
        return new external_function_parameters(
            array());
    }

    /**
     * Wrapper for core function get_users
     * Gets every moodle user
     */
    public static function get_users() {
        global $DB;

        $systemcontext = \context_system::instance();
        self::validate_context($systemcontext);
        \require_capability('moodle/site:viewparticipants', $systemcontext);
        $data = array();
        //$data['users'] = $DB->get_records('user', array('deleted' => '0'), null, 'id, username, firstname, lastname, email');
        $data['users'] = get_users_listing(); // Does not return guest and deleted users.
        //$data['users'] = get_users();

        return $data;
    }

    /**
     * Specifies return value
     *
     * @return array of users
     **/
    public static function get_users_returns() {
        return new external_single_structure(
            array(
                'users' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                        'id' => new external_value(PARAM_INT, 'id of user'),
                        'username' => new external_value(PARAM_RAW, 'username of user'),
                        'firstname' => new external_value(PARAM_RAW, 'firstname of user'),
                        'lastname' => new external_value(PARAM_RAW, 'lastname of user'),
                        'email' => new external_value(PARAM_RAW, 'email adress of user')
                        )
                    )
                )
            ));
    }

    // ------------------------------------------------------------------------------------------------------------------------

    /**
     * Returns description of input parameters
     * @return external_function_parameters
     */
    public static function get_courses_parameters() {
        return new external_function_parameters(
            array());
    }
    /**
     * Wrapper for core function get_courses
     *
     * Gets every moodle course
     */
    public static function get_courses() {
        global $DB;

        self::validate_parameters(self::get_courses_parameters(), array());
        $context = \context_system::instance();
        self::validate_context($context);
        // Is the closest to the needed capability. Is used in /course/management.php.
        \require_capability('moodle/course:viewhiddencourses', $context);

        $categories = $DB->get_records("course_categories", array("visible"=>"1"), $sort='sortorder ASC', $fields='id, name, parent, depth, path');
        $courses = $DB->get_records("course", $conditions=null, $sort='', $fields='id, shortname, fullname, visible, category');

        $all_level_ones = [];
        $all_level_twos = [];

        foreach ($courses as $course) {
            if ($course->category != 0) {
                $category = $categories[$course->category];
                $path_array = explode("/", $category->path);
                if (isset($path_array[1])) {
                    $path_array[1] = $categories[$path_array[1]]->name;
                    $course->level_one = $path_array[1];
                } else {
                    $course->level_one = "";
                }
                if (isset($path_array[2])) {
                    $path_array[2] = $categories[$path_array[2]]->name;
                    $course->level_two = $path_array[2];
                } else {
                    $course->level_two = "";
                }
                $courses_array[] = (array)$course;

            }
        }
        $data['courses'] = $courses_array;

        $data['uniquelevelones'] = [];
        $data['uniqueleveltwoes'] = [];
        foreach ($categories as $category) {
            if ($category->depth == 1) {
                array_push($data['uniquelevelones'], $category->name);
            }
            if ($category->depth == 2) {
                array_push($data['uniqueleveltwoes'], $category->name);
            }
        }

        // Filters should only appear once in the dropdown-menues.
        $data['uniquelevelones'] = array_filter(array_unique($data['uniquelevelones']));
        $data['uniqueleveltwoes'] = array_filter(array_unique($data['uniqueleveltwoes']));

        return $data;
    }

    /**
     * Specifies return values
     *
     * @return array of courses
     */
    public static function get_courses_returns() {
        return new external_single_structure (
            array (
                'courses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'id of course'),
                            'shortname' => new external_value(PARAM_RAW, 'shortname of course'),
                            'fullname' => new external_value(PARAM_RAW, 'course name'),
                            'level_two' => new external_value(PARAM_RAW,  'parent category'),
                            'level_one' => new external_value(PARAM_RAW, 'course category'),
                            'visible' => new external_value(PARAM_INT, 'Is the course visible')
                        )
                    )
                ),
                'uniqueleveltwoes' => new external_multiple_structure (
                    new external_value(PARAM_TEXT, 'array with unique category names of all first levels')),
                'uniquelevelones' => new external_multiple_structure (
                    new external_value(PARAM_TEXT, 'array with unique category names of all second levels'))
            )
        );
    }

    // ------------------------------------------------------------------------------------------------------------------------

    /**
     * Returns description of input parameters
     * @return external_function_parameters
     */
    public static function get_course_info_parameters() {
        return new external_function_parameters(
            array(
                'courseID' => new external_value(PARAM_RAW, 'id of course you want to show')
        ));
    }

    /**
     * Wrapper of core function get_course_info
     *
     * Accumulates and transforms course data to be displayed
     *
     * @param int $courseid Id of the course which needs to be displayed
     */
    public static function get_course_info($courseid) {
        global $DB, $CFG, $COURSE;

        // Check parameters.
        $params = self::validate_parameters(self::get_course_info_parameters(), array('courseID' => $courseid));
        $courseid = $params['courseID'];

        $coursecontext = \context_course::instance($courseid);
        self::validate_context($coursecontext);
        // Is the user allowed to change course_settings?
        \require_capability('moodle/course:view', $coursecontext);

        // Get information about the course.
        $select = "SELECT c.id, c.shortname, c.fullname, c.visible, c.timecreated, cat.path FROM {course} c, ".
                  "{course_categories} cat WHERE c.category = cat.id AND c.id = ".$courseid;
        $coursedetails = $DB->get_record_sql($select);
        $coursedetails = (array)$coursedetails;
        $coursedetails['timecreated'] = date('d.m.Y m:h', $coursedetails['timecreated']); // Convert timestamp to readable format.

        // Get whole course-path.
        // Extract IDs from path and remove empty values by using array_filter.
        $parentcategoriesids = array_filter(explode('/', $coursedetails['path']));

        // Select the name of all parent categories.
        $parentcategoriesnames = $DB->get_records_list('course_categories', 'id', $parentcategoriesids, null, 'id,name');
        $pathcategories = [];
        foreach ($parentcategoriesnames as $val) {
            $pathcategories[] = $val->name;
        }
        $coursedetails['level_one'] = $pathcategories[0];
        isset($pathcategories[1]) ? $coursedetails['level_two'] = $pathcategories[1] : $coursedetails['level_two'] = "";
        $coursedetails['path'] = implode('/', $pathcategories);

        // How many students are enrolled in the course?
        $coursedetails['enrolledUsers'] = \count_enrolled_users($coursecontext, $withcapability = '', $groupid = '0');

        // Get assignable roles in the course.
        $usedrolesincourse = get_assignable_roles($coursecontext);

        // Which roles are used and how many users have this role?
        $roles = array();
        $rolesincourse = [];

        foreach ($usedrolesincourse as $rid => $rname) {
            $rolename = $rname;
            $rolenumber = \count_role_users($rid, $coursecontext);
            if ($rolenumber != 0) {
                $roles[] = ['roleName' => $rolename, 'roleNumber' => $rolenumber];
                $rolesincourse[] = $rolename;
            }
        }
        asort($rolesincourse);

        // Get userinformation about users in course.
        $usersraw = \get_enrolled_users($coursecontext, $withcapability = '', $groupid = 0,
        $userfields = 'u.id,u.username,u.firstname, u.lastname', $orderby = '', $limitfrom = 0, $limitnum = 0);
        $users = array();
        $user_enrolments = $DB->get_records_sql('SELECT ue.userid, ue.id FROM {user_enrolments} ue, {enrol} e WHERE e.id = ue.enrolid AND e.courseid = ?', array($courseid));
        foreach ($usersraw as $u) {
            $u = (array)$u;
            $u['lastaccess'] = date('d.m.Y m:h', $DB->get_field('user_lastaccess', 'timeaccess', array('courseid'=>$courseid, 'userid'=>$u['id'])));
            // Find user specific roles.
            $rolesofuser = get_user_roles($coursecontext, $u['id']);
            $userroles = [];
            foreach ($rolesofuser as $role) {
                $userroles[] = $usedrolesincourse[$role->roleid];
            }
            $u['roles'] = $userroles;
            $u['enrol_id'] = $user_enrolments[$u['id']]->id;
            $users[] = $u;
        }

        // Get Activities in course.
        $activities = array();
        $modules = \get_array_of_activities($courseid);
        foreach ($modules as $mo) {
            $section = \get_section_name($courseid, $mo->section);
            $activity = ['section' => $section, 'activity' => $mo->mod, 'name' => $mo->name, 'visible' => $mo->visible];
            $activities[] = $activity;
        }

        // Get Enrolment Methods in course.
        $enrolmentmethods = array();
        $instances = enrol_get_instances($courseid, false);
        $plugins   = enrol_get_plugins(false);
        // Iterate through enrol plugins and add to the display table.
        foreach ($instances as $instance) {
            $plugin = $plugins[$instance->enrol];

            $enrolmentmethod['methodname'] = $plugin->get_instance_name($instance);
            $enrolmentmethod['enabled'] = false;
            if (!enrol_is_enabled($instance->enrol) or $instance->status != ENROL_INSTANCE_ENABLED) {
                $enrolmentmethod['enabled'] = true;
            }

            $enrolmentmethod['users'] = $DB->count_records('user_enrolments', array('enrolid' => $instance->id));
            $enrolmentmethods[] = $enrolmentmethod;
        }

        // Get links for navigation.
        $settingslink = $CFG->wwwroot."/course/edit.php?id=".$courseid;
        if (\has_capability('moodle/course:delete', $coursecontext) ) {
            $deletelink = $CFG->wwwroot."/course/delete.php?id=".$courseid;
        } else {
            $deletelink = false;
        }

        if (\has_capability('moodle/course:update', \context_system::instance()) ) {
            $isallowedtoupdatecourse = true;
        } else {
            $isallowedtoupdatecourse = false;
        }

        $courselink = $CFG->wwwroot."/course/view.php?id=".$courseid;

        $links = array(
            'settingslink' => $settingslink,
            'deletelink' => $deletelink,
            'courselink' => $courselink
        );
        $data = array(
            'courseDetails' => (array)$coursedetails,
            'rolesincourse' => (array)$rolesincourse,
            'roles' => (array)$roles,
            'users' => (array)$users,
            'activities' => (array)$activities,
            'links' => $links,
            'enrolmentMethods' => (array)$enrolmentmethods,
            'isallowedtoupdatecourse' => $isallowedtoupdatecourse
        );

        //error_log(print_r('data -------------', TRUE));
        //error_log(str_replace("\n", "", print_r($data['enrolmentMethods'], TRUE)));

        return (array)$data;
    }

    /**
     * Specifies return values
     * @return external_single_structure a course with addition information
     */
    public static function get_course_info_returns() {
        return new external_single_structure( array(
            'courseDetails' => new external_single_structure( array(
                'id' => new external_value(PARAM_INT, 'id of course'),
                'shortname' => new external_value(PARAM_RAW, 'shortname of course'),
                'fullname' => new external_value(PARAM_RAW, 'course name'),
                'visible' => new external_value(PARAM_BOOL, 'Is the course visible?'),
                'path' => new external_value(PARAM_RAW, 'path to course'),
                'enrolledUsers' => new external_value(PARAM_INT, 'number of users, without teachers'),
                'timecreated' => new external_value(PARAM_TEXT, 'time the course was created as readable date format'),
                'level_one' => new external_value(PARAM_TEXT, 'first level of the course'),
                'level_two' => new external_value(PARAM_TEXT, 'second level of the course')
            )),
            'rolesincourse' => new external_multiple_structure (new external_value(PARAM_TEXT, 'array with roles used in course')),
            'roles' => new external_multiple_structure(
            new external_single_structure( array(
                'roleName' => new external_value(PARAM_RAW, 'name of one role in course'),
                'roleNumber' => new external_value(PARAM_INT, 'number of participants with role = roleName')
            ))),
            'users' => new external_multiple_structure(
                new external_single_structure( array(
                    'id' => new external_value(PARAM_INT, 'id of user'),
                    'username' => new external_value(PARAM_RAW, 'name of user'),
                    'firstname' => new external_value(PARAM_RAW, 'firstname of user'),
                    'lastname' => new external_value(PARAM_RAW, 'lastname of user'),
                    'lastaccess' => new external_value(PARAM_RAW, 'lastaccess of the user to the course'),
                    'roles' => new external_multiple_structure (new external_value(PARAM_TEXT, 'array with roles for each user')),
                    'enrol_id' => new external_value(PARAM_INT, 'id of user enrolment to course')
                ))),
                'activities' => new external_multiple_structure(
                new external_single_structure( array(
                    'section' => new external_value(PARAM_RAW, 'Name of section, in which the activity appears'),
                    'activity' => new external_value(PARAM_RAW, 'kind of activity'),
                    'name' => new external_value(PARAM_RAW, 'Name of this activity'),
                    'visible' => new external_value(PARAM_INT, 'Is the activity visible? 1: yes, 0: no')
                ))),
                'links' => new external_single_structure( array(
                    'settingslink' => new external_value(PARAM_RAW, 'link to the settings of the course'),
                    'deletelink' => new external_value(PARAM_RAW, 'link to delete the course if allowed, '
                        . 'additional affirmation needed afterwards', VALUE_OPTIONAL),
                    'courselink' => new external_value(PARAM_RAW, 'link to the course')
                )),
            'enrolmentMethods' => new external_multiple_structure(
                new external_single_structure( array(
                    'methodname' => new external_value(PARAM_TEXT, 'Name of the enrolment method'),
                    'enabled' => new external_value(PARAM_BOOL, 'Is method enabled'),
                    'users' => new external_value(PARAM_INT, 'Amount of users enrolled with this method')
                ))),
                'isallowedtoupdatecourse' => new external_value(PARAM_BOOL, "Is the user allowed to update the course globally?")
        ));
    }


    // ------------------------------------------------------------------------------------------------------------------------

    /**
     * Returns description of input parameters
     * @return external_function_parameters
     */
    public static function get_assignable_roles_parameters() {
        return new external_function_parameters( array(
            'courseID' => new external_value(PARAM_RAW, 'id of course you want to show')
        ));
    }

    /**
     * Wrapper for core function get_assignable_roles
     *
     * @param int $courseid Id of the course the roles are present
     */
    public static function get_assignable_roles($courseid) {
        global $CFG, $PAGE;

        $coursecontext = \context_course::instance($courseid);
        self::validate_context($coursecontext);
        // Is the user allowed to enrol a student into this course?
        \require_capability('enrol/manual:enrol', $coursecontext);

        // Parameter validation.
        $params = self::validate_parameters(self::get_course_info_parameters(), array('courseID' => $courseid));

        // Get assignable roles in the course.
        require_once($CFG->dirroot.'/enrol/locallib.php');
        $course = get_course($courseid);
        $manager = new \course_enrolment_manager($PAGE, $course);
        $usedroles = $manager->get_assignable_roles();

        $count = 0;
        $arrayofroles = [];
        foreach ($usedroles as $roleid => $rolename) {
            $arrayofroles[$count]['id'] = $roleid;
            $arrayofroles[$count]['name'] = $rolename;
            $count++;
        }

        // Put the student role in first place.
        $studentrole = array_values(get_archetype_roles('student'))[0];
        $count = 0;
        foreach ($arrayofroles as $role) {
            if ($role['id'] == $studentrole->id) {
                $tmp = $role;
                unset($arrayofroles[$count]); // Funzt nicht
                array_unshift($arrayofroles, $role);
            }
            $count++;
        }

        $data = array(
        'assignableRoles' => (array)$arrayofroles
        );

        return $data;
    }

    /**
     * Specifies return parameters
     * @return external_single_structure the assignable Roles
     */
    public static function get_assignable_roles_returns() {
        return new external_single_structure( array(
            'assignableRoles' => new external_multiple_structure(
                new external_single_structure( array(
                    'id' => new external_value(PARAM_INT, 'id of the role'),
                    'name' => new external_value(PARAM_RAW, 'Name of the role')
                ))
            )
        ));
    }

    // ------------------------------------------------------------------------------------------------------------------------

    /**
     * Returns description of input parameters
     * @return external_function_parameters
     */
    public static function toggle_course_visibility_parameters() {
        return new external_function_parameters(array(
            'courseID' => new external_value(PARAM_INT, 'id of course')
        ));
    }

    /**
     * Wrapper for core function toggle_course_visibility
     *
     * @param int $courseid Id of the course which is to be toggled
     */
    public static function toggle_course_visibility($courseid) {

        $coursecontext = \context_course::instance($courseid);
        self::validate_context($coursecontext);
        // Is the user allowed to change course_settings?
        \require_capability('moodle/course:update', $coursecontext);

        // Checking parameters.
        self::validate_parameters(self::toggle_course_visibility_parameters(), array('courseID' => $courseid));
        // Security checks.
        $coursecontext = \context_course::instance($courseid);
        self::validate_context($coursecontext);
        // Is the user allowed to change the visibility?
        \require_capability('moodle/course:visibility', $coursecontext);

        $course = self::get_course_info($courseid);
        // Second param is the desired visibility value.
        course_change_visibility($courseid, !($course['courseDetails']['visible']));
        $course['courseDetails']['visible'] = !$course['courseDetails']['visible'];

        return $course;
    }

    /**
     * Specifies return parameters
     * @return external_single_structure a course with toggled visibility
     */
    public static function toggle_course_visibility_returns() {
        return self::get_course_info_returns();
    }
}
