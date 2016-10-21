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
                      categoryid: 2
                    }
                }]);

                promises[0].done(function(data) {
                  console.log("promise is done with return data: ")
                  console.log(data);
                  alert("Der Kurs mit der ID " + data + "wurde erstellt!");
                    // We have the data - lets re-render the template with it.

                    /*
                    !!! Hier muss später das Template rein, mit dem man einen ausgewählten Kurs anzeigen lassen kann, z.B. "Show course(id) !!!!"

                    templates.render('tool_supporter/user_table', data).done(function(html, js) {
                      console.log("Return id: " + html);

                        $('[data-region="create_new_course"]').replaceWith(html);
                        // And execute any JS that was in the template.

                        //JS: select course which was created
                        templates.runTemplateJS(js);

                    }).fail(notification.exception);

                    */
                }).fail(notification.exception);
            });
        }
    };
});
