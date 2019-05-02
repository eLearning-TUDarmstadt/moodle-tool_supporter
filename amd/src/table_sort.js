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
 * This modules provides functionality to sort the tables
 *
 * @module     tool_supporter/table_filter
 * @package    tool_supporter
 * @copyright  2019 Klara Saary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1.1
 */
define(['jquery'], function($) {

    // Sorting the clicked column in a specific table.
    var tableSort = function(column, tableID) {
        // Using W3School-Code , modified.
        var rows;
        var i;
        var x;
        var y;
        var shouldSwitch;
        var dir;
        var switchcount = 0;
        var switching = true;
        // Set the sorting direction to ascending.
        dir = "asc";
        // Make a loop that will continue until no switching has been done.
        while (switching) {
            // Start by saying: no switching are done.
            switching = false;
            rows = $(tableID + ' tr');
            // Loop through all table rows (except the first, which contains table headers).
            for (i = 1; i < (rows.length - 1); i++) {
                // Start by saying there should be no switching.
                shouldSwitch = false;
                // Get the two elements you want to compare,one from current row and one from the next.
                x = rows[i].getElementsByTagName('td')[column];
                y = rows[i + 1].getElementsByTagName('td')[column];
                // Check if the two rows should switch place, based on the direction, asc or desc.
                if (dir == "asc") {
                    if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                        // If so, mark as a switch and break the loop.
                        shouldSwitch = true;
                        break;
                    }
                } else if (dir == "desc") {
                    if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                        // If so, mark as a switch and break the loop.
                        shouldSwitch = true;
                        break;
                    }
                }
            }
            if (shouldSwitch) {
                // If a switch has been marked, make the switch and mark that a switch has been done.
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
                // Each time a switch is done, increase this count by 1.
                switchcount++;
            } else {
                // If no switching has been done AND the direction is "asc", set the direction to "desc".
                if (switchcount === 0 && dir == "asc") {
                    dir = "desc";
                    switching = true;
                }
            }
        }
    };

    return /** @alias module:tool_supporter/sortTable */ {
        /**
         * Jquery helper function for sorting the table.
         *
         * @param {string} tableID
         */
        sortTable: function(tableID) {
            $(tableID + ' th').on('click', function() {
                var index = $(this).parent().children().index($(this));
                tableSort(index, tableID);
            });
        }
    };
});
