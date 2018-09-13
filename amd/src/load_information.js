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
        }]);
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

    // Private function which can be referenced from the public click_on_course function.
    var show_course_detail_private = function(courseID) {
        public.show_course_detail(courseID);
    };

    // Toggling course visibility on and off.
    var toggle_course_visibility_private = function() {
        var promises = ajax.call([{
            methodname: 'tool_supporter_toggle_course_visibility',
            args: {
                courseID: $('#selectedcourseid')[0].textContent
            }
        }]);

        promises[0].done(function(course) {
            console.log("toggle visibility return data");
            console.log(course.courseDetails.visible);

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
            if(boolreturn === 'undefined') { boolreturn = 0 ;}
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
         * hide the user block
         *
         * @method hide_user_details
         */
        hide_user_details: function() {
            $('#btn_hide_user_details').on('click', function() {
                $('#user_details_body').hide();
                $('#btn_hide_user_details').hide();
                $('#btn_expand_user_details').show();
            });
        },

        /**
         * expand the user block after it was hidden
         *
         * @method expand_user_details
         */
        expand_user_details: function() {
            $('#btn_expand_user_details').on('click', function() {
                $('#user_details_body').show();
                $('#btn_hide_user_details').show();
                $('#btn_expand_user_details').hide();
            });
        },

        /**
         * hide the course detail block
         *
         * @method hide_course_detail
         */
        hide_course_detail: function() {
            $('#btn_hide_course_details').on('click', function() {
                $('#course_details_body').hide();
                $('#btn_hide_course_details').hide();
                $('#btn_expand_course_details').show();
            });
        },

        /**
         * expands the course detail block again after it was hidden
         *
         * @method expand_course_detail
         */
        expand_course_detail: function() {
            $('#btn_expand_course_details').on('click', function() {
                $('#course_details_body').show();
                $('#btn_hide_course_details').show();
                $('#btn_expand_course_details').hide();
            });
        },

        /**
         * Get the details of the user and displays them
         *
         * @method click_on_user
         * @param table Id of datatable. Example: '#{{uniqid}}-courseTable tbody'.
         */
        click_on_user: function(table) {
            $(table + ' tbody').on('click', 'tr', function() { // Click event on each row.
                var user_id = $(this).find('td:first-child').text(); // Get id (first column) of clicked row.

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
                    data = data[0];
                    templates.render('tool_supporter/user_detail', data).done(function(html, js) {
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

            });
        },

        /**
         * Helper function for jquery event
         *
         * @method click_on_user
         */
        click_on_course: function(table) {
            $(table + ' tbody').on('click', 'tr', function() { // Click event on each row.
                var course_id = $(this).find('td:first-child').text(); // Get id (first column) of clicked row.
                show_course_detail_private(course_id);
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
        },

        search: function(tableID, columnDropdownID, searchFieldID, columns) {
            // Initialize Dropdown - add other options than "all".
            var counter = 0;
            columns.forEach(function(element) {
                $(columnDropdownID).append($('<option>', {
                    value: counter,
                    text : element.name
                }));
                counter++;
            });

            // Apply Filter when user is typing.
            $(searchFieldID).on('keyup', function(){
                var otable = $(tableID).dataTable();
                var searchValue = $(searchFieldID)[0].value;
                var column = $(columnDropdownID)[0].value;

                if (column == "-1") {
                    otable.fnFilter(searchValue, null); // Search all columns.
                } else {
                    otable.fnFilter(searchValue, column, true, false, false, true); // Search a specific column.
                }
            });
        }

    };

    // Alias module:tool_supporter/load_information.
    return  public;
});
