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
define(['jquery', 'tool_supporter/jquery.dataTables', 'core/str', 'tool_supporter/table_filter', 'core/ajax', 'core/notification', 'core/templates'], 
        function($, datatables, str, filter, ajax, notification, templates) {

  
    return /** @alias module:tool_supporter/datatables */ {

      /**
       *
       * @method use_dataTable
       * @param tableID : ID of table you want to convert into datatable
       * @param filterSelector : Arrays with information for the function filterEvent in table_filter.js. Every array has three parameters: 
       * There can be several filterSelectors, for example one for each dropdown-menue 
       */
       use_dataTable: function(tableID, filterSelector){
        var args = arguments;
        str.get_string('search', 'moodle').done(function(searchString) {
          $(tableID).DataTable({
            "retrieve": true, //So the table can be accessed after initialization
            "responsive": true,
            "lengthChange": true,
            "pageLength": 30,
            "language": {
              //Empty info. Legacy: Showing page _PAGE_ of _PAGES_
              'info': "",
              'search': searchString+": ",
              'lengthMenu': "_MENU_"
            },
            "dom": "<'w-100'<'col'f>>" +
              "<'w-100'<'col't>>" +
              "<'w-100'<'col-sm-3'i><'col-sm-6'p><'col-sm-3'l>>",
            /*"dom": '<f>t<ipl>',*/
            "paging": true,
            "pagingType": "numbers",
            "scrollX": "true"
          });

          var i;
          for(i=1; i < args.length; i++){
            if(args[i]){
            filter.filterEvent(args[i][0], args[i][1], args[i][2], tableID);
            }
          };
        });
       },
    
          /**
       *
       * @method dataTable_ajax
       * @param tableID : ID of table you want to convert into datatable
       */
    dataTable_ajax: function(tableID, methodname, args, datainfo, columns){
        
         var promise = ajax.call([{
                "methodname": methodname,
                "args": args
            }]);
   
        var otable;

        promise[0].done(function(data) {
          
          str.get_string('search', 'moodle').done(function(searchString) {
             otable = $(tableID).DataTable( {
                 "data": data[datainfo],
                 "columns":columns, 
                 "retrieve": true, //So the table can be accessed after initialization
                 "responsive": true,
                 "lengthChange": true,
                 "pageLength": 30,
                 "language": {
                     //Empty info. Legacy: Showing page _PAGE_ of _PAGES_
                      'info': " ",
                      'search': searchString+": ",
                      'lengthMenu': "_MENU_"
                     },
                  "dom": "<'row'<'col-sm-6'><'col-sm-6'f>>" +
                        "<'row'<'col-sm-12't>>" +
                        "<'row'<'col-sm-3'i><'col-sm-6 center-block'p><'col-sm-3 center-block'l>>",
                /*"dom": '<f>t<ipl>',*/
                 "paging": true,
                 "pagingType": "numbers",
                 "lengthMenu": [ 10, 25, 50, 75, 100 ],
                 "scrollX": true
             });
         });
      }).fail(notification.exception);
    }
  };
});