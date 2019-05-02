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
 * This is the only page in this plugin.
 *
 * @package    tool_supporter
 * @copyright  2019 Benedikt Schneider, Klara Saary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../../config.php');

$title = get_string('pagetitle', 'tool_supporter');
$pagetitle = get_string('pagetitle', 'tool_supporter');
// Set up the page.
$url = new moodle_url("/admin/tool/supporter/index.php");

$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->blocks->show_only_fake_blocks(); // Disable blocks for layouts which do include pre-post blocks.
require_login();

$PAGE->requires->js_call_amd('tool_supporter/datatables', 'useDataTable', array('.datatable', array()));

// Needed for sorting-arrows and responsive tables without horizontal scrollbars, version 1.10.18.
$PAGE->requires->css('/admin/tool/supporter/style/dataTables.bootstrap4.css');
// Needed for Paging-Buttons and spacing in tables, version 1.10.18.
$PAGE->requires->css('/admin/tool/supporter/style/jquery.dataTables.css');
$PAGE->requires->css('/admin/tool/supporter/style/styles.css');



$output = $PAGE->get_renderer('tool_supporter');

$index = $output->render(new \tool_supporter\output\index_page());
$usertable = $output->render(new \tool_supporter\output\user_table());
$coursetable = $output->render(new \tool_supporter\output\course_table());
$createnewcourse = $output->render(new \tool_supporter\output\create_new_course());
$userdetail = $output->render(new \tool_supporter\output\user_detail());
$coursedetail = $output->render(new \tool_supporter\output\course_detail());

$PAGE->set_headingmenu($index);

// Force collapsed flat navigation for this page only.
$oldpref = get_user_preferences('drawer-open-nav');
set_user_preference('drawer-open-nav', false);
echo $output->header();
set_user_preference('drawer-open-nav', $oldpref);

echo'
    <div class="row-fluid">
        <div class="span12 col-sm-12">
            <div class="row-fluid">
                <div class="span6 col-sm-6">
                    <div class="row-fluid">
                        <div class="span12 col-md-12">
                          '.$createnewcourse.'
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12 col-sm-12">
                          '.$coursedetail.'
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12 col-sm-12">
                          '.$coursetable.'
                        </div>
                    </div>
                </div>

                <div class="span6 col-sm-6">
                    <div class="row-fluid">
                        <div class="span12 col-sm-12">
                             '.$userdetail.'
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12 col-sm-12">
                            '.$usertable.'
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
';
echo $output->footer();
