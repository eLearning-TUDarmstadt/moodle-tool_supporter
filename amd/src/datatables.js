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
define(['jquery', 'tool_supporter/jquery.dataTables'], function($, datatables) {

  return /** @alias module:tool_supporter/table_sort */ {

    /**
     *
     * @method use_dataTable
     */
     use_dataTable: function(tableID){
      // $(document).ready(function() {
      $(tableID).DataTable({
        "lengthChange": false,
        "pageLength": 50,
        "language": {
          'info': "Showing page _PAGE_ of _PAGES_",
          'search': ''
        },
        "dom": "<'w-100'<'col'f>>" +
          "<'w-100'<'col't>>" +
          "<'w-100'<'col-sm-6'i><'col-sm-6'p>>",
        "paging": true,
        "pagingType": "numbers",
      });
      // });
    },

    /**
     *
     * @method use_dataTable_tab
     */
    use_dataTable_tab: function(tableID){ //Does not work yet
      console.log("aktuelle tableID:" + tableID + " alle datatables: ");
      console.log($.fn.dataTable.tables());
      var otable = $(tableID).DataTable({
          scrollY:        200,
          scrollCollapse: true,
          paging:         false
      });
           console.log("in use_dataTable_tab");
           $('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
                 console.log( 'show tab' );
                 otable
                   .columns.adjust()
                   .responsive.recalc();
               });
      }
   };
});
