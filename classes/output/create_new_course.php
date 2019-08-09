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
 * @copyright  2019 Benedikt Schneider, Klara Saary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_supporter\output;

defined('MOODLE_INTERNAL') || die;

use renderable;
use templatable;
use renderer_base;
use stdClass;

/**
 * Class containing data for user_table
 *
 * @copyright  2019 Klara Saary, Benedikt Schneider
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class create_new_course implements renderable, templatable {

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Standard renderer for php output
     * @return stdClass Array of Moodle-categories and config-settings
     * @throws \dml_exception
     */
    public function export_for_template(renderer_base $output) {
        global $DB, $CFG;

        $categoriespath = $DB->get_records('course_categories', null, 'sortorder ASC', 'id, path');
        $categoriesnamearray = $DB->get_records_menu('course_categories', null, null, 'id, name');
        foreach ($categoriespath as $row) {
            $row->path = substr($row->path, 1); // Delete first Slash.
            $path = explode("/", $row->path);
            $row->name = '';
            foreach ($path as $entry) { // Get name for each /path/-element.
                $row->name = $row->name . " / " . format_string($categoriesnamearray[$entry]);
            }
            $categories[] = (array)$row;
        }
        $data['categories'] = $categories;

        $data['config'] = array (
            'startdate' => get_config('tool_supporter', 'new_course_startdate'),
            'enddate' => get_config('tool_supporter', 'new_course_enddate'),
        );

        return $data;
    }
}
