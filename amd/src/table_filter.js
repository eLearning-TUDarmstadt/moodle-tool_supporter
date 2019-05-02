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
 * @copyright  2019 Klara Saary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1.1
 */
define(['jquery'], function($) {

    var filterTable = function(checkedElements, otable, column) {

        var filterElements = [];
        var stringValue = '';
        $(checkedElements).each(function() {
            var val = $(this).val();
            if (val === "") {
                stringValue = '^(?![\\s\\S])';
            } else {
                // Escape Regex-Characters which may be in names of categories.
                val = val.replace(/[-[\]{}()*+!<=:?.\/\\^$|#\s,]/g, '\\$&');
                // String value is added several times with different starts and endings.
                // So filter for "Teacher" does not match "non-editing teacher".
                stringValue = ',' + val + '$|^' + val + ',|,' + val + ',|^' + val + '$';
            }
            filterElements.push(stringValue);
        });
        var filter = filterElements.join("|");
        otable.fnFilter(filter, column, true, false, false, true);
    };

    return /** @alias module:tool_supporter/table_filter */ {

        /**
         * Filtering the table with the appropriate form!
         *
         * @method FilterEvent
         * @param {string} checkboxName Name of the checkboxes that are used to filter.
         * @param {number} FormInput The ID of the dropdownmenu or something similar you want to use to filter the table
         * @param {number} column which column should be filtered
         * @param {string} tableID ID of the table or part of the table you want to filter
         */
        filterEvent: function(checkboxName, FormInput, column, tableID) {
            $(FormInput).change(function() {
                if (tableID === '#courseTable') {
                    $('#courses_clear_filters').css('visibility', 'visible'); // Show "clear filter"-Button.
                }

                var checkedElements = $('input[name=' + checkboxName + ']:checked');
                var otable = $(tableID).dataTable();
                filterTable(checkedElements, otable, column);
            });
        },

        searchTable: function(tableID, columnDropdownID, searchFieldID, columns) {
            // Initialize Dropdown - add other options than "all".
            var counter = 0;
            columns.forEach(function(element) {
                $(columnDropdownID).append($('<option>', {
                    value: counter,
                    text: element.name
                }));
                counter++;
            });

            /**
             * Filter the table
             */
            function actuallySearch() {
                if (tableID === '#courseTable') {
                    $('#courses_clear_filters').css('visibility', 'visible'); // Show "clear filter"-Button.
                }

                var otable = $(tableID).dataTable();
                var searchValue = $(searchFieldID)[0].value;
                var columnID = $(columnDropdownID)[0].value;
                if (columnID == "-1") {
                    $(tableID).DataTable().search(searchValue).draw(); // Search all columns.
                } else {
                    otable.fnFilter(searchValue, columnID, true, true, false, true); // Search a specific column.
                }
            }

            // Apply Filter when user is typing.
            $(searchFieldID).on('keyup', actuallySearch);

            var previousColumn;

            // Safe last column when dropdown is clicked.
            $(columnDropdownID).on('click', function() {
                previousColumn = this.value;
            });

            // Clear previous search and apply new search.
            $(columnDropdownID).on('change', function() {
                $(tableID).DataTable().column(previousColumn).search("");
                actuallySearch();
            });
        },

        coursesClearFilters: function(tableID) {
            $('#courses_clear_filters').on('click', function() {
                $(tableID).DataTable().search('').columns().search('').draw();
                $('#course_table_search_input')[0].value = '';
                $('input[name^=courses_level]:checked').prop("checked", false); // Uncheck all checked Boxes.
                $(this).css('visibility', 'hidden');
            });
        },

    };
});
