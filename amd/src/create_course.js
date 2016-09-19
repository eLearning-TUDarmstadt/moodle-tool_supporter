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
 * @module     tool_supporter/create_course
 * @package    tool_supporter
 * @copyright  2016 Benedikt Schneider
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
define(['jquery', 'core/ajax', 'core/templates', 'core/notification'], function($, ajax, templates, notification) {
    return /** @alias module:tool_supporter/create_course */ {

        /**
         * Create a course
         *
         * @method create_course
         */
        create_course: function() {
            // Add a click handler to the button.
            $('#create_course_button').on('click', function() {
                console.log("Der Button wurde geklickt!")
                var shortname = $('#full_name_input').attr('value');
                var fullname = $('#short_name_input').attr('value');
                console.log("Kurzer Name: " + shortname);
                console.log("Voller Name: " + fullname);

                var promises = ajax.call([{
                  // Hier muss ein eigener Webservice in externallib geschrieben werden, den man von AJAX aus abrufen kann
                    /**
                    methodname: 'webservice_external_course_create_courses',
                    args:{"fullname": "fullname", "shortname": "shortname", "categoryid": 0}
                    */
                    methodname: 'tool_supporter_create_course',
                    args: {
                      shortname: shortname,
                      fullname: fullname,
                    }
                }]);

                promises[0].done(function(data) {
                  console.log("promise is done with return id: " + data)
                    // We have the data - lets re-render the template with it.

                    /*
                    !!! Hier muss später das Template rein, mit dem man einen ausgewählten Kurs anzeigen lassen kann, z.B. "Show course(id)"

                    templates.render('tool_supporter/user_table', data).done(function(html, js) {
                      console.log("Return id: " + html);

                        $('[data-region="create_course"]').replaceWith(html);
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
