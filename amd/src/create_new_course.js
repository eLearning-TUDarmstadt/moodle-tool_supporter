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
 * @copyright  2019 Benedikt Schneider
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1.1
 */
define(['jquery', 'core/ajax', 'core/templates', 'core/notification', 'core/str', 'tool_supporter/load_information'],
        function($, ajax, templates, notification, str, load_information) {
            return /** @alias module:tool_supporter/create_new_course */ {

                /**
                 * Toggles the disabled-property of the password input
                 *
                 * @method toggle_password_input
                 */
                toggle_password_input: function() {
                    $('#new_course_enable_self').on('click', function() {
                        // Set to the opposite of the checkbox.
                        $('#new_course_self_password').prop('disabled', !this.checked);
                    });
                },

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
                                categoryid: $('#new_course_category_input')[0].value,
                                activateselfenrol: $("#new_course_enable_self").is(":checked"),
                                selfenrolpassword: $('#new_course_self_password')[0].value,
                                startdate: $('#new_course_startdate_input')[0].value,
                                enddate: $('#new_course_enddate_input')[0].value,
                            }
                        }], true, true);

                        promises[0].done(function(data) {
                            // Display the created course.
                            var promise1 = load_information.show_course_detail(data.id, true);

                            promise1[0].done(function(data){
                                $('[data-region="create_new_course_section"]').hide();

                                // Add the newly created course to the DataTable without reloading the whole thing.
                                $('#courseTable').DataTable().row.add({
                                    "id": data['courseDetails']['id'],
                                    "shortname": data['courseDetails']['shortname'],
                                    "fullname": data['courseDetails']['fullname'],
                                    "level_one": data['courseDetails']['level_one'],
                                    "level_two": data['courseDetails']['level_two'],
                                    "visible": +data['courseDetails']['visible'], // Implicity cast false to 0.
                                }).draw(false);
                            });
                        });

                        promises[0].fail(function(error) {
                            //console.log("There was an error during course creation - response is:");
                            //console.log(error);

                            str.get_string('error', 'error').done(function(error_string) {
                                str.get_string('ok', 'moodle').done(function(accept) {
                                    var wanted_shortname = $('#new_course_short_name_input')[0].value;
                                    str.get_string('shortnametaken', 'error', wanted_shortname).done(
                                            function(shortnametaken_string) {
                                                var error_message = "Possible problem: " + shortnametaken_string + "<br><br>";
                                                if (error.message) {
                                                    error_message += "Error-Message:<br>" + error.message + "<br><br>";
                                                }
                                                if (error.debuginfo) {
                                                    error_message += "Debuginfo:<br>" + error.debuginfo + "<br><br>";
                                                }

                                                notification.alert(error_string, error_message, accept);
                                            });
                                });
                            });

                        });

                    });
                }
            };
        });
