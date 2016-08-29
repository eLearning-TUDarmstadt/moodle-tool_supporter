<?php
/**
 * videoannotations external file
 *
 * @package    tool_supporter
 * @copyright  2016 Klara Saary
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");
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

 public static function get_users($array){
   global $DB;

   // now security checks
    $context = context_system::instance();
    self::validate_context($context);
    //Is the user allowes to use this web service?
    require_capability('tool_supporter:get_users', $context);

    $rs = $DB->get_recordset('user', null, null, 'id, username, firstname, lastname, email' );
    foreach ($rs as $record) {
      $users[] = (array)$record;
    }
    $rs->close();
    $data['users'] = $users;

    return $data;

 }


}
