/*jslint vars: true, plusplus: true, devel: true, nomen: true, indent: 4, maxerr: 50 */
/*global define */
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
 * @module     tool_supporter/table_search
 * @package    tool_supporter
 * @copyright  2016 Klara Saary <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
//define(['jquery', 'core/ajax', 'core/templates', 'core/notification'], function($, ajax, templates, notification) {
define(['jquery'], function($) {
  /*
    var users;

    var getUser = function(){
      var d = $.Deferred();
      var promises = ajax.call([{
				methodname: 'tool_supporters_get_users',
				args: { },
				fail: function (e) {
					//notification.exception;
					console.log(e);
					d.reject();
				}
      }]);

      promises[0].done(function(data){
        users = data;
      });
      return d.promises();
    };



    var render = function(data){
      var d = $.Deferred();
      templates.render('tool_supporter/user_table', data).done(function(html, js) {
          $('[data-region="user_table"]').replaceWith(html);
          // And execute any JS that was in the template.
          templates.runTemplateJS(js);
        }).fail(notification.exception);
    };
*/


    var search = function(){
      console.log("Hello_search");
      var $rows = $('#body tr');
      //$('#searchUserInput').keyup(function() {
          var val = '^(?=.*\\b' + $.trim($(this).val()).split(/\s+/).join('\\b)(?=.*\\b') + ').*$',
                reg = new RegExp(val, 'i'),
                text;

            $rows.show().filter(function() {
                 text = $(this).text().replace(/\s+/g, ' ');
                 return !reg.test(text);
            }).hide();
      //  });
    };


    return /** @alias module:tool_supporter/table_search */ {
          /**
           * Refresh the table!
           *
           * @method userSearchEvent
           */
          userSearchEvent: function() {
            //var $rows = $('#table tr');
            $('#userSearchInput').keyup(function() {
              console.log("In keyup Event");
              search();
            });
          }
        };
  });
