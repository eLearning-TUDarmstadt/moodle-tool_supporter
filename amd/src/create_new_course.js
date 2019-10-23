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
        function($, ajax, templates, notification, str, loadInformation) {
            return /** @alias module:tool_supporter/createNewCourse */ {

                /**
                 * Toggles the disabled-property of the password input
                 *
                 * @method togglePasswordInput
                 */
                togglePasswordInput: function() {
                    $('#new_course_enable_self').on('click', function() {
                        // Set to the opposite of the checkbox.
                        $('#new_course_self_password').prop('disabled', !this.checked);
                    });
                },

                /**
                 * Show the form to create a course
                 *
                 * @method showNewCourse
                 */
                showNewCourse: function() {
                    $('#btn_show_new_course').on('click', function() {
                        $('[data-region="create_new_course_section"]').toggle();
                    });
                },

                /**
                 * Create a course
                 *
                 * @method createNewCourse
                 */
                createNewCourse: function() {
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
                            var promise1 = loadInformation.showCourseDetail(data.id, true);

                            promise1[0].done(function(data) {
                                $('[data-region="create_new_course_section"]').hide();

                                // Add the newly created course to the DataTable without reloading the whole thing.
                                $('#courseTable').DataTable().row.add({
                                    "id": data.courseDetails.id,
                                    "shortname": data.courseDetails.shortname,
                                    "fullname": data.courseDetails.fullname,
                                    "level_one": data.courseDetails.level_one,
                                    "level_two": data.courseDetails.level_two,
                                    "visible": +data.courseDetails.visible, // Implicity cast false to 0.
                                }).draw(false);
                            });
                        });

                        promises[0].fail(function(error) {
                            str.get_string('error', 'error').done(function(errorString) {
                                str.get_string('ok', 'moodle').done(function(accept) {
                                    var wantedShortname = $('#new_course_short_name_input')[0].value;
                                    str.get_string('shortnametaken', 'error', wantedShortname).done(
                                            function(shortnametakenString) {
                                                var errorMessage = "Possible problem: " + shortnametakenString + "<br><br>";
                                                if (error.message) {
                                                    errorMessage += "Error-Message:<br>" + error.message + "<br><br>";
                                                }
                                                if (error.debuginfo) {
                                                    errorMessage += "Debuginfo:<br>" + error.debuginfo + "<br><br>";
                                                }

                                                notification.alert(errorString, errorMessage, accept);
                                            });
                                });
                            });

                        });

                    });
                },

                /**
                 * Duplicate a course
                 *
                 * @method duplicateCourse
                 */
                duplicateCourse: function() {
                    $('#duplicate_course_button').on('click', function() {

                        var promises = ajax.call([{
                            "methodname": 'tool_supporter_duplicate_course',
                            args: {
                                courseid: $('#selectedcourseid')[0].textContent
                            }
                        }]);

                        str.get_string('beingduplicated', 'tool_supporter').done(function(beingduplicatedstring) {
                            notification.addNotification({
                                message: beingduplicatedstring,
                                type: "info"
                            });
                        });


                        promises[0].done(function(data) { // Returns courseid and shortname.
                            // Display the created course.
                            var promise1 = loadInformation.showCourseDetail(data.id, true);

                            promise1[0].done(function(data) {
                                // Add the newly created course to the DataTable without reloading the whole thing.
                                $('#courseTable').DataTable().row.add({
                                    "id": data.courseDetails.id,
                                    "shortname": data.courseDetails.shortname,
                                    "fullname": data.courseDetails.fullname,
                                    "level_one": data.courseDetails.level_one,
                                    "level_two": data.courseDetails.level_two,
                                    "visible": +data.courseDetails.visible, // Implicity cast false to 0.
                                }).draw(false);
                            });

                        }).fail(notification.exception);
                    });
                }

            };
        });
