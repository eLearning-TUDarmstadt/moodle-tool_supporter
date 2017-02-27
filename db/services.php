<?php
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
 * tool supporter external services.
 *
 * @package    tool_supporter
 * @copyright  2016 Benedikt Schneider, Klara Saary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 $services = array(
       'supporterservice' => array(                                                //the name of the web service
           'functions' => array (
             'tool_supporter_create_new_course',
             'tool_supporter_get_course_info',
             'tool_supporter_get_users_courses',
           'requiredcapability' => '',                //if set, the web service user need this capability to access
                                                                               //any function of this service. For example: 'some/capability:specified'
           'restrictedusers' =>0,                                             //if enabled, the Moodle administrator must link some user to this service
                                                                               //into the administration
           'enabled'=>1,                                                       //if enabled, the service can be reachable on a default installation
           'ajax' => true
           )
   );

$functions = array(

    // For each functuon: Which class provides the function?

    'tool_supporter_create_new_course' => array(         //web service function name
        'classname'   => 'tool_supporter\external',  //class containing the external function
        'methodname'  => 'create_new_course',          //external function name
        'classpath'   => 'tool/supporter/classes/external.php',  //file containing the class/external function
        'description' => 'Create a course',    //human readable description of the web service function
        'type'        => 'write',                  //database rights of the web service function (read, write)
        'ajax'        => true
        //'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)    // Optional, only available for Moodle 3.1 onwards. List of built-in services (by shortname) where the function will be included.  Services created manually via the Moodle interface are not supported.
    ),
    'tool_supporter_get_user_information' => array(         //web service function name
        'classname'   => 'tool_supporter\external',  //class containing the external function
        'methodname'  => 'get_user_information',          //external function name
        'classpath'   => 'tool/supporter/classes/external.php',  //file containing the class/external function
        'description' => 'Get user information',    //human readable description of the web service function
        'type'        => 'read',                  //database rights of the web service function (read, write)
        'ajax'        => true
        //'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)    // Optional, only available for Moodle 3.1 onwards. List of built-in services (by shortname) where the function will be included.  Services created manually via the Moodle interface are not supported.
    ),

	'tool_supporter_get_course_info' => array(         //web service function name
		'classname'   => 'tool_supporter\external',  //class containing the external function
		'methodname'  => 'get_course_info',        //external function name
		'classpath'   => 'tool/supporter/classes/external.php',  //file containing the class/external function
		'description' => 'Get course information',    //human readable description of the web service function
		'type'        => 'read',                  //database rights of the web service function (read, write)
		'ajax'        => true
		//'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)    // Optional, only available for Moodle 3.1 onwards. List of built-in services (by shortname) where the function will be included.  Services created manually via the Moodle interface are not supported.
	),
  'tool_supporter_enrol_user_into_course' => array(         //web service function name
		'classname'   => 'tool_supporter\external',  //class containing the external function
		'methodname'  => 'enrol_user_into_course',        //external function name
		'classpath'   => 'tool/supporter/classes/external.php',  //file containing the class/external function
		'description' => 'Get course information',    //human readable description of the web service function
		'type'        => 'write',                  //database rights of the web service function (read, write)
		'ajax'        => true
		//'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)    // Optional, only available for Moodle 3.1 onwards. List of built-in services (by shortname) where the function will be included.  Services created manually via the Moodle interface are not supported.
	),
  'tool_supporter_get_assignable_roles' => array(         //web service function name
    'classname'   => 'tool_supporter\external',  //class containing the external function
    'methodname'  => 'get_assignable_roles',        //external function name
    'classpath'   => 'tool/supporter/classes/external.php',  //file containing the class/external function
    'description' => 'Get assignable Roles in the course',    //human readable description of the web service function
    'type'        => 'read',                  //database rights of the web service function (read, write)
    'ajax'        => true
    //'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)    // Optional, only available for Moodle 3.1 onwards. List of built-in services (by shortname) where the function will be included.  Services created manually via the Moodle interface are not supported.
  )
);
