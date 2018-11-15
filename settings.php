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
 * Version details.
 *
 * @package    tool_supporter
 * @copyright  2017, Klara Saary, Benedikt Schneider
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

// Possible places in admin menu: root, users, courses, reports, (admin) tools
// Add the plugin to Administration menu.
$ADMIN->add('reports', new admin_externalpage('toolsupporter', get_string('pluginname', 'tool_supporter'),
         new moodle_url('/admin/tool/supporter/index.php')));

// Settings page
if ($hassiteconfig) {
    // These are currently stored in the "mdl_config"-table
    $settings = new admin_settingpage('tool_supporter', get_string('supporter_settings', 'tool_supporter'));
    // Add the config page to the administration menu.
    $ADMIN->add('reports', $settings);

    $settings->add(new admin_setting_heading('level_labeling', '', get_string('levels', 'tool_supporter')." FUNKTIONIERT AKTUELL NICHT"));
    $settings->add(new admin_setting_configtext('tool_supporter_levels', get_string('levels', 'tool_supporter'),
        get_string('levels_description', 'tool_supporter'), get_string('levels_default', 'tool_supporter'), PARAM_TEXT));

    $settings->add(new admin_setting_heading('course_details', 'heading for course details', 'In this section you can select all the things you want to have shown in the user details, i.e. when a user is clicked.'));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_course_details_showid', "Show user ID", "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_course_details_showshortname', "Show shortname", "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_course_details_showfullname', "Show fullname", "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_course_details_showvisible', "Show visible", "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_course_details_showpath', "Show path", "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_course_details_showtimecreated', "Show time created", "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_course_details_showusersamount', "Show total amount of users", "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_course_details_showrolesandamount', "Show all roles and their amount", "Explicitly shows all roles and their amounts in seperate table row, i.e. amount of teachers, amount of student, etc.", 1));

    $settings->add(new admin_setting_heading('user_details', 'heading for user details', 'In this section you can select all the things you want to have shown in the user details, i.e. when a user is clicked.'));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_user_details_showid', "Show ID", "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_user_details_showusername', "Show username", "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_user_details_showidnumber', "Show field idnumber", "", 0));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_user_details_showfirstname', "Show first name", "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_user_details_showlastname', "Show last name", "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_user_details_showmailadress', "Show mail adress", "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_user_details_showtimecreated', "Show time created", "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_user_details_showtimemodified', "Show time modified", "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_user_details_showlastlogin', "Show last login", "", 1));


}
