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
 * This modules creates helper functions for creating a course
 *
 * @module     tool_supporter/create_new_course
 * @package    tool_supporter
 * @copyright  2017 Benedikt Schneider
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1.1
 */
define(['jquery', 'core/ajax', 'core/templates', 'core/notification', 'core/str', 'tool_supporter/load_information'], function($, ajax, templates, notification, str, load_information) {
    return /** @alias module:tool_supporter/create_new_course */ {

        /**
         * show the form to create a course
         *
         * @method show_new_course
         */
        show_new_course: function() {
            $('#btn_show_new_course').on('click', function() {
                $('[data-region="create_new_course_section"]').toggle();
            });
        },

        /**
         * Create a course
         *
         * @method create_new_course
         */
        create_new_course: function() {
            $('#create_new_course_button').on('click', function() {

                var promises = ajax.call([{
                    methodname: 'tool_supporter_create_new_course',
                    args: {
                        shortname: $('#new_course_short_name_input')[0].value,
                        fullname: $('#new_course_full_name_input')[0].value,
                        visible: $("#new_course_is_visible").is(":checked"),
                        categoryid: $('#new_course_category_input')[0].value
                    }
                }]);

                promises[0].done(function(data) {
                    console.log("create new course return data: ");
                    console.log(data);

                    // Display the created course.
                    var promise1 = load_information.show_course_detail(data.id, true);
                    $('[data-region="create_new_course_section"]').toggle();
                    
                    var otables = $.fn.dataTable.tables();
                    var coursetableid;
                    $.each(otables, function(i, val) {  
                        if (val.id.indexOf("courseTable") >= 0) {
                            coursetableid = val.id;
                            return false;
                        }
                    });
                    promise1[0].done(function(data){
                        var visible = 0;
                        if(data.courseDetails.visible) visible = 1;

                        // Add the newly created course to the DataTable without reloading the whole thing
                        $('#' + coursetableid).DataTable().row.add({
                            "id": data.courseDetails.id,
                            "shortname": data.courseDetails.shortname,
                            "fullname": data.courseDetails.fullname,
                            "fb": data.courseDetails.fb,
                            "semester": data.courseDetails.semester,
                            "visible": visible
                        }).draw( false );
                    });
                });

                promises[0].fail(function() {
                    str.get_string('error', 'error').done(function(error) {
                        str.get_string('duplicateroleshortname', 'error').done(function(duplicateroleshortname) {
                            str.get_string('continue', 'hub').done(function(next) {
                                notification.alert(error, duplicateroleshortname, next);
                            });
                        });
                    });
                });
            });
        }
    };
});
