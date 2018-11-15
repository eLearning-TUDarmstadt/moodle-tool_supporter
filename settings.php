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
    $settings->add(new admin_setting_configcheckbox('tool_supporter_showid', "Show user ID", "Beschreibung", 1));
    // TODO: Do the same thing for course detail

    $settings->add(new admin_setting_heading('user_details', 'heading for user details', 'In this section you can select all the things you want to have shown in the user details, i.e. when a user is clicked.'));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_showid', "Show ID", "Beschreibung", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_showusername', "Show username", "Beschreibung", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_showidnumber', "Show field idnumber", "Beschreibung", 0));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_showfirstname', "Show first name", "Beschreibung", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_showlastname', "Show last name", "Beschreibung", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_showmailadress', "Show mail adress", "Beschreibung", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_showtimecreated', "Show time created", "Beschreibung", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_showtimemodified', "Show time modified", "Beschreibung", 1));
    $settings->add(new admin_setting_configcheckbox('tool_supporter_showlastlogin', "Show last login", "Beschreibung", 1));


}
