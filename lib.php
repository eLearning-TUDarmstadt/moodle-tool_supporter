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
 * Assigns Font-Awesome icons to Moodle icons
 *
 * @package   tool_supporter
 * @copyright 2019 Benedikt Schneider
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

 /**
  * Get icon mapping for font-awesome.
  *
  * @return  array
  */
function tool_supporter_get_fontawesome_icon_map() {
    return [
        'tool_supporter:i/signin' => 'fa-sign-in',
        'tool_supporter:i/hide' => 'fa-eye-slash',
        'tool_supporter:i/minus' => 'fa-minus',
        'tool_supporter:i/plus' => 'fa-plus',
        'tool_supporter:i/copy' => 'fa-copy',
    ];
}