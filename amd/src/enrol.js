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
 * This is an empty module, that is required before all other modules.
 * Because every module is returned from a request for any other module, this
 * forces the loading of all modules with a single request.
 *
 * @module     tool_supporter/create_new_course
 * @package    tool_supporter
 * @copyright  2016 Benedikt Schneider
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
define(['jquery', 'core/ajax', 'core/templates', 'core/notification'], function($, ajax, templates, notification) {
    return /** @alias module:tool_supporter/create_new_course */ {

        /**
         * Create a course
         *
         * @method create_new_course
         */
        enroluserintocourse: function() {
            $('#enroluserintocoursebutton').on('click', function() {
              console.log("Button wurde geklickt");
              console.log("course id: ");
              console.log($('#selectedcourseid')[0].textContent);
              var course = $('#selectedcourseid')[0].textContent;
              var user = $('#selecteduserid')[0].textContent;
              console.log("user id: ");
              console.log($('#selecteduserid')[0].textContent);

              console.log("id of role:");
              console.log($('#role-dropdown')[0].value);

              if ($('#role-dropdown')[0].value == "") {
                alert("Es wurde keine Rolle ausgewählt"); //ToDo: English translation
              } else {
                var promises = ajax.call([{
                    methodname: 'tool_supporter_enrol_user_into_course',
                    args: {
                      userid: $('#selecteduserid')[0].textContent,
                      courseid: $('#selectedcourseid')[0].textContent,
                      roleid: $('#role-dropdown')[0].value
                    }
                }]);

                promises[0].done(function(data) {
                  console.log("promise is done with return data: ")
                  console.log(data);

                }).fail(notification.exception);
              }
            });
        }
    };
});
