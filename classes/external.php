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

              //$created_course = create_course($data);

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

              /** aus Hackfest Beispiel
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
                'id' => new external_value ( PARAM_INT, 'The id of the newly created course' )
             ));
           }

           public static function get_users_courses_parameters() {
             return new external_function_parameters (
               array (
                 'user_id' => new external_value ( PARAM_INT, 'The id of the user' )
               ));
           }

           /**
            * Wrap the core function get_site_info.
            */
           public static function get_users_courses($user_id) {

             echo "test";

             //Parameters validation
             $params = self::validate_parameters ( self::create_new_course_parameters (), $array );

             echo "something";

             echo "user id: " . $user_id;
             //var test = core_enrol_get_users_courses($user_id);
             //print_r(test);

             return array (
               'id' => 1
             );
           }

           /**
            * Wrap the core function get_site_info.
            *
            * @return external_description
            */
           public static function get_users_courses_returns() {
             return new external_single_structure (
              array (
                'id' => new external_value ( PARAM_INT, 'Has to be changed later' )
             ));
           }

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
}
