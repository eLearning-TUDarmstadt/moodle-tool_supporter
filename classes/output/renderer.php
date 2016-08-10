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
 * Renderer class for tool supporter.
 *
 * @package    tool_supporter
 * @copyright  2016 Benedikt Schneider, Klara Saary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_supporter\output;

defined('MOODLE_INTERNAL') || die;

use plugin_renderer_base;

/**
 * Renderer class for tool supporter.
 *
 * @package    tool_supporter
 * @copyright  2016 Benedikt Schneider
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Defer to template.
     *
     * @param index_page $page
     *
     * @return string html for the page
     */
    public function render_index_page($page) { //index_page: type of renderable; "render" muss immer davor
        $data = $page->export_for_template($this);
        return parent::render_from_template('tool_supporter/index_page', $data);
    }

    public function render_user_table($user) { //user_table: type of renderable; "render" muss immer davor
        $data = $user->export_for_template($this);
        return parent::render_from_template('tool_supporter/user_table', $data);
    }

    public function render_course_table($course) { //course_table: type of renderable; "render" muss immer davor
        $data = $course->export_for_template($this);
        return parent::render_from_template('tool_supporter/course_table', $data);
    }

}
