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
define(['jquery'], function($) {

//Hides all rows, which don't match with the search input. The search-function is case insensitive and also recognizes inner word parts
    var search = function(element, tableID){
      var rows = $(tableID + ' tr');
      var val = $.trim(element).replace(/ +/g, ' ').toLowerCase().split(' ');
      rows.hide().filter(function() {
        var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
          var matchesSearch = true;
          $(val).each(function(index, value) {
            matchesSearch = (!matchesSearch) ? false : ~text.indexOf(value);
          });
          return matchesSearch;
        }).show();
    };

    var filterTable = function(elements, otable, column){
      filter ='';
      $(elements).each(function(index){
        console.log("index: " + index + "length: " + elements.length);
        if(index  == elements.length -1){filter = filter + $(this).val()}
        else
          filter = filter + $(this).val() + '|';
      });
      otable.fnFilter(filter, column, true, false, false, true);
    };


    return /** @alias module:tool_supporter/table_search */ {

          /**
           * Refresh the table!
           *
           * @method userSearchEvent
           * @param searchInputID, tableID
           * searchInputID: ID of searchfield
           * tableID: ID of the table or part of the table you want to filter
           */
          searchEvent: function(searchInputID, tableID, FormInput) {
            //for input texts
            $(searchInputID).keyup(function() {
              search($(searchInputID).val(),tableID);
            });
          },

          /**
           * Refresh the table!
           *
           * @method FilterEvent
           * @param searchInputID, tableID
           * searchInputID: ID of searchfield
           * tableID: ID of the table or part of the table you want to filter
           */
          filterEvent: function(searchInputID, tableID, FormInput, column) {
            //for radios
            var otable = $(tableID).dataTable();
            $(FormInput).change(function() {
              console.log("checkboxes_changed");
              console.log($('input[name='+searchInputID+']:checked'));
              var elements = $('input[name='+searchInputID+']:checked');
              filterTable(elements, otable, column);
            });
        }
      };
  });
