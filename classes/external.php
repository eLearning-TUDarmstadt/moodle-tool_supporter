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
require_once("$CFG->libdir/adminlib.php");
require_once("$CFG->libdir/coursecatlib.php");

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

              $catcontext = \context_coursecat::instance($categoryid);
              self::validate_context($catcontext);
              \require_capability('moodle/course:create', $catcontext);

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
              if (strpos($params['shortname'], 'WiSe') !== FALSE) {
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

              $created_course = create_course($data);

              $return_data = array(
                'id' => $created_course->id,
                'category' => $created_course->category,
                'fullname' => $created_course->fullname,
                'shortname' => $created_course->shortname,
                'startdate' => $created_course->startdate,
                'visible' => $created_course->visible,
                'timecreated' => $created_course->timecreated,
                'timemodified' => $created_course->timemodified
              );

              return $return_data;
          }

          /**
           * Wrap the core function course_create_new_course
           *
           * @return external_description
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

           // --------------------------------------------------------------------------------------------------------------------------------------

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
            */
           public static function enrol_user_into_course($userid, $courseid, $roleid) {
             global $DB;
             global $CFG;
             require_once("$CFG->dirroot/enrol/manual/externallib.php");

             $context = \context_course::instance($courseid);
             self::validate_context($context);
             // Check that the user has the permission to manual enrol.
             \require_capability('moodle/enrol/manual:enrol', $context);

             $params = array(
                     'userid' => $userid,
                     'courseid' => $courseid,
                     'roleid' => $roleid
                 );

             //Parameters validation
             $params = self::validate_parameters(self::enrol_user_into_course_parameters(), $params);

             $enrolment = array('courseid' => $courseid, 'userid' => $userid, 'roleid' => $roleid);
             $enrolments[] = $enrolment;
             \enrol_manual_external::enrol_users($enrolments);

             return true;
           }

           /**
            * Specifies the return values
            *
            * @return returns true or false
            */

           public static function enrol_user_into_course_returns() {
             return new external_value (PARAM_BOOL, 'true if user was enrolled');
             }

           // --------------------------------------------------------------------------------------------------------------------------------------

           public static function get_user_information_parameters() {
             return new external_function_parameters(
               array(
                 'userid' => new external_value ( PARAM_INT, 'The id of the user' )
               ));
           }

           /**
            * Wrap the core function get_user_information.
            */
           public static function get_user_information($userid) {
             global $DB;

             $context = \context_system::instance();
             self::validate_context($context);
             \require_capability('moodle/user:viewdetails', $context);

             //Parameters validation
             $params = self::validate_parameters(self::get_user_information_parameters (), array('userid'=>$userid));

             $userinformation = user_get_users_by_id(array('userid'=>$userid));
             // important output: id, username, firstname, lastname, email, timecreated, timemodified, lang [de, en], auth [manual]

             $userinformationarray = [];
             foreach ($userinformation as $info) {
               // cast as an array
               $info->timecreated = date(DATE_RFC850, $info->timecreated); //Example: Monday, 15-Aug-05 15:52:01 UTC
               $info->timemodified = date(DATE_RFC850, $info->timemodified);
               $userinformationarray[] = (array)$info;
             }
             $userinformationarray = $userinformationarray[0]; //we only retrieved one user

             $usercourses = enrol_get_users_courses($userid); // important Output: id, category, shortname, fullname, startdate, visible

             //Get an array of categories [id]=>[name]
             $categories = $DB->get_records_menu('course_categories', null, null, 'id, name');

             $usercoursesarray = [];
             $data['uniqueparentcategory'] = [];
             $data['uniquecategoryname'] = [];
             foreach ($usercourses as $course) {
               //Get the semester the course is in (parent of category)
               $course->categoryname = $categories[$course->category]; //Department, Fachbereich
               if (!in_array($course->categoryname, $data['uniquecategoryname'])) {
                 array_push ($data['uniquecategoryname'], $course->categoryname);
               }

               $categorypath = $DB->get_record('course_categories', array('id'=>$course->category), 'path');
               $patharray = explode("/", $categorypath->path);
               $parentcategory = array_reverse($patharray)[1]; //Semester
               $course->parentcategory = $categories[$parentcategory];

               if (!in_array($course->parentcategory, $data['uniqueparentcategory'])) {
                 array_push ($data['uniqueparentcategory'], $course->parentcategory);
               }

               //Get the used Roles the user is enrolled as (teacher, student, ...)
               $context = \context_course::instance($course->id);
               $usedroles = get_user_roles($context, $userid);
               foreach ($usedroles as $role) {
                 $course->roles[] = $role->shortname;
               }
               $usercoursesarray[] = (array)$course; //cast it as an array
             }

             $data['userscourses'] = $usercoursesarray;
             $data['userinformation'] = $userinformationarray;

             global $CFG, $USER;
             if (\has_capability('moodle/user:loginas', $context) ) {
               $link = $CFG->wwwroot."/course/loginas.php?id=1&user=".$data['userinformation']['id']."&sesskey=".$USER->sesskey;
               $data['loginaslink'] = (array)$link;
             }

             $link = $CFG->wwwroot."/user/profile.php?id=".$data['userinformation']['id'];
             $data['profilelink'] = (array)$link;

             $link = $CFG->wwwroot."/user/editadvanced.php?id=".$data['userinformation']['id'];
             $data['edituserlink'] = (array)$link;

             //print_r($data);

             return array($data);
           }

           /**
            * Specifies the return values
            *
            * @return returns the user's courses and information
            */

           public static function get_user_information_returns() {
             return
              new external_multiple_structure (new external_single_structure (array (
                  'userinformation' => new external_single_structure ( array (
                      'id' => new external_value (PARAM_INT, 'id of the user'),
                      'username' => new external_value (PARAM_TEXT, 'username of the user'),
                      'firstname' => new external_value (PARAM_TEXT, 'firstname of the user'),
                      'lastname' => new external_value (PARAM_TEXT, 'lastname of the user'),
                      'email' => new external_value (PARAM_TEXT, 'email of the user'),
                      'timecreated' => new external_value (PARAM_TEXT, 'timecreated of the user'),
                      'timemodified' => new external_value (PARAM_TEXT, 'timemodified of the user'),
                      'lang' => new external_value (PARAM_TEXT, 'lang of the user'),
                      'auth' => new external_value (PARAM_TEXT, 'auth of the user')
                    )),
                    'userscourses' => new external_multiple_structure (new external_single_structure (array (
                          'id' => new external_value (PARAM_INT, 'id of course'),
                          'category' => new external_value (PARAM_INT, 'category id of the course'),
                          'shortname' => new external_value (PARAM_TEXT, 'short name of the course'),
                          'fullname' => new external_value (PARAM_TEXT, 'long name of the course'),
                          'startdate' => new external_value (PARAM_INT, 'starting date of the course'),
                          'visible' => new external_value (PARAM_BOOL, 'visible of course'),
                          'parentcategory' => new external_value (PARAM_TEXT, 'the parent category name of the course'),
                          'categoryname' => new external_value (PARAM_TEXT, 'the direkt name of the course category'),
                            'roles' => new external_multiple_structure (new external_value(PARAM_TEXT, 'array with roles for each course'))
                          //'idnumber' => new external_value (PARAM_RAW, 'idnumber of the course'),
                          //'sortorder' => new external_value (PARAM_INT, 'sortorder of the course'),
                          // new external_value ('id', PARAM_INT, 'category id of the course'), // für external_single_structure
                          //'defaultgroupingid' => new external_value (PARAM_INT, ' the defaultgroupingid of the course'),
                          //'groupmode' => new external_value (PARAM_INT, 'the groupmode of the course'),
                          //'groupmodeforce' => new external_value (PARAM_INT, 'groupmodeforce of course'),
                          //'ctxid' => new external_value (PARAM_INT, 'the ctxid of the course'),
                          //'ctxpath' => new external_value (PARAM_RAW, 'the ctxpath of the course'),
                          //'ctxdepth' => new external_value (PARAM_INT, 'the ctxdepth of the course'),
                          //'ctxinstance' => new external_value (PARAM_INT, 'the ctxinstance of the course'),
                          //'ctxlevel' => new external_value (PARAM_INT, 'the ctxlevel of the course')
                    ))),
                    'loginaslink' => new external_single_structure (array(new external_value(PARAM_TEXT, 'The link to login as the user'))),
                    'profilelink' => new external_single_structure (array(new external_value(PARAM_TEXT, 'The link to the users profile page'))),
                    'edituserlink' => new external_single_structure (array(new external_value(PARAM_TEXT, 'The link to edit the user'))),
                    'uniquecategoryname' => new external_multiple_structure (new external_value(PARAM_TEXT, 'array with unique category names')),
                    'uniqueparentcategory' => new external_multiple_structure (new external_value(PARAM_TEXT, 'array with unique parent categories'))
                  )));
             }

           // --------------------------------------------------------------------------------------------------------------------------------------

           /**
            * Returns description of method parameters
            * @return external_function_parameters
            */
           public static function get_users_parameters() {
           	return new external_function_parameters(
           			array('search_input' => new external_value(PARAM_RAW, 'if you just looking for specified users', VALUE_DEFAULT, '%')
         			));
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
           					)));
           }

           public static function get_users(){
           	global $DB;

            $systemcontext = \context_system::instance();
            self::validate_context($systemcontext);
            \require_capability('moodle/site:viewparticipants', $systemcontext);

           	$recordset = $DB->get_recordset('user', null, null, 'id, username, firstname, lastname, email' );
           	foreach ($recordset as $record) {
           		$users[] = (array)$record;
           	}
           	$recordset->close();
           	$data['users'] = $users;
           	return $data;

           }

          // --------------------------------------------------------------------------------------------------------------------------------------

           public static function get_courses_parameters(){
           	return new external_function_parameters(
           			array( //no parameters required
         			));
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

           	$context = \context_system::instance();
           	self::validate_context($context);
           	//Is the closest to the needed capability. Is used in /course/management.php
           	\require_capability('tool/category:manage', $context);

           	$select = 'SELECT c.id, c.fullname, c.visible, cat.name AS fb, (SELECT name FROM {course_categories} WHERE id = cat.parent) AS semester FROM {course} c, {course_categories} cat WHERE c.category = cat.id';
           	$rs = $DB->get_recordset_sql($select);
           	foreach ($rs as $record) {
           		$courses[] = (array)$record;
           	}
           	$rs->close();
           	$data['courses'] = $courses;
           	return $data;
           }

          // --------------------------------------------------------------------------------------------------------------------------------------

           public static function get_course_info_parameters(){
           	return new external_function_parameters(
           			array(
           					'courseID' => new external_value(PARAM_RAW, 'id of course you want to show')
           			)
           			);

           }

           public static function get_course_info_returns(){ //data_returns.txt anschauen und parameter anpassen
           	return
           			new external_single_structure(
           					array(
           							'courseDetails'=> new external_single_structure(
           											array(
           													'id' => new external_value(PARAM_INT, 'id of course'),
                                    'shortname' => new external_value(PARAM_RAW, 'shortname of course'),
                                    'fullname' => new external_value(PARAM_RAW, 'course name'),
                                    'visible' => new external_value(PARAM_BOOL, 'Is the course visible?'),
						           							'fb' => new external_value(PARAM_RAW,'course category'),
                                    'semester' => new external_value(PARAM_RAW, 'parent category'),
						           						//	'path' => new external_value(PARAM_RAW, 'path of course'),
						           							'enrolledUsers' => new external_value(PARAM_INT, 'number of users, without teachers')
           											)
           									),
                        'rolesincourse' => new external_multiple_structure (new external_value(PARAM_TEXT, 'array with roles used in course')),
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
           											array(
						                                  'id' => new external_value(PARAM_INT, 'id of user'),
						                                  'username' => new external_value(PARAM_RAW, 'name of user'),
						                                  'firstname' => new external_value(PARAM_RAW, 'firstname of user'),
						                                  'lastname' => new external_value(PARAM_RAW, 'lastname of user'),
                                              'roles' => new external_multiple_structure (new external_value(PARAM_TEXT, 'array with roles for each user'))
           										)
           									)
                       				 ),
			                        'activities' => new external_multiple_structure(
			                            new external_single_structure(
			                                array(
					                                  'section' => new external_value(PARAM_RAW, 'Name of section, in which the activity appears'),
					                                  'activity' => new external_value(PARAM_RAW, 'kind of activity'),
					                                  'name' => new external_value(PARAM_RAW, 'Name of this activity'),
					                                  'visible' => new external_value(PARAM_INT, 'Is the activity visible? 1: yes, 0: no')
			                            	)
			                            )
			                        ),
                              'links' => new external_single_structure(array(
                                'settingslink' => new external_value(PARAM_RAW, 'link to the settings of the course'),
                                'deletelink' => new external_value(PARAM_RAW, 'link to delete the course, additional affirmation needed afterwards', optional),
                                'courselink' => new external_value(PARAM_RAW, 'link to the course')
                              ))

			           		)
			          )
              ;
           }

           public static function get_course_info($courseID){
           	global $DB, $CFG, $PAGE;
           	//check parameters
           	$params = self::validate_parameters(self::get_course_info_parameters(), array('courseID'=>$courseID));
            $courseID = $params['courseID'];

           	$coursecontext = \context_course::instance($courseID);
            self::validate_context($coursecontext);
            // is the user allowed to change course_settings
           	\require_capability('moodle/course:update', $coursecontext);

            //Get information about the course
           	$select = "SELECT c.id, c.shortname, c.fullname, c.visible, cat.name AS fb, (SELECT name FROM {course_categories} WHERE id = cat.parent) AS semester FROM {course} c, {course_categories} cat WHERE c.category = cat.id AND c.id = ".$courseID;
           	$courseDetails = $DB->get_record_sql($select);
           	$courseDetails = (array)$courseDetails;

            //How many students are enrolled in the course?
            $courseDetails['enrolledUsers'] = \count_enrolled_users($coursecontext, $withcapability = '', $groupid = '0');

            //Which roles are used and how many users have this role?
            $roleList = get_roles_used_in_context($coursecontext);
            $roles = array();
           	//$roleList = \get_all_roles($coursecontext); // array('moodle/legacy:student', 'moodle/legacy:teacher', 'moodle/legacy:editingteacher', 'moodle/legacy:coursecreator');
           	$count= \count_role_users([1,2,3,4,5,6,7], $coursecontext);
            $rolesincourse = [];
           	foreach ($roleList as $r) {
           		if($r->coursealias != NULL)
           			$roleName = $r->coursealias;
           			else
           				$roleName =$r->shortname;
           			$roleNumber = \count_role_users($r->id,$coursecontext);
           				//$roleNumber = count_enrolled_users($coursecontext, $withcapability = $role, $groupid = 0);
           			if($roleNumber != 0)
           					$roles[] = ['roleName' => $roleName, 'roleNumber' => $roleNumber];
                $rolesincourse[] = $roleName;
           	}
            //Get userinformation about users in course
           	$users_raw = \get_enrolled_users($coursecontext, $withcapability = '', $groupid = 0, $userfields = 'u.id,u.username,u.firstname, u.lastname', $orderby = '', $limitfrom = 0, $limitnum = 0);
           	$users = array();
           	foreach($users_raw as $u){
              $u = (array)$u;
              //Find user specific roles
              $usedroles = get_user_roles($coursecontext, $u['id']);
              $userRoles = [];
              foreach ($usedroles as $role) {
                $userRoles[] = $role->shortname;
              }
              $u['roles'] = $userRoles;
           		$users[] = $u;
           	}

            //Activities in course
           	$activities = array();
           	$modules =  \get_array_of_activities($courseID);
           	foreach($modules as $mo){
           		$section = \get_section_name($courseID, $mo->section);
           		$activity = ['section' =>$section, 'activity'=>$mo->mod,'name'=>$mo->name, 'visible'=>$mo->visible];
           		$activities[] = $activity;
           	}

            global $CFG, $USER;

            $settingslink = $CFG->wwwroot."/course/edit.php?id=".$courseID;
            if (\has_capability('moodle/course:delete', $coursecontext) ) {
                $deletelink = $CFG->wwwroot."/course/delete.php?id=".$courseID;
            }
            $courselink = $CFG->wwwroot."/course/view.php?id=".$courseID;

            $links = array(
              'settingslink' => $settingslink,
              'deletelink' => $deletelink,
              'courselink' => $courselink
            );
           	$data = array(
              'courseDetails' => (array)$courseDetails,
              'rolesincourse' => (array)$rolesincourse,
              'roles' => (array)$roles,
              'users' => (array)$users,
              'activities' => (array)$activities,
              'links' => $links
            );

           	//print_r($data);
           	return (array)$data;
           }

           // --------------------------------------------------------------------------------------------------------------------------------------

           /**
            * Returns description of method parameters
            * @return external_function_parameters
            */
            public static function get_assignable_roles_parameters(){
             return new external_function_parameters(array(
                   'courseID' => new external_value(PARAM_RAW, 'id of course you want to show')
                 ));
            }

           public static function get_assignable_roles($courseID){
            global $CFG, $PAGE;

            $coursecontext = \context_course::instance($courseID);
            self::validate_context($coursecontext);
            // is the user allowed to enrol a student into this course
            \require_capability('moodle/enrol/manual:enrol', $coursecontext);

            //Parameter validation
            $params = self::validate_parameters(self::get_course_info_parameters(), array('courseID'=>$courseID));

            // Get assignable roles in the course
            require_once $CFG->dirroot.'/enrol/locallib.php';
            $course = get_course($courseID);
            $manager = new \course_enrolment_manager($PAGE, $course);
            $usedRoles = $manager->get_assignable_roles();

            $count = 0;
            foreach ($usedRoles as $roleid => $rolename) {
              $arrayofRoles[$count]['id'] = $roleid;
              $arrayofRoles[$count]['name'] = $rolename;
              $count++;
            }
            //To (sometimes) make the least privileged role the default (first)
            $arrayofRoles = array_reverse($arrayofRoles);

            $data = array(
             'assignableRoles' => (array)$arrayofRoles
           );

           print_r($data);
           return $data;
           }

           public static function get_assignable_roles_returns() {
              new external_single_structure(
                  array(
                    'assignableRoles' => new external_multiple_structure( new external_single_structure( array(
                      'id' => new external_value(PARAM_INT, 'id of the role'),
                      'name' => new external_value(PARAM_RAW, 'Name of the role')
                      )))
                  ));
           }

           // --------------------------------------------------------------------------------------------------------------------------------------

          public static function toggle_course_visibility_parameters(){
            return new external_function_parameters(array(
                  'courseID' => new external_value(PARAM_INT, 'id of course')
                ));
          }

          public static function toggle_course_visibility($courseID){

            $coursecontext = \context_course::instance($courseID);
            self::validate_context($coursecontext);
            // is the user allowed to change course_settings
            \require_capability('moodle/course:update', $coursecontext);

             // checking parameters
             self::validate_parameters(self::toggle_course_visibility_parameters(), array('courseID'=>$courseID));
             // security checks
             $coursecontext = \context_course::instance($courseID);
             self::validate_context($coursecontext);
             //Is the user allowed to change the visibility?
             \require_capability('moodle/course:visibility', $coursecontext);

             $course = self::get_course_info($courseID);
             //2nd param is the desired visibility value
             course_change_visibility($courseID, !($course['courseDetails']['visible']));
             $course['courseDetails']['visible'] = !$course['courseDetails']['visible'];

             return $course;
          }

          public static function toggle_course_visibility_returns(){
            return self::get_course_info_returns();
          }
}
