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
 * @module     tool_supporter/load_information
 * @package    tool_supporter
 * @copyright  2016 Benedikt Schneider
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
define(['jquery', 'core/ajax', 'core/templates', 'core/notification'], function($, ajax, templates, notification) {
   return /** @alias module:tool_supporter/load_information */ {

     /**
      * hide the user-block
      * @method hide_user
      */
     hide_user: function() {
         $('#hide_user').on('click', function() {
           console.log("hide user");
             $('[data-region="user_details"]').toggle();
         });
     },

     /**
      * hide the course detail block
      * @method hide_course_detail
      */
     hide_course_detail: function() {
         $('#hide_course_details').on('click', function() {
             $('[data-region="course_details"]').toggle();
             $('#enroluserintocoursebutton').hide();
         });
     },

       /**
        * Get Users Details
        *
        * @method click_on_user
        */
       click_on_user: function(table) {
           $(table + ' tr').on('click', function() { //click event on each row
             var user_id = $(this).find('td:first-child').text(); //get id (first column) of clicked row

             var promises = ajax.call([{
               methodname: 'tool_supporter_get_user_information',
                 args: {
                   userid: user_id
                 }
             }]);

             promises[0].done(function(data) {
               data = data[0];
               console.log(data);
               templates.render('tool_supporter/user_detail', data).done(function(html, js) {
                 $('[data-region="user_details"]').replaceWith(html);
                 $('[data-region="user_details"]').show();
                 console.log("data:");
                 console.log(data);
                 // And execute any JS that was in the template.
                 templates.runTemplateJS(js);
               }).fail(notification.exception);
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
            	methodname: 'tool_supporter_get_course_info',
            	args: {
                courseID: course_id
              }
             }]);
             promise[0].done(function(data){
                 // Render template with data
                 console.log(data);
                 templates.render('tool_supporter/course_detail', data).done(function(html, js) {
                     $('[data-region="course_details"]').replaceWith(html);
                     $('[data-region="course_details"]').show();
                     console.log("Rollen im Kurs:");
                     console.log(data['assignableRoles']);
                     console.log(data['assignableRoles'][0].id)
                     //$('#enroluserintocoursebutton').show();

                     templates.render('tool_supporter/enrolusersection', data).done(function(html, js) {
                       console.log("html:");
                       console.log(html);
                       //enroluserregion
                       $('[data-region="enroluserregion"]').replaceWith(html);
                       $('[data-region="enroluserregion"]').show();
                       console.log($('enrolusersection').show());
                       console.log("Rendern---");
                       templates.runTemplateJS(js);
                     }).fail(notification.exception);

                     // And execute any JS that was in the template.
                    templates.runTemplateJS(js);
                 }).fail(notification.exception);

             }).fail(notification.exception);
           });
       }
   };
});
