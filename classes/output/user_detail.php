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
 * Class containing data for user_detail
 *
 * @package    tool_supporter
 * @copyright  2019 Benedikt Schneider, Klara Saary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_supporter\output;

use renderable;
use templatable;
use renderer_base;
use stdClass;

/**
 * Class containing data for user_detail
 *
 * @copyright  2019 Klara Saary, Benedikt Schneider
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_detail implements renderable, templatable {
    /**
     * Standard functions which is needed, but does not get any data
     *
     * @param renderer_base $output
     * @return array without any content
     */
    public function export_for_template(renderer_base $output) {
        global $CFG;

        $data['showlevel1'] = get_config('tool_supporter', 'course_table_showlevel1');
        $data['showlevel2'] = get_config('tool_supporter', 'course_table_showlevel2');
        $data['showlevel3'] = get_config('tool_supporter', 'course_table_showlevel3');
        $data['showlevel4'] = get_config('tool_supporter', 'course_table_showlevel4');
        $data['showlevel5'] = get_config('tool_supporter', 'course_table_showlevel5');

        return $data;
    }
}
