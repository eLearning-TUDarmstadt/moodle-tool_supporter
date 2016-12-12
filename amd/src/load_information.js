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
        * Get Users Details
        *
        * @method click_on_user
        */
       click_on_user: function(table) {
           $(table + ' tr').on('click', function() { //click event on each row
             var user_id = $(this).find('td:first-child').text(); //get id (first column) of clicked row
             console.log("Reihe geklickt, User-ID gefunden: " + user_id);

             //core_enrol_get_users_courses

             var promises = ajax.call([{
               methodname: 'tool_supporter_get_users_courses',
                 //methodname: 'core_enrol_get_users_courses',
                 // Im Dashboard nachschauen, wie die das dort gemacht haben
                 args: {
                   user_id: user_id
                 }
             }]);

             promises[0].done(function(data) {
               console.log("promise is done with return data: ")
               console.log(data);
             }).fail(notification.exception);

           });
       },

       /**
        * Get course details
        *
        * @method click_on_user
        */
       click_on_course: function(table) {
           $(table + ' tr').on('click', function() { //click event on each row
             var course_id = $(this).find('td:first-child').text(); //get id (first column) of clicked row
             console.log("Reihe geklickt, Kurs-ID gefunden: " + course_id);
            var promise = ajax.call([{
            	methodename: 'tool_supporter_get_course_info',           	 
            	args: {courseID: course_id}              	
             }]);
            console.log("in promises")
             promise[0].done(function(data){
                 // Render template with data
                 templates.render('tool_supporter/course_detail', data).done(function(html, js) {
                     $('[data-region="course_details"]').replaceWith(html);
                     // And execute any JS that was in the template.
                     templates.runTemplateJS(js);
                 }).fail(notification.exception);
            	 
             }).fail(notification.exception);
           });
       }
   };
});
