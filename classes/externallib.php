<?php
/**
 * videoannotations external file
 *
 * @package    tool_supporter
 * @copyright  2016 Klara Saary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("$CFG->libdir/externallib.php");
require_once("$CFG->dirroot/config.php");

class tool_supporter_external extends external_api {

  /**
 * Returns description of method parameters
 * @return external_function_parameters
 */
 public static function get_users_parameters() {
   return new external_function_parameters(
            array(
                'search_input' => new external_value(PARAM_RAW, 'if you just looking for specified users', VALUE_DEFAULT, '%')
            )
    );
 }

 public static function get_users_returns() {
     return new external_multiple_structure(
        new external_single_structure(
          array(
            'id' => new external_value(PARAM_INT, 'id of user'),
            'username' => new external_value(PARAM_RAW, 'username of user'),
            'firstname' => new external_value(PARAM_RAW,'firstname of user'),
            'lastname' => new external_value(PARAM_RAW, 'lastname of user'),
            'email' => new external_value(PARAM_RAW, 'email adress of user')
          )
        )
      );
 }

 public static function get_users(){
   global $DB;

   // now security checks
    $context = context_system::instance();
    self::validate_context($context);
    //Is the user allowes to use this web service?
    require_capability('tool/supporter:get_users', $context);

    $rs = $DB->get_recordset('user', null, null, 'id, username, firstname, lastname, email' );
    foreach ($rs as $record) {
      $users[] = (array)$record;
    }
    $rs->close();
    $data['users'] = $users;
    return $data;

 }

 public static function get_courses_parameters(){
   return new external_function_parameters(
            array( //no parameters required
            )
    );
 }

 public static function get_courses_returns() {
     return new external_multiple_structure(
        new external_single_structure(
          array(
            'id' => new external_value(PARAM_INT, 'id of course'),
            'semester' => new external_value(PARAM_RAW, 'parent category'),
            'FB' => new external_value(PARAM_RAW,'course category'),
            'fullname' => new external_value(PARAM_RAW, 'course name'),
            'visible' => new external_value(PARAM_RAW, 'Is the course visible?')
          )
        )
      );
 }

 public static function get_courses(){
   global $DB;

   // now security checks
    $context = context_system::instance();
    self::validate_context($context);
    //Is the user allowes to use this web service?
    require_capability('tool/supporter:get_users', $context);

    $select = 'SELECT c.id, c.fullname, c.visible, cat.name AS fb, (SELECT name FROM {course_categories} WHERE id = cat.parent) AS semester FROM {course} c, {course_categories} cat WHERE c.category = cat.id';
    $rs = $DB->get_recordset_sql($select);
    foreach ($rs as $record) {
      $courses[] = (array)$record;
    }
    $rs->close();
    $data['courses'] = $courses;
    return $data;
  }

 public static function get_course_info_parameters(){
    return new external_function_parameters(
             array(
                 'courseID' => new external_value(PARAM_RAW, 'id of course you want to show')
             )
     );

  }

 public static function get_course_info_returns(){
   return new external_multiple_structure(
      new external_single_structure(
        array(
          'courseDetails'=> new external_multiple_structure(
              new external_single_structure(
                array(
                'id' => new external_value(PARAM_INT, 'id of course'),
                'semester' => new external_value(PARAM_RAW, 'parent category'),
                'FB' => new external_value(PARAM_RAW,'course category'),
                'shortname' => new external_value(PARAM_RAW, 'shortname of course'),
                'fullname' => new external_value(PARAM_RAW, 'course name'),
                'visible' => new external_value(PARAM_RAW, 'Is the course visible?'),
                'path' => new external_value(PARAM_RAW, 'path of course'),
                'enrolledUsers' => new external_value(PARAM_RAW, 'number of users, without teachers')
                )
              )
            ),
          'roles' => new external_multiple_structure(
              new external_single_structure(
                array(
                  'roleName' => new external_value(PARAM_RAW, 'name of one role in course'),
                  'roleNumber' => new external_value(PARAM_INT, 'number of participants with role = roName')
                  )
              )
            ),
          'users' => new external_multiple_structure(
              new external_single_structure(
                array( )
                )
              )
          )
        )
      );
  }

 public static function get_course_info($courseID){
   global $DB;
   //check parameters
    $params = self::validate_parameters(self::get_course_info_parameters(), array('courseID'=>$courseID));
   // now security checks
    $coursecontext = context_course::instance($params['courseID']);
    self::validate_context($coursecontext);
    //Is the user allowes to use this web service?
    //require_capability('moodle/site:viewparticipants', $context); // is the user normaly allowed to see all participants of the course
    require_capability('tool/supporter:get_course_info', $coursecontext); // is the user coursecreator, manager, teacher, editingteacher

    $select = "SELECT c.id, c.shortname, c.fullname, c.visible, cat.name AS fb, (SELECT name FROM {course_categories} WHERE id = cat.parent) AS semester FROM {course} c, {course_categories} cat WHERE c.category = cat.id AND c.id = '$courseID'";
    $courseDetails = $DB->get_record_sql($select);
    $courseDetails = (array)$courseDetails;
    $courseDetails['enrolledUsers'] = count_enrolled_users($coursecontext, $withcapability = '', $groupid = '0');
    $roles = array();
    $roleList = get_all_roles($coursecontext); // array('moodle/legacy:student', 'moodle/legacy:teacher', 'moodle/legacy:editingteacher', 'moodle/legacy:coursecreator');
    $count= count_role_users([1,2,3,4,5,6,7], $coursecontext);
    print_r($roleList);
    echo"count";
    print_r($count);
    foreach ($roleList as $r) {
      if($r->coursealias != NULL)
      	$roleName = $r->coursealias;
      else 
      	$roleName =$r->shortname;
      $roleNumber = count_role_users($r->id,$coursecontext);
      //$roleNumber = count_enrolled_users($coursecontext, $withcapability = $role, $groupid = 0);
      if($roleNumber != 0)
      	$roles[] = ['roleName' => $roleName, 'roleNumber' => $roleNumber];
    }
    $users_raw = get_enrolled_users($coursecontext, $withcapability = '', $groupid = 0, $userfields = 'u.id,u.username,u.firstname, u.lastname', $orderby = '', $limitfrom = 0, $limitnum = 0);
    $users = array();
    foreach($users_raw as $u){
      $users[] = (array)$u;
    }
    $activities = array();
    $modules =  get_array_of_activities($courseID);   
    foreach($modules as $mo){
      $section = get_section_name($courseID, $mo->section);
      $activity = ['section' =>$section, 'activity'=>$mo->mod,'name'=>$mo->name, 'visible'=>$mo->visible];
      $activities[] = $activity;
    }
    $data = ['courseDetails' => $courseDetails, 'roles' => $roles, 'users' => $users, 'activities' => $activities];
    return $data;
 }

 public static function get_course_activities($courseID){
 	global $DB;
 
 	// now security checks
 	$context = context_system::instance();
 	self::validate_context($context);
 	//Is the user allowes to use this web service?
 	require_capability('tool/supporter:get_users', $context);
 
 	
 
 }
 
 public static function get_course_activities_parameters(){
 	return new external_function_parameters(
 			array(
 					'courseID' => new external_value(PARAM_RAW, 'id of course')
 			)
 			);
 }
 
 public static function get_course_activities_returns() {
 	return new external_multiple_structure(
 			new external_single_structure(
 					array(
 						'activities'=> new external_multiple_structure(
             				 new external_single_structure(
                				array(
		 							'Section' => new external_value(PARAM_INT, 'Name of the section the activity appears in'),
		 							'Kind of activity' => new external_value(PARAM_RAW, 'Kind of activity'),
		 							'Activity name' => new external_value(PARAM_RAW,'User given Name of Activty'),
		 							'visible' => new external_value(PARAM_RAW, 'Is the course visible?')
 								)
 							)
 						)
 					)
 			)
 		);
 }

}
