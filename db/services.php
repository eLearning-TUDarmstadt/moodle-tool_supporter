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
 * Local hackfest external services.
 *
 * @package    local_hackfest
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(

    // For each functuon: Which class provides the function?

    //Name of webservice: "local_hackfest"
    'local_hackfest_get_site_info' => array(
        'classname'   => 'local_hackfest\external',
        'methodname'  => 'get_site_info',
        'classpath'   => '',
        'description' => 'Return some site info.',
        'type'        => 'read',
        'capabilities'=> '',
    )
);

