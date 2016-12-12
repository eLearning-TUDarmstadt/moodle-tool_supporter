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
 * @copyright  2016 Benedikt Schneider, Klara Saary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../../config.php');

$title = get_string('pagetitle', 'tool_supporter');
$pagetitle = get_string('pagetitle', 'tool_supporter');
// Set up the page.
$url = new moodle_url("/admin/tool/supporter/index.php");
echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">';
$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$output = $PAGE->get_renderer('tool_supporter');

$index_page = new \tool_supporter\output\index_page();
$index = $output->render($index_page);

$user = new \tool_supporter\output\user_table();
$user_table = $output->render($user);

$course = new \tool_supporter\output\course_table();
$course_table = $output->render($course);

//$course_detail = new \tool_supporter\output\course_detail();
//$course_detail_v = $output->render($course_detail);

$create_new_course = $output->render(new \tool_supporter\output\create_new_course());

echo $output->header();
echo $index;
//echo $course_detail_v;

?>

<?php echo $create_new_course; ?>

<div class="container">
    <div class="row-fluid">
      <div class="cole-md">
        <?php echo $user_table; ?>
      </div>
      <div class="cole-md">
        <?php echo $course_table; ?>
     </div>
    </div>
</div>


<?php echo $output->footer(); ?>
