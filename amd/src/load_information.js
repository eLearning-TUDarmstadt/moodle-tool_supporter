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
 * Module witch provides mostly jquery user interactions and ajax calls
 *
 * @module     tool_supporter/load_information
 * @package    tool_supporter
 * @copyright  2017 Benedikt Schneider, Klara Saary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1.1
 */
define(['jquery', 'core/ajax', 'core/templates', 'core/notification'], function($, ajax, templates, notification) {

    // Private Stuff.

    var show_enrol_section = function(courseID) {
        var promise = ajax.call([{
            methodname: 'tool_supporter_get_assignable_roles',
            args: {
                courseID: courseID
            }
        }], true, true);
        promise[0].done(function(data){
            console.log("assignableRoles Returns: ");
            console.log(data);
            // Render template with data.
            templates.render('tool_supporter/enrolusersection', data).done(function(html, js) {
                $('[data-region="enroluserregion"]').replaceWith(html);
                $('[data-region="enroluserregion"]').show();
                templates.runTemplateJS(js);
            }).fail(notification.exception);
        }).fail(notification.exception);
    };

    // Toggling course visibility on and off.
    var toggle_course_visibility_private = function() {
        var promises = ajax.call([{
            methodname: 'tool_supporter_toggle_course_visibility',
            args: {
                courseID: $('#selectedcourseid')[0].textContent
            }
        }], true, true);

        promises[0].done(function(course) {
            console.log("toggle visibility return data");
            console.log(course['courseDetails'].visible);

            // Re-render the template to show the changes.
            templates.render('tool_supporter/course_detail', course).done(function(html, js) {
                $('[data-region="course_details"]').replaceWith(html);
                $('[data-region="course_details"]').show();
                templates.runTemplateJS(js);
            }).fail(notification.exception);
        }).fail(notification.exception);
    };

    // Public Stuff.

    var public = {

        /**
         * show the course details
         * @method show_course_detail
         */
        show_course_detail: function(course_id, boolreturn) {
            if(boolreturn === 'undefined') {
                boolreturn = 0;
            }
            // Go to top.
            var position = $("#course_details").offset().top;
            $("html, body").animate({ scrollTop: position - 50 }, "slow");

            var promise = ajax.call([{
                methodname: 'tool_supporter_get_course_info',
                args: {
                    courseID: course_id
                }
            }]);

            promise[0].done(function(data){
                console.log("Show course detail Returns: ");
                console.log(data);
                // Render template with data.
                templates.render('tool_supporter/course_detail', data).done(function(html, js) {
                    $('[data-region="course_details"]').replaceWith(html);
                    $('[data-region="course_details"]').show();
                    // And execute any JS that was in the template.
                    templates.runTemplateJS(js);

                    // If a user is selected.
                    if ($('#selecteduserid').length === 1) {
                        show_enrol_section(course_id);
                    }

                }).fail(notification.exception);
            }).fail(notification.exception);
            if(boolreturn){
                return promise;
            }
        },

        /**
         * toggle course visibility on and off
         *
         * @method toggle_course_visibility
         */
        toggle_course_visibility: function() {
            // Both are needed because of different ids.
            $('#hide_course_visibility').on('click', function() {
                toggle_course_visibility_private();
            });
            $('#show_course_visibility').on('click', function() {
                toggle_course_visibility_private();
            });
        },

        /**
         * Toggles the user block
         *
         * @method toggle_user_details
         */
        toggle_user_details: function() {
            $('#btn_hide_user_details, #btn_show_user_details').on('click', function() {
                $('#user_details_body').toggle();
                $('#btn_hide_user_details').toggle();
                $('#btn_show_user_details').toggle();
            });
        },

        /**
         * Toggles the course detail block
         *
         * @method toggle_course_detail
         */
        toggle_course_details: function() {
            $('#btn_hide_course_details, #btn_show_course_details').on('click', function() {
                $('#course_details_body').toggle();
                $('#btn_hide_course_details').toggle();
                $('#btn_show_course_details').toggle();
            });
        },

        /**
         * Get the details of the user and displays them
         *
         * @method click_on_user
         * @param tableID Id of datatable. Example: '#{{uniqid}}-courseTable tbody'.
         */
        click_on_user: function(tableID) {
            $(tableID + ' tbody').on('click', 'tr', function() { // Click event on each row.

                // Get id (first column) of clicked row.
                var user_id = $(this).find('td:first-child').text();

                if (!isNaN(user_id)) {
                    // Remove previous hightlights.
                    var dataTable = $(tableID).dataTable()[0];
                    $(dataTable.rows).css("background-color", "");
                    // Highlight the clicked user.
                    $(this).css("background-color", "#bcbcbc");

                    // Go to top.
                    var position = $("#user_details").offset().top;
                    $("html, body").animate({ scrollTop: position - 50}, "slow");

                    var promises = ajax.call([{
                        methodname: 'tool_supporter_get_user_information',
                        args: {
                            userid: user_id
                        }
                    }]);

                    promises[0].done(function(data) {
                        console.log("click on user Returns: ");
                        console.log(data);
                        templates.render('tool_supporter/user_detail', data[0]).done(function(html, js) {
                            $('[data-region="user_details"]').replaceWith(html);
                            $('[data-region="user_details"]').show();

                            // Only show the section if a course is selected.
                            if ($('[data-region="course_details"]').is(':visible')) {
                                var courseid = $('#selectedcourseid')[0].textContent;
                                show_enrol_section(courseid);
                            }

                            templates.runTemplateJS(js);
                        }).fail(notification.exception);
                    }).fail(notification.exception);
                }
            });
        },

        /**
         * Helper function for jquery event
         *
         * @method click_on_user
         */
        click_on_course: function(tableID) {
            var public_object = this;
            $(tableID + ' tbody').on('click', 'tr', function() { // Click event on each row.

                var course_id = $(this).find('td:first-child').text(); // Get id (first column) of clicked row.
                if (!isNaN(course_id)) {
                    // Remove previous hightlights.
                    var dataTable = $(tableID).dataTable()[0];
                    $(dataTable.rows).css("background-color", "");
                    // Highlight the clicked course.
                    $(this).css("background-color", "#bcbcbc");

                    // Show details of this course.
                    public_object.show_course_detail(course_id, false);
                }
            });
        },

        click_on_refresh: function(tableID, methodname, args) {
            // For users table.
            $('#btn_refresh_users').on('click', function() {
                var promises = ajax.call([{
                    "methodname": methodname,
                    "args": args
                }]);
                promises[0].done(function(data) {
                    console.log("return data for refreshing a user");
                    console.log(data);
                    templates.render('tool_supporter/user_table', data).done(function(html, js) {
                        $('[data-region="user_table"]').replaceWith(html);
                        templates.runTemplateJS(js);
                    }).fail(notification.exception);
                }).fail(notification.exception);
            });

            // For courses table.
            $('#btn_refresh_courses').on('click', function() {
                var promises = ajax.call([{
                    "methodname": methodname,
                    "args": args
                }]);
                promises[0].done(function(data) {
                    console.log("return data for refreshing a course");
                    console.log(data);
                    templates.render('tool_supporter/course_table', data).done(function(html, js) {
                        $('[data-region="course_table"]').replaceWith(html);
                        templates.runTemplateJS(js);
                    }).fail(notification.exception);
                }).fail(notification.exception);
            });
        }
    };

    // Alias module:tool_supporter/load_information.
    return  public;
});
