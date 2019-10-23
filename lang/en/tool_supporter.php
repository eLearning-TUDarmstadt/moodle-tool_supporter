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
 * Strings for component 'tool_supporter', language 'en'
 *
 * @package    tool_supporter
 * @copyright  2019 Benedikt Schneider, Klara Saary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['toolsupporter'] = 'toolsupporter';

$string['pagetitle'] = 'Moodle-Supporter';
$string['pluginname'] = 'Supporter';
$string['level'] = 'Level';
$string['enable_selfenrolment'] = 'Activate self enrolment and set password';

$string['beingduplicated'] = "The course is being duplicated. This may take a while.";

// Strings for setting page - settings.php.
$string['sett_title'] = 'Supporter configuration';
$string['sett_levels'] = 'Labeling of course category levels';
$string['sett_levels_default'] = 'Semester;Department';
$string['sett_levels_description'] = 'Specify the displayed names of the course levels. In descending order (uppermost level first) and separated by semicolon. ';

$string['sett_course_table'] = 'Course table';
$string['sett_user_table'] = 'User table';
$string['sett_user_details'] = 'User details';
$string['sett_course_details'] = 'Course details';

$string['sett_course_table_desc'] = 'The course table lists all courses and is displayed in the bottom left.';
$string['sett_course_table_pagelength'] = 'The amount of courses shown';
$string['sett_user_table_desc'] = 'The user table lists all users and is displayed in the bottom right.';
$string['sett_user_table_pagelength'] = 'The amount of user-courses shown';
$string['sett_sort_course_table'] = 'Sorting of the ID-Column in course table';
$string['sett_sort_course_details'] = 'Sorting of the ID-Column in course view (enrolled users)';
$string['sett_sort_user_table'] = 'Sorting of the ID-Column in user table';
$string['sett_sort_user_details'] = 'Sorting of the ID-Column in user view (enrolled courses)';
$string['sett_user_details_desc'] = 'The user details are shown in the top right when a user is clicked.';
$string['sett_course_details_desc'] = 'The course details are shown in the top left when a course is clicked.';
$string['sett_course_detail_showrolesandamount'] = 'Show all roles and their amount';
$string['sett_course_detail_showrolesandamount_desc'] = 'Explicitly shows all roles and their amounts in a seperate table row per role, i.e. amount of teachers, amount of students, etc.';

$string['sett_never'] = 'never';
$string['strftimesecondsdatetimeshort'] = '%d/%m/%Y, %H:%M:%S';

// Privacy API.
$string['privacy:metadata'] = 'This plugin does not save user-specific data, only global settings.';
