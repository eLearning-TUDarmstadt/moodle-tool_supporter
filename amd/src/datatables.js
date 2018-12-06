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
define(['jquery', 'tool_supporter/jquery.dataTables', 'core/str', 'tool_supporter/table_filter',
        'core/ajax', 'core/notification', 'core/templates'],
function($, datatables, str, filter, ajax, notification, templates) {

    var use_filters = function(tableID, filterSelector){
        // Only execute if there are filters.
        if (typeof filterSelector != "undefined") {
            // Set filters for every dropdown-menu.
            for(var i = 0; i < filterSelector.length; i++){
                // Params: checkbox, FormInput, column, tableID.
                filter.filterEvent(filterSelector[i][0], filterSelector[i][1], filterSelector[i][2], tableID);
            }
        }
    };

    return /** @alias module:tool_supporter/datatables */ {

        /**
         * @method use_dataTable
         * @param tableID: ID of table you want to convert into datatable
         * @param filterSelector: Array for filtering with the dropdown-menues
         * There can be several filterSelectors, for example one for each dropdown-menue
         */
        use_dataTable: function(tableID, filterSelector){

            var promises = ajax.call([{
                methodname: 'tool_supporter_get_settings',
                args: {}
            }]);

            promises[0].done(function(settings) {

                var string_noresults = str.get_string('noresults', 'admin');
                var string_search = str.get_string('search', 'moodle');

                $.when(string_noresults, string_search).done(function (noresultsString, searchString) {
                    // Standard settings for every "details"-table.
                    var options = {
                        "retrieve": true,   // So the table can be accessed after initialization.
                        "responsive": true,
                        "lengthChange": true,
                        "language": {
                            // Empty info. Legacy: Showing page _PAGE_ of _PAGES_ .
                            'info': "",
                            'search': searchString + ": ",
                            'lengthMenu': "_MENU_",
                            'zeroRecords': noresultsString,
                        },
                        "dom": "<'row'<'col-sm-6'><'col-sm-6'f>>" +
                            "<'row'<'col-sm-12't>>" +
                            "<'row'<'col-sm-4'i><'col-sm-3'p><'col-sm-3'><'col-sm-1'l>>",
                        "pagingType": "numbers",
                        "scrollX": "true",
                    };

                    // Initialize depending on setting.
                    if (tableID == '#userincourse') {
                        options.order = [[ 0, settings.tool_supporter_course_details_order ]];
                        options.pageLength = settings.tool_supporter_course_details_pagelength;
                        options.lengthMenu = [settings.tool_supporter_course_details_pagelength, 10, 25, 50, 100];
                    }
                    if (tableID == '#userdetailcourses') {
                        options.order = [[ 0, settings.tool_supporter_user_details_order ]];
                        options.pageLength = settings.tool_supporter_user_details_pagelength;
                        options.lengthMenu = [settings.tool_supporter_user_details_pagelength, 10, 25, 50, 100];
                    }

                    $(tableID).DataTable(options);

                    // Apply Dropdown-Filters to DataTable.
                    use_filters(tableID, filterSelector);

                });
            });
        },

        /**
         * @method dataTable_ajax
         * @param tableID : ID of table you want to convert into datatable
         * @param methodname : Method to get the table data from
         * @param args : arguments for ajax-call
         * @param datainfo : specification, wher to find table data in return value of method
         * @param columns : Name of table columns
         */
        dataTable_ajax: function(tableID, methodname, args, datainfo, columns){

            var promise = ajax.call([{
                "methodname": methodname,
                "args": args
            }]);

            promise[0].done(function(data) {

                var promises = ajax.call([{
                    methodname: 'tool_supporter_get_settings',
                    args: {}
                }]);

                promises[0].done(function(settings) {
                    var string_noresults = str.get_string('noresults', 'admin');
                    var string_search = str.get_string('search', 'moodle');

                    $.when(string_noresults, string_search).done(function(noresultsString, searchString) {
                        var options = {
                            "data": data[datainfo],
                            "columns": columns,
                            "retrieve": true, // So the table can be accessed after initialization.
                            "responsive": true,
                            "lengthChange": true,
                            "deferRender": true, // For perfomance reasons.
                            "language": {
                                // Empty info. Legacy: Showing page _PAGE_ of _PAGES_ .
                                'info': " ",
                                'search': searchString + ": ",
                                'lengthMenu': "_MENU_",
                                'zeroRecords': noresultsString,
                            },
                            "dom": "<'col-sm-12't>" +
                                "<'row'<'col-sm-4'i><'col-sm-3'p><'col-sm-3'><'col-sm-1'l>>",
                            "paging": true,
                            "pagingType": "numbers",
                            "processing": true,
                            "initComplete" : function () { // Execute after the dataTable was rendered.
                                $(tableID + "-loadingIcon").hide();

                                if (tableID == "#courseTable") {
                                    $('#course_table_filtering').css('visibility', 'visible'); // Make it visible.
                                    // Add the course filtering for the courses table.
                                    templates.render('tool_supporter/course_table', data).done(function(html) {

                                        // Only render the filtering dropdowns of the tables, not the whole course_table.
                                        var anchor = $('[data-region="course_filtering"]', $(html));
                                        $('[data-region="course_filtering"]').replaceWith(anchor[0].outerHTML);

                                        // Counting begins at 0, but the shortname-column is invisible.
                                        use_filters(tableID, [['courses_levelonecheckbox', '#courses_levelonedropdown', 3],
                                            ['courses_leveltwocheckbox', '#courses_leveltwodropdown', 4]]);
                                    }).fail(notification.exception);
                                }
                                if (tableID.includes("#userTable")) {
                                    $('#users_dynamic_search').css('visibility', 'visible'); // Show search input.
                                }
                            }

                        };

                        // Initialize depending on setting.
                        if (tableID == '#courseTable') {
                            options.order = [[ 0, settings.tool_supporter_course_table_order ]];
                            options.pageLength = settings.tool_supporter_course_table_pagelength;
                            options.lengthMenu = [settings.tool_supporter_course_table_pagelength, 10, 25, 50, 100];
                        }
                        if (tableID == '#userTable') {
                            options.order = [[ 0, settings.tool_supporter_user_table_order ]];
                            options.pageLength = settings.tool_supporter_user_table_pagelength;
                            options.lengthMenu = [settings.tool_supporter_user_table_pagelength, 10, 25, 50, 100];
                        }

                        $(tableID).DataTable(options);
                    });
                });
            }).fail(notification.exception);

        }
    };
});
