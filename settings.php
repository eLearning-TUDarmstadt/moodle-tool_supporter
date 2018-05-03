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
// Add to Plugins->Admin tools
$ADMIN->add('tools', new admin_externalpage('toolsupporter', get_string('pluginname', 'tool_supporter'),
         new moodle_url('/admin/tool/supporter/index.php')));

if ($hassiteconfig) {
	$settings = new admin_settingpage('tool_supporter', get_string('supporter_settings', 'tool_supporter'));
	$ADMIN->add('tools', $settings);
  
    $settings->add(new admin_setting_heading('level_labeling', '', get_string('levels', 'tool_supporter')));
    $settings->add(new admin_setting_configtext('supporter_level', get_string('levels', 'tool_supporter'),
                            get_string('levels_description', 'tool_supporter'), get_string('levels_default', 'tool_supporter'), PARAM_RAW));
}
