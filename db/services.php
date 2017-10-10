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

    'tool_supporter_create_new_course' => array(         // Web service function name.
        'classname'   => 'tool_supporter\external',  // Class containing the external function.
        'methodname'  => 'create_new_course',          // External function name.
        'classpath'   => 'tool/supporter/classes/external.php',  // File containing the class/external function.
        'description' => 'Create a course',    // Human readable description of the web service function.
        'type'        => 'write',                  // Database rights of the web service function (read, write).
        'ajax'        => true,
        'capabilities' => 'moodle/course:create'
    ),
    'tool_supporter_get_user_information' => array(
        'classname'   => 'tool_supporter\external',
        'methodname'  => 'get_user_information',
        'classpath'   => 'tool/supporter/classes/external.php',
        'description' => 'Get user information',
        'type'        => 'read',
        'ajax'        => true,
        'capabilities' => 'moodle/user:viewdetails, moodle/user:loginas'
    ),
    'tool_supporter_get_course_info' => array(
        'classname'   => 'tool_supporter\external',
        'methodname'  => 'get_course_info',
        'classpath'   => 'tool/supporter/classes/external.php',
        'description' => 'Get course information',
        'type'        => 'read',
        'ajax'        => true,
        'capabilities' => 'moodle/course:update'
    ),
    'tool_supporter_enrol_user_into_course' => array(
        'classname'   => 'tool_supporter\external',
        'methodname'  => 'enrol_user_into_course',
        'classpath'   => 'tool/supporter/classes/external.php',
        'description' => 'Get course information',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities' => 'enrol/manual:enrol'
    ),
    'tool_supporter_get_assignable_roles' => array(
        'classname'   => 'tool_supporter\external',
        'methodname'  => 'get_assignable_roles',
        'classpath'   => 'tool/supporter/classes/external.php',
        'description' => 'Get assignable Roles in the course',
        'type'        => 'read',
        'ajax'        => true,
        'capabilities' => 'enrol/manual:enrol'
    ),
    'tool_supporter_toggle_course_visibility' => array(
        'classname'   => 'tool_supporter\external',
        'methodname'  => 'toggle_course_visibility',
        'classpath'   => 'tool/supporter/classes/external.php',
        'description' => 'hide/show the course',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities' => 'moodle/course:update'
    ),
    'tool_supporter_get_users' => array(
        'classname'   => 'tool_supporter\external',
        'methodname'  => 'get_users',
        'classpath'   => 'tool/supporter/classes/external.php',
        'description' => 'get all users',
        'type'        => 'read',
        'ajax'        => true,
        'capabilities' => 'moodle/site:viewparticipants'
    ),
    'tool_supporter_get_courses' => array(
        'classname'   => 'tool_supporter\external',
        'methodname'  => 'get_courses',
        'classpath'   => 'tool/supporter/classes/external.php',
        'description' => 'get all courses',
        'type'        => 'read',
        'ajax'        => true,
           'capabilities' => 'moodle/course:viewhiddencourses'
    ),
    'tool_supporter_get_courses_test' => array(
        'classname'   => 'tool_supporter\external',
        'methodname'  => 'get_courses_test',
        'classpath'   => 'tool/supporter/classes/external.php',
        'description' => 'get all courses',
        'type'        => 'read',
        'ajax'        => true,
        'capabilities' => 'moodle/course:viewhiddencourses'
    ),
    'tool_supporter_get_sesskey' => array(         // Web service function name.
        'classname'   => 'tool_supporter\external',  // Class containing the external function.
        'methodname'  => 'get_sesskey',          // External function name.
        'classpath'   => 'tool/supporter/classes/external.php',  // File containing the class/external function.
        'description' => 'Get sesskey',    // Human readable description of the web service function.
        'type'        => 'read',                  // Database rights of the web service function (read, write).
        'ajax'        => true,
        'capabilities' => 'moodle/user:loginas'
    ),
);
