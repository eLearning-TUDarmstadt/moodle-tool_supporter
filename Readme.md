Copyright: 2018 Benedikt Schneider (@Nullmann), Klara Saary (@KlaraSaary)

License: http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

# tool_supporter

## About
With the Supporter you can simply find & manage your students and courses - all in one, intuitve window.

It is the successor to the [Moodle Analyst](https://moodle.org/plugins/report_moodleanalyst) which was also created at the TU Darmstadt.

The Supporter is developed with amd and thus only retrieves the information from the database which are needed, not requiring reloading of the whole page; bringing a whole new user experience to moodle admins.

It has built-in the standard Moodle Capability-Checks so there are no extra capabilites needed and multiple levels of Support can be accomplished (see chapter "Capabilites").

## Features
* Find Students & Courses
* Display additional information about your students
  * Enrolled Courses/Students
  * Amount of enrolled Students
* Enrol users into courses
* Create a new Course
* Delete users from courses
* Hotlinks to additional Features
  * Edit, Settings, Visibility of courses
  * Log in as the selected user

## Prerequisites
* This version is tested on Moodle 3.5
  * No guarantees for other versions of moodle!
* A php version of 5.5 or greater is needed

## Installation
* Copy/Clone to `https://YOURSITE/admin/tool/` directory
* Enable database upgrade
* Go to `https://YOURSITE/admin/tool/supporter` or `Site Administration->Reports->Supporter`

## Capabilites
With this Plugin, we strove to implement all nesseccary moodle-capabilities. As such, there is no need to give users additional caps. 
If there is a need to divide different levels of support, these caps need to be set on a system-context:

Level 1 Support (read-only): 
- moodle/site:viewparticipants
- moodle/user:viewdetails (requires site:viewparticipants)
- moodle/course:viewhiddencourses
- moodle/course:view (requires course:viewhiddencourses)
		
level 2 Support (also write):
- Read-Capabilites from above
- moodle/user:loginas		
- moodle/course:create	
- moodle/course:update
- enrol/manual:enrol
  * This requires the defined role to be able to "Allow role assignments" in its settings

## Configuration
 * Blocks are disabled by default and the nav drawer gets closed
* The settings menu and it's functions are currently WIP and do not work at all.

## Screenshot
![screenshot](https://cloud.githubusercontent.com/assets/15816473/26623733/ec15ddf8-45ee-11e7-81e0-6414209d58e7.jpg)
