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
 * This is the external API for this plugin.
 *
 * @package    tool_supporter
 * @copyright  2016 Benedikt Schneider, Klara Saary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_supporter;

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->dirroot/webservice/externallib.php");
require_once("$CFG->dirroot/course/lib.php");
require_once("$CFG->dirroot/user/lib.php");
require_once($CFG->libdir.'/adminlib.php');

use external_api;
use external_function_parameters;
use external_value;
use external_format_value;
use external_single_structure;
use external_multiple_structure;
use invalid_parameter_exception;

/**
 * This is the external API for this plugin.
 *
 * @copyright  2016 Benedikt Schneider
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {

    /**
     * Wrap the core function get_site_info.
     *
     * @return external_function_parameters
     */
    public static function get_site_info_parameters() {
        return \core_webservice_external::get_site_info_parameters();
    }

    /**
     * Expose to AJAX
     * @return boolean
     *
     * By default this is turned off - security issues
     */
    public static function get_site_info_is_allowed_from_ajax() {
        return true;
    }

    /**
     * Wrap the core function get_site_info.
     */
    public static function get_site_info($serviceshortnames = array()) {
        global $PAGE;
        $renderer = $PAGE->get_renderer('tool_supporter');
        $page = new \tool_supporter\output\index_page();
        return $page->export_for_template($renderer);
    }

    /**
     * Wrap the core function get_site_info.
     *
     * @return external_description
     */
    public static function get_site_info_returns() {
        $result = \core_webservice_external::get_site_info_returns();
        $result->keys['currenttime'] = new external_value(PARAM_RAW, 'the current time');
        return $result;
    }

          /**
           * Create a coruse.
           *
           * @return external_function_parameters
           */
          public static function create_new_course_parameters() {
            return new external_function_parameters (
              array (
                // an external_description can be: external_value, external_single_structure or external_multiple structure
                'shortname' => new external_value ( PARAM_TEXT, 'The short name of the course to be created' ),
                'fullname' => new external_value ( PARAM_TEXT, 'The full name of the course to be created' ),
                'visible' => new external_value ( PARAM_BOOL, 'Toggles visibility of course' ),
                'categoryid' => new external_value ( PARAM_INT, 'ID of category the course should be created in' )
              ));
            }

          /**
           * Wrap the core function create_new_course.
           */
            public static function create_new_course($shortname, $fullname, $visible, $categoryid) {

              global $DB, $CFG;

              $array = array (
              				'shortname' => $shortname,
              				'fullname' => $fullname,
                      'visible' => $visible,
                      'categoryid' => $categoryid
              		);

              //Parameters validation
		          $params = self::validate_parameters ( self::create_new_course_parameters (), $array );

              //$transaction = $DB->start_delegated_transaction(); //If an exception is thrown in the below code, all DB queries in this code will be rollback.

              $data = new \stdClass();
              $data->shortname = $params ['shortname'];
              $data->fullname = $params ['fullname'];
              $data->category = $params ['categoryid'];
              $data->visible = $params ['visible'];

              if (trim($params['shortname']) == '') {
                 throw new invalid_parameter_exception('Invalid short name');
                 //throw new moodle_exception('shortnametaken', '', '', $data->shortname);
              }
              if (trim($params['fullname']) == '') {
                 throw new invalid_parameter_exception('Invalid full name');
              }
              if ($DB->record_exists('course', array('shortname' => $data->shortname))) {
                  throw new invalid_parameter_exception('shortnametaken already taken');
              }

              //Set Start date to 1.4. or 1.10.
              if (strpos(shortname, 'WiSe') !== FALSE) {
                 $array_after_semester = explode('WiSe', shortname);
                 $year = substr($array_after_semester[1], 1, 4);
                 $data->startdate = mktime(24, 0, 0, 10, 1, $year); //hour, minute, second, month, day, year
                }
                else if (strpos($shortname, 'SoSe') !== FALSE) {
                   $array_after_semester = explode('SoSe', $shortname);
                   $year = substr($array_after_semester[1], 1, 4);
                   $data->startdate = mktime(24, 0, 0, 4, 1, $year);
                  }
                  else {
                    $data->startdate = time();
                  }

              //$transaction->allow_commit(); //DB wird commited

              //var test = core_enrol_get_users_courses(5);
              //print_r(test);

              $created_course = create_course($data);

              return array (
                'id' => $created_course->id
              );

              /*
              $record = new stdClass();
              $record -> shortname = $shortname;
              $record -> shortname = $fullname;

              /*

              // Parameters validation
              $params = self::validate_parameters ( self::create_new_course_parameters (), array ('shortname' => $shortname, 'fullname' => $fullname) );
              if ($DB->record_exists('course', array('shortname' => $shortname))) {
                  console.log("There was an error! The shortname is already taken! Quit and display ");
                  alert("There was an error! The shortname is already taken! Quit and display ");
              }
              console.log("Shortname was not yet taken. Create the course now!");

              /*

              $contextmodule = context_module::instance($cm->id);

              // Context validation
              $cmid = self::get_cmid_by_instance ( $params ['supporterinstance'] );
              $context = context_module::instance ( $cmid );
              self::validate_context ( $context );



              // Welche Rechte muss man haben, damit man das machen darf? Wie muss der Kontext aussehen, "damit man Admin ist"?
              /*
              $context = context_course::instance($course->courseid);
              self::validate_context($context);
              require_capability('moodle/course:create', $context);
              */
              /*

              $DB->insert_record($table, $dataobject, $returnid=true, $bulk=false)

              $record = new stdClass();
              $record->name         = 'overview';
              $record->displayorder = '10000';
              $lastinsertid = $DB->insert_record('quiz_report', $record, false);

              /*** aus Hackfest Beispiel
                global $PAGE;
                $renderer = $PAGE->get_renderer('tool_supporter');
                $page = new \tool_supporter\output\create_new_course();
                return $page->export_for_template($renderer);
                */

          }

          /**
           * Wrap the core function course_create_new_course
           *
           * @return external_description
           */
           public static function create_new_course_returns() {
             return new external_single_structure (
              array (
                'id' => new external_value ( PARAM_RAW, 'The id of the newly created course' )
             ));
           }

           // --------------------------------------------------------------------------------------------------------------------------------------

           public static function get_user_information_parameters() {
             return new external_function_parameters(
               array(
                 //'userid' => new external_value ( PARAM_INT, 'The id of the user' )
                 'userid' => new external_value(PARAM_RAW, 'The id of the user')
               ));
           }

           /**
            * Wrap the core function get_site_info.
            */
           public static function get_user_information($userid) {
             global $DB;

             //Parameters validation
             $params = self::validate_parameters(self::get_user_information_parameters (), array('userid'=>$userid));

             //echo "user iddd: " . $userid;
             $user_courses = enrol_get_users_courses($userid);

             // --------------------------- HIER WIRDS IN EIN ARRAY UMGEWANDELT
             foreach ($user_courses as $course) {
               $user_courses_array[] = (array)$course;
             }



             //$test = enrol_get_all_users_courses($userid);
             // core_enrol_
             //print_r($user_courses);

             $user_information = user_get_users_by_id(array('userid'=>$userid));
             //print_r($user_information); /// wichtiger output: id, username, firstname, lastname, email, timecreated, timemodified, lang [de, en], auth [manual]
             //echo "test3";



             $data = array(
               'user_courses' => $user_courses_array,
               //'user_information' => $user_information
             );

              echo "-----6------";
              print_r ($data);

             return $data;
           }

           /**
            * Specifies the return thingies
            *
            * @return returns the user's courses and information
            */
           public static function get_user_information_returns() {
             return
             new external_multiple_structure(
                 //new external_multiple_structure(
                   new external_single_structure(
                     array(
                       'id' => new external_value (PARAM_INT, 'id of course'),
                       'category' => new external_value (PARAM_INT, 'category id of the course'),
                       'sortorder' => new external_value (PARAM_INT, 'sortorder of the course'),
                       'shortname' => new external_value (PARAM_TEXT, 'short name of the course'),
                       'fullname' => new external_value (PARAM_TEXT, 'long name of the course'),
                       'idnumber' => new external_value (PARAM_INT, 'idnumber of the course'),
                       'startdate' => new external_value (PARAM_INT, 'starting date of the course'),
                       'visible' => new external_value (PARAM_BOOL, 'visible of course'),
                       'defaultgroupingid' => new external_value (PARAM_INT, ' the defaultgroupingid of the course'),
                       'groupmode' => new external_value (PARAM_INT, 'the groupmode of the course'),
                       'groupmodeforce' => new external_value (PARAM_INT, 'groupmodeforce of course'),
                       'ctxid' => new external_value (PARAM_INT, 'the ctxid of the course'),
                       'ctxpath' => new external_value (PARAM_RAW, 'the ctxpath of the course'),
                       'ctxdepth' => new external_value (PARAM_INT, 'the ctxdepth of the course'),
                       'ctxinstance' => new external_value (PARAM_INT, 'the ctxinstance of the course'),
                       'ctxlevel' => new external_value (PARAM_INT, 'the ctxlevel of the course')
                     )
                   , 'one of users courses')
                 //, 'all of users courses')
                 // , user_information => .....
             )
             ;
           }

           /************************************
           public static function get_course_info_returns(){ //data_returns.txt anschauen und parameter anpassen
             return new external_multiple_structure(
                 new external_single_structure(
                     array(
                         'courseDetails'=> new external_multiple_structure(
                             new external_single_structure(
                                 array(
                                     'id' => new external_value(PARAM_INT, 'id of course'),
                                     'semester' => new external_value(PARAM_RAW, 'parent category'),
                                     'fb' => new external_value(PARAM_RAW,'course category'),
                                     'shortname' => new external_value(PARAM_RAW, 'shortname of course'),
                                     'fullname' => new external_value(PARAM_RAW, 'course name'),
                                     'visible' => new external_value(PARAM_RAW, 'Is the course visible?'),
                                     'path' => new external_value(PARAM_RAW, 'path of course'),
                                     'enrolledUsers' => new external_value(PARAM_RAW, 'number of users, without teachers')
                                 )
                                 )
                             ),
                         'roles' => new external_multiple_structure(
                             new external_single_structure(
                                 array(
                                     'roleName' => new external_value(PARAM_RAW, 'name of one role in course'),
                                     'roleNumber' => new external_value(PARAM_INT, 'number of participants with role = roName')
                                 )
                                 )
                             ),
                         'users' => new external_multiple_structure(
                             new external_single_structure(
                                 array( )
                                 )
                             )
                     )
                     )
                 );
           }
           */

           /*
            * Expose to AJAX
            * @return boolean
            *
            * By default this is turned off - security issues
            *
           public static function get_site_info_is_allowed_from_ajax() {
               return true;
           }
           */

           /**
            * Returns description of method parameters
            * @return external_function_parameters
            */
           public static function get_users_parameters() {
           	return new external_function_parameters(
           			array(
           					'search_input' => new external_value(PARAM_RAW, 'if you just looking for specified users', VALUE_DEFAULT, '%')
           			)
           			);
           }

           public static function get_users_returns() {
           	return new external_multiple_structure(
           			new external_single_structure(
           					array(
           							'id' => new external_value(PARAM_INT, 'id of user'),
           							'username' => new external_value(PARAM_RAW, 'username of user'),
           							'firstname' => new external_value(PARAM_RAW,'firstname of user'),
           							'lastname' => new external_value(PARAM_RAW, 'lastname of user'),
           							'email' => new external_value(PARAM_RAW, 'email adress of user')
           					)
           					)
           			);
           }

           public static function get_users(){
           	global $DB;

           	// now security checks
           	$context = \context_system::instance();
           	self::validate_context($context);
           	//Is the user allowes to use this web service?
           	require_capability('tool/supporter:get_users', $context);

           	$rs = $DB->get_recordset('user', null, null, 'id, username, firstname, lastname, email' );
           	foreach ($rs as $record) {
           		$users[] = (array)$record;
           	}
           	$rs->close();
           	$data['users'] = $users;
           	return $data;

           }

           public static function get_courses_parameters(){
           	return new external_function_parameters(
           			array( //no parameters required
           			)
           			);
           }

           public static function get_courses_returns() {
           	return new external_multiple_structure(
           			new external_single_structure(
           					array(
           							'id' => new external_value(PARAM_INT, 'id of course'),
           							'semester' => new external_value(PARAM_RAW, 'parent category'),
           							'FB' => new external_value(PARAM_RAW,'course category'),
           							'fullname' => new external_value(PARAM_RAW, 'course name'),
           							'visible' => new external_value(PARAM_RAW, 'Is the course visible?')
           					)
           					)
           			);
           }

           public static function get_courses(){
           	global $DB;

           	// now security checks
           	$context = \context_system::instance();
           	self::validate_context($context);
           	//Is the user allowes to use this web service?
           	require_capability('tool/supporter:get_users', $context);

           	$select = 'SELECT c.id, c.fullname, c.visible, cat.name AS fb, (SELECT name FROM {course_categories} WHERE id = cat.parent) AS semester FROM {course} c, {course_categories} cat WHERE c.category = cat.id';
           	$rs = $DB->get_recordset_sql($select);
           	foreach ($rs as $record) {
           		$courses[] = (array)$record;
           	}
           	$rs->close();
           	$data['courses'] = $courses;
           	return $data;
           }

           public static function get_course_info_parameters(){
           	return new external_function_parameters(
           			array(
           					'courseID' => new external_value(PARAM_RAW, 'id of course you want to show')
           			)
           			);

           }

           public static function get_course_info_returns(){ //data_returns.txt anschauen und parameter anpassen
           	return new external_multiple_structure(
           			new external_single_structure(
           					array(
           							'courseDetails'=> new external_multiple_structure(
           									new external_single_structure(
           											array(
           													'id' => new external_value(PARAM_INT, 'id of course'),
           													'semester' => new external_value(PARAM_RAW, 'parent category'),
           													'fb' => new external_value(PARAM_RAW,'course category'),
           													'shortname' => new external_value(PARAM_RAW, 'shortname of course'),
           													'fullname' => new external_value(PARAM_RAW, 'course name'),
           													'visible' => new external_value(PARAM_RAW, 'Is the course visible?'),
           													'path' => new external_value(PARAM_RAW, 'path of course'),
           													'enrolledUsers' => new external_value(PARAM_RAW, 'number of users, without teachers')
           											)
           											)
           									),
           							'roles' => new external_multiple_structure(
           									new external_single_structure(
           											array(
           													'roleName' => new external_value(PARAM_RAW, 'name of one role in course'),
           													'roleNumber' => new external_value(PARAM_INT, 'number of participants with role = roName')
           											)
           											)
           									),
           							'users' => new external_multiple_structure(
           									new external_single_structure(
           											array( )
           											)
           									)
           					)
           					)
           			);
           }

           public static function get_course_info($courseID){
           	global $DB;
           	//check parameters

           	$params = self::validate_parameters(self::get_course_info_parameters(), array('courseID'=>$courseID));
           	// now security checks
           	$coursecontext = \context_course::instance($params['courseID']);
           	$courseID = $params['courseID'];
           	self::validate_context($coursecontext);
           	//Is the user allowes to use this web service?
           	//require_capability('moodle/site:viewparticipants', $context); // is the user normaly allowed to see all participants of the course
           	require_capability('tool/supporter:get_course_info', $coursecontext); // is the user coursecreator, manager, teacher, editingteacher

           	$select = "SELECT c.id, c.shortname, c.fullname, c.visible, cat.name AS fb, (SELECT name FROM {course_categories} WHERE id = cat.parent) AS semester FROM {course} c, {course_categories} cat WHERE c.category = cat.id AND c.id = ".$courseID;
           	$courseDetails = $DB->get_record_sql($select);
           	$courseDetails = (array)$courseDetails;
           	$courseDetails['enrolledUsers'] = \count_enrolled_users($coursecontext, $withcapability = '', $groupid = '0');
           	$roles = array();
           	$roleList = \get_all_roles($coursecontext); // array('moodle/legacy:student', 'moodle/legacy:teacher', 'moodle/legacy:editingteacher', 'moodle/legacy:coursecreator');
           	$count= \count_role_users([1,2,3,4,5,6,7], $coursecontext);
           	foreach ($roleList as $r) {
           		if($r->coursealias != NULL)
           			$roleName = $r->coursealias;
           			else
           				$roleName =$r->shortname;
           				$roleNumber = \count_role_users($r->id,$coursecontext);
           				//$roleNumber = count_enrolled_users($coursecontext, $withcapability = $role, $groupid = 0);
           				if($roleNumber != 0)
           					$roles[] = ['roleName' => $roleName, 'roleNumber' => $roleNumber];
           	}
           	$users_raw = \get_enrolled_users($coursecontext, $withcapability = '', $groupid = 0, $userfields = 'u.id,u.username,u.firstname, u.lastname', $orderby = '', $limitfrom = 0, $limitnum = 0);
           	$users = array();
           	foreach($users_raw as $u){
           		$users[] = (array)$u;
           	}
           	$activities = array();
           	$modules =  \get_array_of_activities($courseID);
           	foreach($modules as $mo){
           		$section = \get_section_name($courseID, $mo->section);
           		$activity = ['section' =>$section, 'activity'=>$mo->mod,'name'=>$mo->name, 'visible'=>$mo->visible];
           		$activities[] = $activity;
           	}
           	$data = array('courseDetails' => $courseDetails, 'roles' => $roles, 'users' => $users, 'activities' => $activities);

           	print_r($data);

           	return $data;
           }
}
