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

 "Geklauter Webservice"
 *
 * @package    local_hackfest
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_hackfest;

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
 * @copyright  2015 Damyon Wiese
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
     */

     // By default this is turned off - security issues
    public static function get_site_info_is_allowed_from_ajax() {
        return true;
    }

    /**
     * Wrap the core function get_site_info.
     */
    public static function get_site_info($serviceshortnames = array()) {
        global $PAGE;
        $renderer = $PAGE->get_renderer('local_hackfest');
        $page = new \local_hackfest\output\index_page();
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
}
