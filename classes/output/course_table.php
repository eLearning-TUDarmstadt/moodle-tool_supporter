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
 * Class containing data for index page
 *
 * @package    tool_supporter
 * @copyright  2016 Benedikt Schneider, Klara Saary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_supporter\output;

//require_once("$CFG->dirroot/user/externallib.php");
require_once("$CFG->dirroot/config.php");

use renderable;
use templatable;
use renderer_base;
use stdClass;

/**
 * Class containing data for index page
 * Gets passed to the renderer
 *
 * @copyright  2016 Klara Saary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_table implements renderable, templatable {

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */

    public function export_for_template(renderer_base $output) {
        // "Flattens" the data
        global $DB;
        $rs = $DB->get_recordset('course', null, null, 'id, fullname, shortname, category'); //Load ALL courses in $rs (object), fields: id, fullname, shortname, category
      //Absatz soll ersetzt werden durch Webservice, siehe user_table.php und externallib.php
        foreach ($rs as $record) { //single course as array in array $courses, so it can be rendered
          $cat_id = $record->category;
          $record = (array)$record;
          $record['parent_category'] = null;
          if($cat_id != 0){
            $path = $DB->get_record_sql('Select path FROM {course_categories} WHERE id = ?', array($cat_id));
            //$path = $DB->get_record('course_categories', array('id' => $cat_id), 'path');
            $record['category'] = $this->get_category((string)$path->path, -1);
            $record['parent_category'] =$this->get_category((string)$path->path, 1);
          }
          $courses[] = $record;
        }
        $rs->close();
        //  $categories->close();
        $data['courses'] = $courses;
        // echo "<pre>" . print_r($data, true) . "</pre>";
        return $data;
    }

    /** function: get_category
        pass path of category and the number of the parent category your looking for.
        $num = 1 for highest parent-category, $num = -1 for the direct category
        Example: $path: /2/5/1 -> 1 is the id of teh direct category and 2 the id of the highest parent-category
                  get_category($path, 1) = "name of category with id 2"
                  get_category($path, 3) = "name of category with id 5"
    */
    function get_category($path, $num){
      global $DB;
      if ($path != null){
        $cat_num = substr($path, $num, 1);
        if ($cat_num != false){
          $category = $DB->get_record('course_categories', array('id' => $cat_num), 'name');
          return $category->name;
        }
      }
      else return null;
    }
}
