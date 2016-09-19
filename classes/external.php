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
          public static function create_course_parameters($shortname, $fullname) {
            /*
            Hier müssen später noch rein: categoryid und visible
            */
            return new external_function_parameters ( array (
                // an external_description can be: external_value, external_single_structure or external_multiple structure
                'shortname' => new external_value ( PARAM_TEXT, 'The short name for the course to be created. Must not be taken.' ),
                'fullname' => new external_value ( PARAM_TEXT, 'The full name for the course to be created.' )
            ) );
          }

          /**
           * Wrap the core function create_course.
           */
          public static function create_course($shortname, $fullname) {

            /** aus Hackfest Beispiel
              global $PAGE;
              $renderer = $PAGE->get_renderer('tool_supporter');
              $page = new \tool_supporter\output\create_course();
              return $page->export_for_template($renderer);
              */



              global $DB;

              console.log("Something");
              alert("Something");

              /*
              $record = new stdClass();
              $record -> shortname = $shortname;
              $record -> shortname = $fullname;

              /*

              // Parameters validation
              $params = self::validate_parameters ( self::create_course_parameters (), array ('shortname' => $shortname, 'fullname' => $fullname) );
              if ($DB->record_exists('course', array('shortname' => $shortname))) {
                  console.log("There was an error! The shortname is already taken! Quit and display ");
                  alert("There was an error! The shortname is already taken! Quit and display ");
              }
              console.log("Shortname was not yet taken. Create the course now!");

              /*

              $DB->insert_record($table, $dataobject, $returnid=true, $bulk=false)

              $record = new stdClass();
              $record->name         = 'overview';
              $record->displayorder = '10000';
              $lastinsertid = $DB->insert_record('quiz_report', $record, false);

              */

              return 70; //Dummy; später: id des Kurses, der angelegt wurde
          }

          /**
           * Wrap the core function course_create_course
           *
           * @return external_description
           */
          public static function create_course_returns() {
              return new external_value(PARAM_INT, 'The course id that was created');
          }
}
