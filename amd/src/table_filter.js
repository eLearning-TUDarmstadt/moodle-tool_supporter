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
define(['jquery'], function($) {

    var filterTable = function(elements, otable, column){
        var filterElements = [];
        var string_value = '';
        $(elements).each(function(index){
            var val = $(this).val();
            if(val === ""){string_value = '^(?![\\s\\S])';}
            //String value is added several times with different beginings and endings so filter for i.e. "Teacher" does not match "non-editing teacher"
            else {string_value = ',' + val + '$|^' + val + ',|,' + val + ',|^' + val + '$';} 
            filterElements.push(string_value);
        });
        filterElements.join("|");
        otable.fnFilter(filterElements, column, true, false, false, true);
    };

    return /** @alias module:tool_supporter/table_filter */ {

        /**
         * Filtering the table with the appropiate form!
         *
         * @method FilterEvent
         * @param searchInputName : Name of the input fields you want to use as filter parameters. All fields have to have the same name. Here: The dropdown menu entries
         * @param tableID : ID of the table or part of the table you want to filter
         * @param FormInput : The ID of the dropdownmenu or something similiary you want to use to filter the table
         * @param column : which column should be filtered
         */
        filterEvent: function(searchInputName, FormInput, column, tableID) {
            // For Radio Buttons.
            var otable = $(tableID).dataTable();
            $(FormInput).change(function() {
                var elements = $('input[name=' + searchInputName + ']:checked');
                filterTable(elements, otable, column);
            });
        }
    };
});
