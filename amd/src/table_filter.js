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
 * This modules provides functionality to search the html tables
 * with a specific Input Term
 *
 * It is modular in respect to the given table (body)
 *
 * @module     tool_supporter/table_filter
 * @package    tool_supporter
 * @copyright  2017 Klara Saary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1.1
 */
define(['jquery', 'tool_supporter/datatables'], function($, dataTablesjs) {

//bsp exact search: https://stackoverflow.com/questions/33122994/how-to-search-for-an-exact-string-in-a-jquery-datatable

    var filterTable = function(elements, otable, column){
      filterElements ='';
      string_value = '';
      $(elements).each(function(index){
        if($(this).val() == ""){string_value = '^(?![\\s\\S])'} else string_value = '\\b' + $(this).val();
        if (index  == elements.length -1){filterElements = filterElements +  string_value}
        else
          filterElements = filterElements  + string_value + '|'; // Add value of elements[index] and add "|" as an OR and add "\b" to only accept matches which starts with the string
      });
      otable.fnFilter(filterElements, column, true, false, false, true);
    };


    return /** @alias module:tool_supporter/table_filter */ {

        /**
         * Filtering the table with the appropiate form!
         *
         * @method FilterEvent
         * @param searchInputID, tableID
         * @param tableID: ID of the table or part of the table you want to filter
         * @param FormInput: The selected Filtering Term
         * @param column: which column should be filtered
         */
        filterEvent: function(searchInputID, FormInput, column, tableID) {
          //for radios
          var otable = $(tableID).dataTable();
          $(FormInput).change(function() {
            var elements = $('input[name='+searchInputID+']:checked');
            filterTable(elements, otable, column);
          });
        }
      };
  });
