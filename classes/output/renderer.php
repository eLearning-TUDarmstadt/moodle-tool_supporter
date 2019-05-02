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
 * @copyright  2019 Benedikt Schneider, Klara Saary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_supporter\output;

defined('MOODLE_INTERNAL') || die;

use plugin_renderer_base;

/**
 * Renderer class for tool supporter.
 *
 * @package    tool_supporter
 * @copyright  2019 Klara Saary, Benedikt Schneider
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Defer to template.
     *
     * @param index_page $page
     *
     * @return string html for the page
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function render_index_page($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('tool_supporter/index_page', $data);
    }

    /**
     * Renders the user table with all users
     *
     * @param index_page $user
     * @return bool|string
     * @throws \moodle_exception
     */
    public function render_user_table($user) {
        $data = $user->export_for_template($this);
        return parent::render_from_template('tool_supporter/user_table', $data);
    }

    /**
     * Renders the course table with all courses
     *
     * @param index_page $course
     * @return bool|string
     * @throws \moodle_exception
     */
    public function render_course_table($course) {
        $data = $course->export_for_template($this);
        return parent::render_from_template('tool_supporter/course_table', $data);
    }

    /**
     * Renders details of a course after it was clicked
     *
     * @param index_page $coursedetail
     * @return bool|string
     * @throws \moodle_exception
     */
    public function render_course_detail($coursedetail) {
        $data = $coursedetail->export_for_template($this);
        return parent::render_from_template('tool_supporter/course_detail', $data);
    }

    /**
     * Renders the section to create a new course
     *
     * @param index_page $createnewcourse
     * @return bool|string
     * @throws \moodle_exception
     */
    public function render_create_new_course($createnewcourse) {
        $data = $createnewcourse->export_for_template($this);
        return parent::render_from_template('tool_supporter/create_new_course', $data);
    }

    /**
     * Renders the details of a clicked user
     *
     * @param index_page $userdetail
     * @return bool|string
     * @throws \moodle_exception
     */
    public function render_user_detail($userdetail) {
        $data = $userdetail->export_for_template($this);
        return parent::render_from_template('tool_supporter/user_detail', $data);
    }

    /**
     * Renders the
     *
     * @param index_page $getuserinformation
     * @return bool|string
     * @throws \moodle_exception
     */
    public function render_get_user_information_courses($getuserinformation) {
        $data = $getuserinformation->export_for_template($this);
        return parent::render_from_template('tool_supporter/get_user_information', $data);
    }

    /**
     * Renders the section where users are enrolled into a selected course
     *
     * @param index_page $enrolusersection
     * @return bool|string
     * @throws \moodle_exception
     */
    public function render_enrolusersection($enrolusersection) {
        $data = $enrolusersection->export_for_template($this);
        return parent::render_from_template('tool_supporter/enrolusersection', $data);
    }
}
