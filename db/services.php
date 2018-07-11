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
 * tool supporter external services.
 *
 * @package    tool_supporter
 * @copyright  2017 Benedikt Schneider, Klara Saary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$functions = array(

    /* -------- Read-only --------- */
    'tool_supporter_get_users' => array(
        'classname'   => 'tool_supporter\external',
        'methodname'  => 'get_users',
        'classpath'   => 'tool/supporter/classes/external.php',
        'description' => 'Get overview of all users',
        'type'        => 'read',
        'ajax'        => true,
        'requiredcapability' => 'moodle/site:viewparticipants'
    ),
    'tool_supporter_get_user_information' => array(
        'classname'   => 'tool_supporter\external',
        'methodname'  => 'get_user_information',
        'classpath'   => 'tool/supporter/classes/external.php',
        'description' => 'Get detailed user information (clicking on a user)',
        'type'        => 'read',
        'ajax'        => true,
        'requiredcapability' => 'moodle/user:viewdetails'
    ),
    'tool_supporter_get_courses' => array(
        'classname'   => 'tool_supporter\external',
        'methodname'  => 'get_courses',
        'classpath'   => 'tool/supporter/classes/external.php',
        'description' => 'Get overview of all courses',
        'type'        => 'read',
        'ajax'        => true,
        'requiredcapability' => 'moodle/course:viewhiddencourses'
    ),
    'tool_supporter_get_course_info' => array(
        'classname'   => 'tool_supporter\external',
        'methodname'  => 'get_course_info',
        'classpath'   => 'tool/supporter/classes/external.php',
        'description' => 'Get detailed course information (clicking on a course)',
        'type'        => 'read',
        'ajax'        => true,
        'requiredcapability' => 'moodle/course:view'
    ),
    /* -------- Write-capabilites --------- */
    'tool_supporter_get_sesskey' => array(
        'classname'   => 'tool_supporter\external',
        'methodname'  => 'get_sesskey',
        'classpath'   => 'tool/supporter/classes/external.php',
        'description' => 'Get sesskey',
        'type'        => 'read, write',
        'ajax'        => true,
        'requiredcapability' => 'moodle/user:loginas'
    ),
    'tool_supporter_create_new_course' => array(
        'classname'   => 'tool_supporter\external',
        'methodname'  => 'create_new_course',
        'classpath'   => 'tool/supporter/classes/external.php',
        'description' => 'Create a course',
        'type'        => 'write',
        'ajax'        => true,
        'requiredcapability' => 'moodle/course:create'
    ),
    'tool_supporter_toggle_course_visibility' => array(
        'classname'   => 'tool_supporter\external',
        'methodname'  => 'toggle_course_visibility',
        'classpath'   => 'tool/supporter/classes/external.php',
        'description' => 'hide/show the course',
        'type'        => 'write',
        'ajax'        => true,
        'requiredcapability' => 'moodle/course:update'
    ),
    'tool_supporter_enrol_user_into_course' => array(
        'classname'   => 'tool_supporter\external',
        'methodname'  => 'enrol_user_into_course',
        'classpath'   => 'tool/supporter/classes/external.php',
        'description' => 'Get course information',
        'type'        => 'write',
        'ajax'        => true,
        'requiredcapability' => 'enrol/manual:enrol'
    ),
    'tool_supporter_get_assignable_roles' => array(
        'classname'   => 'tool_supporter\external',
        'methodname'  => 'get_assignable_roles',
        'classpath'   => 'tool/supporter/classes/external.php',
        'description' => 'Get assignable Roles in the course, e.g. used for enrolling',
        'type'        => 'read, write',
        'ajax'        => true,
        'requiredcapability' => 'enrol/manual:enrol'
    ),
);
