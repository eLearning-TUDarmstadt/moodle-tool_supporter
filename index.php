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
 * @package    local_hackfest
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$title = get_string('pagetitle', 'local_hackfest'); //dev moodle: String API
$pagetitle = get_string('pagetitle', 'local_hackfest');
// Set up the page.
$url = new moodle_url("/local/hackfest/index.php");
$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_title($title); //title und header sollten später verschieden sein
$PAGE->set_heading($title);
$output = $PAGE->get_renderer('local_hackfest');
echo $output->header();

$page = new \local_hackfest\output\index_page(); //renderable
echo $output->render($page);

echo $output->footer(); //javascript ist hier drin