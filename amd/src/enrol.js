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
 * This Module provides a function to enrol a user into a course
 *
 * @module     tool_supporter/create_new_course
 * @package    tool_supporter
 * @copyright  2019 Benedikt Schneider
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1.1
 */
define(['jquery', 'core/ajax', 'core/templates', 'core/notification'], function($, ajax, templates, notification) {
    return /** @alias module:tool_supporter/create_new_course */ {

        /**
         * Enrol a user into a course
         *
         * The user and the course are selected and their ids are stored in the fields with the ids
         * selectedcourseid and selecteduserid
         *
         * @method enroluserintocours
         */
        enrolUserIntoCourse: function() {
            $('#enroluserintocoursebutton').on('click', function() {

                var promises = ajax.call([{
                    methodname: 'tool_supporter_enrol_user_into_course',
                    args: {
                        userid: $('#selecteduserid')[0].textContent,
                        courseid: $('#selectedcourseid')[0].textContent,
                        roleid: $('#role-dropdown')[0].value
                    }
                }], true, true);

                promises[0].done(function(course) {

                    // Re-render the template to show the changes.
                    templates.render('tool_supporter/course_detail', course).done(function(html, js) {
                        $('[data-region="course_details"]').replaceWith(html);
                        $('[data-region="course_details"]').show();
                        templates.runTemplateJS(js);
                    }).fail(notification.exception);

                }).fail(notification.exception);
            });
        }
    };
});
