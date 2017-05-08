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
 * @copyright  2017 Benedikt Schneider, Klara Saary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../../config.php');

$title = get_string('pagetitle', 'tool_supporter');
$pagetitle = get_string('pagetitle', 'tool_supporter');
// Set up the page.
$url = new moodle_url("/admin/tool/supporter/index.php");
//echo '<link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">';
//echo '<link rel="stylesheet" type="text/css" href="/admin/tool/supporter/style/datatables.css">';
//echo '<script type="text/javascript" charset="utf8" src="/admin/tool/supporter/amd/src/datatables.js"></script>';
//echo '<script type="text/javascript" charset="utf8" src="/admin/tool/supporter/amd/src/datatables.responsive.js"></script>';
$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->requires->js_call_amd('tool_supporter/datatables', 'use_dataTable', array('.datatable', array()));

// $PAGE->requires->css('/admin/tool/supporter/style/dataTables.bootstrap.css');
// $PAGE->requires->css('/admin/tool/supporter/style/dataTables.bootstrap4.css');
// $PAGE->requires->css('/admin/tool/supporter/style/responsive.bootstrap.css');
// $PAGE->requires->css('/admin/tool/supporter/style/responsive.datatables.css');
// $PAGE->requires->css('/admin/tool/supporter/style/responsive.jqueryui.css');
// $PAGE->requires->css('/admin/tool/supporter/style/responsive.foundation.css');
// $PAGE->requires->css('/admin/tool/supporter/style/dataTables.jqueryui.css');
//$PAGE->requires->css('/admin/tool/supporter/style/dataTables.foundation.css');
//$PAGE->requires->css('/admin/tool/supporter/style/dataTables.semanticui.css');
//$PAGE->requires->css('/admin/tool/supporter/style/select.bootstrap.css');
//$PAGE->requires->css('/admin/tool/supporter/style/bootstrap-themes.css');
$PAGE->requires->css('/admin/tool/supporter/style/jquery.datatables.css');

$output = $PAGE->get_renderer('tool_supporter');

$index = $output->render(new \tool_supporter\output\index_page());
$user_table = $output->render(new \tool_supporter\output\user_table());
$course_table = $output->render(new \tool_supporter\output\course_table());
$create_new_course = $output->render(new \tool_supporter\output\create_new_course());
$user_detail = $output->render(new \tool_supporter\output\user_detail());
$course_detail = $output->render(new \tool_supporter\output\course_detail());

echo $output->header();
echo $index;

echo $create_new_course;

echo '

<style>
.well {
    background-color: 	rgb(252,253,253);
}
</style>

<div class="container-fluid">
<div class="row-fluid">
  <div class="col-md-12">
    <div class="row-fluid">
      <div class="span6 col-md-6">
        <div class="col-md-12">
          '.$course_detail.'
          </div>
        <div class="col-md-12">
          '.$course_table.'
        </div>
      </div>
      <div class="span6 col-md-6">
        <div class="col-md-12">
          '.$user_detail.'
          </div>
        <div class="col-md-12">
          '.$user_table.'
        </div>
      </div>
    </div>
  </div>
</div>
</div>
';

echo $output->footer();
