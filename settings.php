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
 * @copyright  2019 Klara Saary, Benedikt Schneider
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

// Possible places in admin menu: root, users, courses, reports, (admin) tools.
// Add the plugin to Administration menu.
$ADMIN->add('reports', new admin_externalpage('toolsupporter', get_string('pluginname', 'tool_supporter'),
         new moodle_url('/admin/tool/supporter/index.php')));

// Settings page.
if ($hassiteconfig) {
    // These are stored in table 'config_plugins'.
    $settings = new admin_settingpage('tool_supporter', get_string('sett_title', 'tool_supporter'));
    // Add the config page to the administration menu.
    $ADMIN->add('reports', $settings);

    // Settings for level naming.
    $settings->add(new admin_setting_configtext('tool_supporter/level_labels', get_string('sett_levels', 'tool_supporter'),
        get_string('sett_levels_description', 'tool_supporter'),
        get_string('sett_levels_default', 'tool_supporter'), PARAM_TEXT));

    // Settings for course table (bottom left).
    $settings->add(new admin_setting_heading('header_course_table', get_string('sett_course_table', 'tool_supporter'),
        get_string('sett_course_table_desc', 'tool_supporter')));
    $settings->add(new admin_setting_configtext('tool_supporter/course_table_pagelength',
        get_string('sett_course_table_pagelength', 'tool_supporter'), "", 30, PARAM_INT));
    $settings->add(new admin_setting_configselect('tool_supporter/course_table_order',
        get_string('sett_sort_course_table', 'tool_supporter'), "", "desc",
        array("asc" => get_string('asc'), "desc" => get_string('desc'))));
    $settings->add(new admin_setting_configcheckbox('tool_supporter/course_table_viewhiddenscourses',
        get_string('course:viewhiddencourses', 'role'), "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter/course_table_viewhiddenscat',
        get_string('category:viewhiddencategories', 'role'), "", 1));

    // Settings for user table (bottom right).
    $settings->add(new admin_setting_heading('header_user_table', get_string('sett_user_table', 'tool_supporter'),
        get_string('sett_user_table_desc', 'tool_supporter')));
    $settings->add(new admin_setting_configtext('tool_supporter/user_table_pagelength',
        get_string('sett_user_table_pagelength', 'tool_supporter'), "", 30, PARAM_INT));
    $settings->add(new admin_setting_configselect('tool_supporter/user_table_order', get_string('sett_sort_user_table', 'tool_supporter'),
        "", "asc", array("asc" => get_string('asc'), "desc" => get_string('desc'))));
    $settings->add(new admin_setting_configcheckbox('tool_supporter/user_table_excludesuspended',
        get_string('exportonlyactive', 'grades'), "", 1));

    // Standard settings for new course.
    $settings->add(new admin_setting_heading('header_new_course', get_string('addnewcourse', 'core'), ""));
    $settings->add(new admin_setting_configtext('tool_supporter/new_course_startdate',
        get_string('standard')." ".get_string('startdate', 'core'), "", '01.04.2019', PARAM_TEXT));
    $settings->add(new admin_setting_configtext('tool_supporter/new_course_enddate',
        get_string('standard')." ".get_string('enddate', 'core'), "", '30.09.2019', PARAM_TEXT));

    // Settings for course details (top left).
    $settings->add(new admin_setting_heading('header_course_details',
        get_string('sett_course_details', 'tool_supporter'),
        get_string('sett_course_details_desc', 'tool_supporter')));
    $settings->add(new admin_setting_configtext('tool_supporter/course_details_pagelength',
        get_string('sett_course_table_pagelength', 'tool_supporter'), "", 10, PARAM_INT));
    $settings->add(new admin_setting_configselect('tool_supporter/course_details_order',
        get_string('sett_sort_course_details', 'tool_supporter'), "", "desc",
        array("asc" => get_string('asc'), "desc" => get_string('desc'))));
    $settings->add(new admin_setting_configcheckbox('tool_supporter/course_details_showshortname',
        get_string('shortnamecourse'), "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter/course_details_showfullname',
        get_string('fullnamecourse'), "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter/course_details_showvisible',
        get_string('visible'), "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter/course_details_showpath',
        get_string('path'), "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter/course_details_showtimecreated',
        get_string('eventcoursecreated'), "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter/course_details_showusersamount',
        get_string('users'), "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter/course_details_showrolesandamount',
        get_string('sett_course_detail_showrolesandamount', 'tool_supporter'),
        get_string('sett_course_detail_showrolesandamount_desc', 'tool_supporter'), 1));

    // Settings for user details (top right).
    $settings->add(new admin_setting_heading('header_user_details', get_string('sett_user_details', 'tool_supporter'),
        get_string('sett_user_details_desc', 'tool_supporter')));
    $settings->add(new admin_setting_configtext('tool_supporter/user_details_pagelength',
        get_string('sett_user_table_pagelength', 'tool_supporter'), "", 10, PARAM_INT));
    $settings->add(new admin_setting_configselect('tool_supporter/user_details_order',
        get_string('sett_sort_user_details', 'tool_supporter'),
        "", "asc", array("asc" => get_string('asc'), "desc" => get_string('desc'))));
    $settings->add(new admin_setting_configcheckbox('tool_supporter/user_details_showusername',
        get_string('username'), "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter/user_details_showidnumber',
        get_string('idnumbermod'), "", 0));
    $settings->add(new admin_setting_configcheckbox('tool_supporter/user_details_showfirstname',
        get_string('firstname'), "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter/user_details_showlastname',
        get_string('lastname'), "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter/user_details_showmailadress',
        get_string('email'), "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter/user_details_showtimecreated',
        get_string('eventusercreated'), "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter/user_details_showtimemodified',
        get_string('lastmodified'), "", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter/user_details_showlastlogin',
        get_string('lastlogin'), "", 1));
}