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
 * Class containing data for course_table
 *
 * @package tool_supporter
 * @copyright  2019 Klara Saary, Benedikt Schneider
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_table implements renderable, templatable {


    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Standard renderer for php output
     *
     * @return stdClass Array with labels for each level
     * @throws \dml_exception
     */
    public function export_for_template(renderer_base $output) {
        $labels = get_config('tool_supporter', 'level_labels');
        $count = 1; // Root is level 0, so we begin at 1.
        foreach (explode(';', $labels) as $label) {
            $data['label_level_'.$count] = format_string($label); // Each label will be available with {{label_level_0}}, {{label_level_1}}, etc.
            $count++;
        }

        return $data;
    }
}
