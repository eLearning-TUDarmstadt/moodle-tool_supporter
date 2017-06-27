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
 * This module provides setttings and implements
 * the dataTabes Plugin
 *
 * @module     tool_supporter/datatables
 * @package    tool_supporter
 * @copyright  2017 Klara Saary, Benedikt Schneider
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1.1
 */
define(['jquery', 'tool_supporter/jquery.dataTables', 'core/str', 'tool_supporter/table_filter'], function($, datatables, str, filter) {

  return /** @alias module:tool_supporter/datatables */ {

      /**
       *
       * @method use_dataTable
       */
       use_dataTable: function(tableID, filterSelector){ //There can be several filterSelectors, for example one for each dropdown-menue
        var args = arguments;
        str.get_string('search', 'moodle').done(function(searchString) {
          $(tableID).DataTable({
            "retrieve": true, //So the table can be accessed after initialization
            "responsive": true,
            "lengthChange": false,
            "pageLength": 30,
            "language": {
              //Empty info. Legacy: Showing page _PAGE_ of _PAGES_
              'info': "",
              //'search': searchString+": "
            },
            "dom": "<'w-100'<'col'f>>" +
              "<'w-100'<'col't>>" +
              "<'w-100'<'col-sm-6'i><'col-sm-6'p>>",
            "paging": true,
            "pagingType": "numbers",
            "scrollX": true
          });
          for(i=1; i < args.length; i++){
            if(args[i]){
            filter.filterEvent(args[i][0], args[i][1], args[i][2], tableID);
            }
          };
          $('[data-region="course_table"]').show();
          $('[data-region="user_table"]').show();
        });
    },
  };
});
