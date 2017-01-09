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
 * Class containing data for index page
 *
 * @package    tool_supporter
 * @copyright  2016 Benedikt Schneider, Klara Saary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_supporter\output;

//require_once($CFG->dirroot . "/user/externallib.php");
require_once($CFG->dirroot . "/admin/tool/supporter/classes/externallib.php");


use renderable;
use templatable;
use renderer_base;
use stdClass;

/**
 * Class containing data for user_table
 * Gets passed to the renderer
 *
 * @copyright  2016 Klara Saary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_table implements renderable, templatable {

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        // "Flattens" the data
        $array[] = null;
        //echo "in export_for_template user_table";
        $data = \tool_supporter\external::get_users();
      //  echo "<pre>" . print_r($data, true) . "</pre>";
        return $data;
    }
}
