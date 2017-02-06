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

                /*
                var shortnameinput = $('#new_course_full_name_input')[0].value;
                var fullname = $('#new_course_short_name_input')[0].value;
                var visible = $("#new_course_is_visible").is(":checked");
                console.log("Kurzer Name: " + shortnameinput);
                console.log("Voller Name: " + fullname);
                console.log("Sichtbar: " + visible);
                */

                var promises = ajax.call([{
                    methodname: 'tool_supporter_create_new_course',
                    args: {
                      shortname: $('#new_course_full_name_input')[0].value,
                      fullname: $('#new_course_short_name_input')[0].value,
                      visible: $("#new_course_is_visible").is(":checked"),
                      categoryid: $('#new_course_category_input')[0].value
                    }
                }]);

                promises[0].done(function(data) {
                  // stdClass with category, fullname, id, shortname, startdate, timecreated, timemodified, visible
                  console.log("promise is done with return data: ")
                  console.log(data);
                  alert("Der Kurs mit der ID " + data['id'] + " wurde erstellt!");
                  var courseDetails = data;
                    // We have the data of the course. Now it has to be displayed
                    templates.render('tool_supporter/course_detail', courseDetails).done(function(html, js) {
                      console.log("Return id: " + html);
                        $('[data-region="course_details"]').replaceWith(html);
                        $('[data-region="course_details"]').show();
                        // And execute any JS that was in the template.

                        //JS: Show course which was created
                        templates.runTemplateJS(js);
                        // reload cours table?
                    }).fail(notification.exception);
                }).fail(notification.exception);
            });
        }
    };
});
