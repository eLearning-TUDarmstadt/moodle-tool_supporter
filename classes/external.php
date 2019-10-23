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
 * @copyright  2019 Benedikt Schneider, Klara Saary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_supporter;

defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->dirroot/webservice/externallib.php");
require_once("$CFG->dirroot/course/lib.php");
require_once("$CFG->dirroot/user/lib.php");
require_once("$CFG->libdir/adminlib.php");
require_once($CFG->dirroot . '/course/externallib.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;
use invalid_parameter_exception;

/**
 * Class external defines several functions to prepare data for further use
 * @package tool_supporter
 * @copyright  2019 Benedikt Schneider, Klara Saary
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
            'activateselfenrol' => new external_value ( PARAM_BOOL, 'Toggles if self_enrolment should be activated' ),
            'selfenrolpassword' => new external_value ( PARAM_TEXT, 'Password of self enrolment' ),
            'startdate' => new external_value ( PARAM_TEXT, 'Course start date' ),
            'enddate' => new external_value ( PARAM_TEXT, 'Course end date' ),
        ));
    }

    /**
     * Wrap the core function to create a new course, e.g. activating self enrolment
     *
     * @param string $shortname
     * @param string $fullname
     * @param bool $visible
     * @param int $categoryid
     * @param bool $activateselfenrol
     * @param string $selfenrolpassword
     * @param int $startdate
     * @param int $enddate
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     * @throws \required_capability_exception
     * @throws \restricted_context_exception
     * @throws invalid_parameter_exception
     */
    public static function create_new_course($shortname, $fullname, $visible, $categoryid, $activateselfenrol,
                                             $selfenrolpassword, $startdate, $enddate) {
        global $DB;

        self::validate_context(\context_coursecat::instance($categoryid));
        \require_capability('moodle/course:create', \context_system::instance());

        $array = array (
            'shortname' => $shortname,
            'fullname' => $fullname,
            'visible' => $visible,
            'categoryid' => $categoryid,
            'activateselfenrol' => $activateselfenrol,
            'selfenrolpassword' => $selfenrolpassword,
            'startdate' => $startdate,
            'enddate' => $enddate,
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

        // Convert string to date.
        $data->startdate = strtotime($params['startdate']);
        $data->enddate = strtotime($params['enddate']);

        $data->numsections = 10;
        $createdcourse = create_course($data);

        if ($activateselfenrol) {
            $selfenrolment = $DB->get_record("enrol", array ('courseid' => $createdcourse->id, 'enrol' => 'self'), '*');

            if (empty($selfenrolment)) {
                // If self enrolment is NOT activated for new courses, add one.
                $plugin = enrol_get_plugin('self');
                $plugin->add_instance($createdcourse, array("password" => $selfenrolpassword));
            } else {
                // If self enrolment is activated for new courses, activaten and update it.
                $selfenrolment->status = 0; // 0 is active!
                $selfenrolment->password = $selfenrolpassword; // The PW is safed as plain text.
                $DB->update_record("enrol", $selfenrolment);
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
     * @throws \moodle_exception
     * @throws \required_capability_exception
     * @throws \restricted_context_exception
     * @throws invalid_parameter_exception
     */
    public static function enrol_user_into_course($userid, $courseid, $roleid) {
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
        self::validate_parameters(self::enrol_user_into_course_parameters(), $params);

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
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \required_capability_exception
     * @throws \restricted_context_exception
     * @throws invalid_parameter_exception
     */
    public static function get_user_information($userid) {
        global $DB, $CFG, $USER;

        $context = \context_system::instance();
        self::validate_context($context);
        \require_capability('moodle/user:viewdetails', $context);

        // Parameters validation.
        self::validate_parameters(self::get_user_information_parameters (), array('userid' => $userid));

        $userinformation = user_get_users_by_id(array('userid' => $userid));

        $userinformationarray = [];
        $neverstring = get_string('never', 'moodle');
        foreach ($userinformation as $info) {
            // Example: Monday, 15-Aug-05 15:52:01 UTC.
            if ($info->timecreated == 0) {
                $info->timecreated = $neverstring;
            } else {
                $info->timecreated =
                    userdate($info->timecreated, get_string('strftimesecondsdatetimeshort', 'tool_supporter'));
            }
            if ($info->timemodified == 0) {
                $info->timemodified = $neverstring;
            } else {
                $info->timemodified =
                    userdate($info->timemodified, get_string('strftimesecondsdatetimeshort', 'tool_supporter'));
            }
            if ($info->lastlogin == 0) {
                $info->lastlogin = $neverstring;
            } else {
                $info->lastlogin =
                    userdate($info->lastlogin, get_string('strftimesecondsdatetimeshort', 'tool_supporter'));
            }
            // Cast as an array.
            $userinformationarray[] = (array)$info;
        }
        $data['userinformation'] = $userinformationarray[0]; // We only retrieved one user.

        $usercourses = enrol_get_users_courses($userid, false, $fields = '*');

        // Get assignable roles with correct role name.
        $coursecontext = \context_course::instance(1);
        $assignableroles = \get_assignable_roles($coursecontext);

        $categories = $DB->get_records("course_categories", null, 'sortorder ASC',
                                 'id, name, parent, depth, path');
        // Used for unenrolling users.
        $userenrolments = $DB->get_records_sql('SELECT e.courseid, ue.id FROM {user_enrolments} ue,
                                               {enrol} e WHERE e.id = ue.enrolid AND ue.userid = ?', array($userid));

        $data['uniquelevelones'] = [];
        $data['uniqueleveltwoes'] = [];
        $coursesarray = [];
        foreach ($usercourses as $course) {
            if ($course->category != 0) {
                $category = $categories[$course->category];
                $patharray = explode("/", $category->path);
                if (isset($patharray[1])) {
                    // Support multilang course categories.
                    $patharray[1] = external_format_string($categories[$patharray[1]]->name, $context);
                    $course->level_one = $patharray[1];
                    array_push($data['uniquelevelones'], $patharray[1]);
                } else {
                    $course->level_one = "";
                }

                if (isset($patharray[2])) {
                    // Support multilang course categories.
                    $patharray[2] = external_format_string($categories[$patharray[2]]->name, $context);
                    $course->level_two = $patharray[2];
                    array_push($data['uniqueleveltwoes'], $patharray[2]);
                } else {
                    $course->level_two = "";
                }

                // Get the used Roles the user is enrolled as (teacher, student, ...).
                $usedroles = get_user_roles(\context_course::instance($course->id), $userid, false);
                $course->roles = [];
                foreach ($usedroles as $role) {
                    $course->roles[] = $assignableroles[$role->roleid];
                }

                // Used for unenrolling users.
                $course->enrol_id = $userenrolments[$course->id]->id;

                // Support multilang course fullnames.
                $course->fullname = external_format_string($course->fullname, $context);

                $coursesarray[] = (array)$course;
            }
        }
        $data['userscourses'] = $coursesarray;

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

        $link = $CFG->wwwroot."/message/notificationpreferences.php?userid=".$data['userinformation']['id'];
        $data['usernotificationpreferenceslink'] = $link;

        $data['wwwroot'] = $CFG->wwwroot;

        if (\has_capability('moodle/user:update', $context) ) {
            $data['isallowedtoupdateusers'] = true;
        } else {
            $data['isallowedtoupdateusers'] = false;
        }

        $data['config'] = array(
            'showusername' => get_config('tool_supporter', 'user_details_showusername'),
            'showidnumber' => get_config('tool_supporter', 'user_details_showidnumber'),
            'showfirstname' => get_config('tool_supporter', 'user_details_showfirstname'),
            'showlastname' => get_config('tool_supporter', 'user_details_showlastname'),
            'showmailadress' => get_config('tool_supporter', 'user_details_showmailadress'),
            'showtimecreated' => get_config('tool_supporter', 'user_details_showtimecreated'),
            'showtimemodified' => get_config('tool_supporter', 'user_details_showtimemodified'),
            'showlastlogin' => get_config('tool_supporter', 'user_details_showlastlogin'),
        );

        // Get level labels.
        $labels = get_config('tool_supporter', 'level_labels');
        $count = 1; // Root is level 0, so we begin at 1.
        foreach (explode(';', $labels) as $label) {
            $data['label_level_'.$count] = external_format_string($label, $context); // Each label will be available under {{label_level_0}}, {{label_level_1}}, etc.
            $count++;
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
                'auth' => new external_value (PARAM_TEXT, 'auth of the user'),
                'idnumber' => new external_value (PARAM_TEXT, 'idnumber of the user'),
            )),
            'config' => new external_single_structure( (array (
                'showusername' => new external_value(PARAM_BOOL, "Show username of user in user details"),
                'showidnumber' => new external_value(PARAM_BOOL, "Show idnumber of user in user details"),
                'showfirstname' => new external_value(PARAM_BOOL, "Show first name of user in user details"),
                'showlastname' => new external_value(PARAM_BOOL, "Show last name of user in user details"),
                'showmailadress' => new external_value(PARAM_BOOL, "Show mail adress of user in user details"),
                'showtimecreated' => new external_value(PARAM_BOOL, "Show time created of user in user details"),
                'showtimemodified' => new external_value(PARAM_BOOL, "Show time modified of user in user details"),
                'showlastlogin' => new external_value(PARAM_BOOL, "Show last login of user in user details"),
            ))),
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
            'usernotificationpreferenceslink' => new external_value(PARAM_TEXT, 'The link to edit the user\'s notification preferences'),
            'deleteuserlink' => new external_value(PARAM_TEXT, 'The link to delete the user, confirmation required'),
            'uniquelevelones' => new external_multiple_structure (
                    new external_value(PARAM_TEXT, 'array with unique first level categories')),
            'uniqueleveltwoes' => new external_multiple_structure (
                    new external_value(PARAM_TEXT, 'array with unique second level categories')),
            'isallowedtoupdateusers' => new external_value(PARAM_BOOL, "Is the user allowed to update users' globally?"),
            'wwwroot' => new external_value(PARAM_TEXT, "Root URL of this moodle instance"),
            // For now, it is limited to 5 levels and this implementation is ugly.
            'label_level_1' => new external_value(PARAM_TEXT, 'label of first level', VALUE_OPTIONAL),
            'label_level_2' => new external_value(PARAM_TEXT, 'label of second level', VALUE_OPTIONAL),
            'label_level_3' => new external_value(PARAM_TEXT, 'label of third level', VALUE_OPTIONAL),
            'label_level_4' => new external_value(PARAM_TEXT, 'label of fourth level', VALUE_OPTIONAL),
            'label_level_5' => new external_value(PARAM_TEXT, 'label of fifth level', VALUE_OPTIONAL),
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
        if (get_config('tool_supporter', 'user_table_excludesuspended')) {
            $data['users'] = $DB->get_records('user', array('deleted' => '0', 'suspended' => 0), null,
                                        'id, idnumber, username, firstname, lastname, email');
        } else {
            $data['users'] = $DB->get_records('user', array('deleted' => '0'), null,
                                        'id, idnumber, username, firstname, lastname, email');
        }

        return $data;
    }

    /**
     * Specifies return value
     *
     * @return external_single_structure of array of users
     **/
    public static function get_users_returns() {
        return new external_single_structure(
            array(
                'users' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'id of user'),
                            'idnumber' => new external_value(PARAM_RAW, 'idnumber of user'),
                            'username' => new external_value(PARAM_TEXT, 'username of user'),
                            'firstname' => new external_value(PARAM_TEXT, 'firstname of user'),
                            'lastname' => new external_value(PARAM_TEXT, 'lastname of user'),
                            'email' => new external_value(PARAM_TEXT, 'email adress of user')
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

        $viewhiddencategories = get_config('tool_supporter', 'course_table_viewhiddenscat');
        if ($viewhiddencategories) {
            $categories = $DB->get_records("course_categories", array(), 'sortorder ASC',
                'id, name, parent, depth, path');
        } else {
            // Only show visible categories
            $categories = $DB->get_records("course_categories", array("visible" => "1"), 'sortorder ASC',
                'id, name, parent, depth, path');
        }

        $viewhiddencourses = get_config('tool_supporter', 'course_table_viewhiddenscourses');
        if ($viewhiddencourses) {
            $courses = $DB->get_records("course", null, '',
                'id, shortname, fullname, visible, category');
        } else {
            // Only show visible courses
            $courses = $DB->get_records("course", array("visible" => "1"), '',
                'id, shortname, fullname, visible, category');
        }

        $coursesarray = [];
        foreach ($courses as $course) {
            if ($course->category != 0) {
                $category = $categories[$course->category];
                $patharray = explode("/", $category->path);
                if (isset($patharray[1])) {
                    // Support multilang course categories.
                    $patharray[1] = external_format_string($categories[$patharray[1]]->name, $context);
                    $course->level_one = $patharray[1];
                } else {
                    $course->level_one = "";
                }
                if (isset($patharray[2])) {
                    // Support multilang course categories.
                    $patharray[2] = external_format_string($categories[$patharray[2]]->name, $context);
                    $course->level_two = $patharray[2];
                } else {
                    $course->level_two = "";
                }

                // Support multilang course fullnames.
                $course->fullname = external_format_string($course->fullname, $context);

                $coursesarray[] = (array)$course;
            }
        }
        $data['courses'] = $coursesarray;

        $data['uniquelevelones'] = [];
        $data['uniqueleveltwoes'] = [];
        foreach ($categories as $category) {
            if ($category->depth == 1) {
                // Support multilang course categories.
                array_push($data['uniquelevelones'], external_format_string($category->name, $context));
            }
            if ($category->depth == 2) {
                // Support multilang course categories.
                array_push($data['uniqueleveltwoes'], external_format_string($category->name, $context));
            }
        }

        // Filters should only appear once in the dropdown-menues.
        $data['uniquelevelones'] = array_filter(array_unique($data['uniquelevelones']));
        $data['uniqueleveltwoes'] = array_filter(array_unique($data['uniqueleveltwoes']));

        // Get level labels.
        $labels = get_config('tool_supporter', 'level_labels');
        $count = 1; // Root is level 0, so we begin at 1.
        foreach (explode(';', $labels) as $label) {
            $data['label_level_'.$count] = external_format_string($label, $context); // Each label will be available under {{label_level_0}}, {{label_level_1}}, etc.
            $count++;
        }

        return $data;
    }

    /**
     * Specifies return values
     *
     * @return external_single_structure of array of courses
     */
    public static function get_courses_returns() {
        return new external_single_structure (array (
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
                new external_value(PARAM_TEXT, 'array with unique category names of all first levels')
            ),
            'uniquelevelones' => new external_multiple_structure (
                new external_value(PARAM_TEXT, 'array with unique category names of all second levels')
            ),
            // For now, it is limited to 5 levels and this implementation is ugly.
            'label_level_1' => new external_value(PARAM_TEXT, 'label of first level', VALUE_OPTIONAL),
            'label_level_2' => new external_value(PARAM_TEXT, 'label of second level', VALUE_OPTIONAL),
            'label_level_3' => new external_value(PARAM_TEXT, 'label of third level', VALUE_OPTIONAL),
            'label_level_4' => new external_value(PARAM_TEXT, 'label of fourth level', VALUE_OPTIONAL),
            'label_level_5' => new external_value(PARAM_TEXT, 'label of fifth level', VALUE_OPTIONAL),
        ));
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
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     * @throws \required_capability_exception
     * @throws \restricted_context_exception
     * @throws invalid_parameter_exception
     */
    public static function get_course_info($courseid) {
        global $DB, $CFG;

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
        if ($coursedetails['timecreated'] == 0) {
            $coursedetails['timecreated'] = get_string('never', 'moodle');
        } else {
            $coursedetails['timecreated'] =
                userdate($coursedetails['timecreated'], get_string('strftimesecondsdatetimeshort', 'tool_supporter')); // Convert timestamp to readable format.
        }
        // Support course multilang fullnames.
        $coursedetails['fullname'] = external_format_string($coursedetails['fullname'], $coursecontext);

        // Get whole course-path.
        // Extract IDs from path and remove empty values by using array_filter.
        $parentcategoriesids = array_filter(explode('/', $coursedetails['path']));

        // Select the name of all parent categories.
        $parentcatnames = $DB->get_records_list('course_categories', 'id', $parentcategoriesids, null, 'id,name');
        $pathcategories = [];
        foreach ($parentcatnames as $val) {
            // Support multilang course categories.
            $pathcategories[] = external_format_string($val->name, $coursecontext);
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
            // Support multilang roles.
            $rolename = external_format_string($rname, $coursecontext);
            $rolenumber = \count_role_users($rid, $coursecontext);
            if ($rolenumber != 0) {
                $roles[] = ['roleName' => $rolename, 'roleNumber' => $rolenumber];
                $rolesincourse[] = $rolename;
            }
        }
        asort($rolesincourse);

        // Get userinformation about users in course.
        $usersraw = \get_enrolled_users($coursecontext, $withcapability = '', $groupid = 0,
        $userfields = 'u.id,u.username,u.firstname, u.lastname', '', 0, 0);
        $users = array();
        $userenrolments = $DB->get_records_sql('SELECT ue.userid, ue.id FROM {user_enrolments} ue,
                                               {enrol} e WHERE e.id = ue.enrolid AND e.courseid = ?', array($courseid));
        foreach ($usersraw as $u) {
            $u = (array)$u;

            $userlastaccess = $DB->get_field('user_lastaccess', 'timeaccess',
                array('courseid' => $courseid, 'userid' => $u['id']));

            if ($userlastaccess == 0) {
                $u['lastaccess'] = get_string('never', 'moodle');
            } else {
                $u['lastaccess'] = date('Y-m-d H:i:s', $userlastaccess);
            }

            // Find user specific roles, but without parent context (no global roles).
            $rolesofuser = get_user_roles($coursecontext, $u['id'], false);
            $userroles = [];
            foreach ($rolesofuser as $role) {
                $userroles[] = $usedrolesincourse[$role->roleid];
            }
            $u['roles'] = $userroles;
            $u['enrol_id'] = $userenrolments[$u['id']]->id;
            $users[] = $u;
        }

        // Get Activities in course.
        $activities = array();
        $modules = \get_array_of_activities($courseid);
        foreach ($modules as $mo) {
            $section = \get_section_name($courseid, $mo->section);
            // Support section and activity multilang names.
            $activity = ['section' => external_format_string($section, $coursecontext),
                'activity' => get_string('pluginname', $mo->mod),
                'name' => external_format_string($mo->name, $coursecontext), 'visible' => $mo->visible];
            $activities[] = $activity;
        }

        // Get Enrolment Methods in course.
        $enrolmentmethods = array();
        $instances = enrol_get_instances($courseid, false);
        $plugins   = enrol_get_plugins(false);
        // Iterate through enrol plugins and add to the display table.
        foreach ($instances as $instance) {
            $plugin = $plugins[$instance->enrol];

            $enrolmentmethod['password'] = $instance->password;
            $enrolmentmethod['methodname'] = $plugin->get_instance_name($instance);
            $enrolmentmethod['enabled'] = false;
            if (!enrol_is_enabled($instance->enrol) or $instance->status != ENROL_INSTANCE_ENABLED) {
                $enrolmentmethod['enabled'] = true;
            }

            $enrolmentmethod['users_count'] = $DB->count_records('user_enrolments', array('enrolid' => $instance->id));
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
            $capupdatecourse = true;
        } else {
            $capupdatecourse = false;
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
            'isallowedtoupdatecourse' => $capupdatecourse,
            'wwwroot' => $CFG->wwwroot
        );

        $data['config'] = array(
            'showshortname' => get_config('tool_supporter', 'course_details_showshortname'),
            'showfullname'  => get_config('tool_supporter', 'course_details_showfullname'),
            'showvisible'  => get_config('tool_supporter', 'course_details_showvisible'),
            'showpath'  => get_config('tool_supporter', 'course_details_showpath'),
            'showtimecreated'  => get_config('tool_supporter', 'course_details_showtimecreated'),
            'showusersamount'  => get_config('tool_supporter', 'course_details_showusersamount'),
            'showrolesandamount'  => get_config('tool_supporter', 'course_details_showrolesandamount'),
        );

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
            'config' => new external_single_structure( (array (
                'showshortname' => new external_value(PARAM_BOOL, "Config setting if courses shortname should be displayed"),
                'showfullname' => new external_value(PARAM_BOOL, "Config setting if courses fullname should be displayed"),
                'showvisible' => new external_value(PARAM_BOOL, "Config setting if courses visible status should be displayed"),
                'showpath' => new external_value(PARAM_BOOL, "Config setting if courses path should be displayed"),
                'showtimecreated' => new external_value(PARAM_BOOL, "Config setting if courses timecreated should be displayed"),
                'showusersamount' => new external_value(PARAM_BOOL, "Setting if courses total amount of users should be displayed"),
                'showrolesandamount' => new external_value(PARAM_BOOL, "Setting if courses roles and their amount are displayed"),
            ))),
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
                    'users_count' => new external_value(PARAM_INT, 'Amount of users enrolled with this method'),
                    'password' => new external_value(PARAM_TEXT, 'Password for enrolment method'),
                ))),
                'isallowedtoupdatecourse' => new external_value(PARAM_BOOL, "Is the user allowed to update the course globally?"),
                'wwwroot' => new external_value(PARAM_TEXT, "Root URL of this moodle instance")
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
     * @return array
     * @throws \dml_exception
     * @throws \required_capability_exception
     * @throws \restricted_context_exception
     * @throws invalid_parameter_exception
     */
    public static function get_assignable_roles($courseid) {
        global $CFG, $PAGE;

        $coursecontext = \context_course::instance($courseid);
        self::validate_context($coursecontext);
        // Is the user allowed to enrol a student into this course?
        \require_capability('enrol/manual:enrol', $coursecontext);

        // Parameter validation.
        self::validate_parameters(self::get_course_info_parameters(), array('courseID' => $courseid));

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
                unset($arrayofroles[$count]);
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
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     * @throws \required_capability_exception
     * @throws \restricted_context_exception
     * @throws invalid_parameter_exception
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

    // ------------------------------------------------------------------------------------------------------------------------

    /**
     * Returns description of input parameters
     * @return external_function_parameters
     */
    public static function get_settings_parameters() {
        return new external_function_parameters(array());
    }

    /**
     * Wrapper for core function toggleCourseVisibility
     *
     * @return array: See return-function
     * @throws \dml_exception
     * @throws \restricted_context_exception
     * @throws invalid_parameter_exception
     */
    public static function get_settings() {

        $systemcontext = \context_system::instance();
        self::validate_context($systemcontext);

        $data = array (
            'tool_supporter_user_details_pagelength' => get_config('tool_supporter', 'user_details_pagelength'),
            'tool_supporter_user_details_order' => get_config('tool_supporter', 'user_details_order'),
            'tool_supporter_course_details_pagelength' => get_config('tool_supporter', 'course_details_pagelength'),
            'tool_supporter_course_details_order' => get_config('tool_supporter', 'course_details_order'),
            'tool_supporter_user_table_pagelength' => get_config('tool_supporter', 'user_table_pagelength'),
            'tool_supporter_user_table_order' => get_config('tool_supporter', 'user_table_order'),
            'tool_supporter_course_table_pagelength' => get_config('tool_supporter', 'course_table_pagelength'),
            'tool_supporter_course_table_order' => get_config('tool_supporter', 'course_table_order'),
        );
        return $data;
    }

    /**
     * Specifies return parameters
     * @return external_single_structure a course with toggled visibility
     */
    public static function get_settings_returns() {
        return new external_function_parameters(array(
            'tool_supporter_user_details_pagelength' => new external_value(PARAM_INT, 'Amount shown per page as set in settings'),
            'tool_supporter_user_details_order' => new external_value(PARAM_TEXT, 'Sorting of ID-Column, either ASC or DESC '),
            'tool_supporter_course_details_pagelength' => new external_value(PARAM_INT, 'Amount shown per page as set in settings'),
            'tool_supporter_course_details_order' => new external_value(PARAM_TEXT, 'Sorting of ID-Column, either ASC or DESC '),
            'tool_supporter_user_table_pagelength' => new external_value(PARAM_INT, 'Amount shown per page as set in settings'),
            'tool_supporter_user_table_order' => new external_value(PARAM_TEXT, 'Sorting of ID-Column, either ASC or DESC '),
            'tool_supporter_course_table_pagelength' => new external_value(PARAM_INT, 'Amount shown per page as set in settings'),
            'tool_supporter_course_table_order' => new external_value(PARAM_TEXT, 'Sorting of ID-Column, either ASC or DESC '),
        ));
    }


    // ------------------------------------------------------------------------------------------------------------------------

    /**
     * Returns description of input parameters
     * @return external_function_parameters
     */
    public static function duplicate_course_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_RAW, 'id of course you want to be duplicated and then shown')
            ));
    }

    /**
     * Wrapper for core function toggleCourseVisibility
     *
     * @return array: See return-function
     * @throws \dml_exception
     * @throws \restricted_context_exception
     * @throws invalid_parameter_exception
     */
    public static function duplicate_course($courseid) {
        // TODO
        // Check parameters.
        $params = self::validate_parameters(self::duplicate_course_parameters(), array('courseid' => $courseid));
        $courseid = $params['courseid'];

        $coursecontext = \context_course::instance($courseid);
        self::validate_context($coursecontext);
        \require_capability('moodle/course:create', \context_system::instance());

        $oldcourse = get_course($courseid);
        $oldcourse = (array)$oldcourse;

        $newcategoryid = $oldcourse["category"];
        $newvisible = $oldcourse["visible"];
        $newfullname = $oldcourse["fullname"]." - duplicated";
        $newshortname = $oldcourse["shortname"]." - duplicated".rand(0, 1000); // Add random number to avoid shortnametaken.

        $options = array(
            array ('name' => 'activities', 'value' => 1),
            array ('name' => 'blocks', 'value' => 1),
            array ('name' => 'filters', 'value' => 1),
            array ('name' => 'users', 'value' => 0),
            // array ('name' => 'enrolments', 'value' => backup::ENROL_WITHUSERS),
            array ('name' => 'role_assignments', 'value' => 0),
            array ('name' => 'comments', 'value' => 0),
            array ('name' => 'userscompletion', 'value' => 0),
            array ('name' => 'logs', 'value' => 0),
            array ('name' => 'grade_histories', 'value' => 0),
        );

        $newcourse = \core_course_external::duplicate_course($courseid, $newfullname, $newshortname,
                $newcategoryid, $newvisible, $options);

        return $newcourse;
    }

    /**
     * Specifies return parameters
     * @return external_single_structure a course with toggled visibility
     */
    public static function duplicate_course_returns() {
        return new external_single_structure(
            array(
                'id'       => new external_value(PARAM_INT, 'course id'),
                'shortname' => new external_value(PARAM_TEXT, 'short name'),
            )
        );
    }

}
