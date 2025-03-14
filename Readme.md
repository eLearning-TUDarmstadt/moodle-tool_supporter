Copyright: developed and maintained by TU Darmstadt (initial release by Benedikt Schneider (@Nullmann), further development by @my-curiosity)

License: http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

# tool_supporter
![Moodle Plugin CI](https://github.com/eLearning-TUDarmstadt/moodle-tool_supporter/actions/workflows/moodle-ci.yml/badge.svg?branch=master)

## About
With the Supporter you can simply find & manage your students and courses - all in one, intuitive window.

It is the successor to the [Moodle Analyst](https://moodle.org/plugins/report_moodleanalyst) which was also created at the TU Darmstadt.

The Supporter is developed with asynchronous calls and thus only retrieves the information from the database which are needed, not requiring reloading of the whole page; bringing a whole new user experience to Moodle admins.

It has the standard Moodle capability-checks built-in, so there are no extra capabilities needed and multiple levels of support can be accomplished (see chapter "Capabilities").

## Features
* Find students & courses
* Display additional information about your students
  * Enrolled courses/students
  * Amount of enrolled students
* Enrol users in courses
* Create a new course
* Delete users from courses
* Hotlinks to additional features
  * Edit, Settings, Visibility of courses
  * Log in as the selected user

## Prerequisites
* This version is tested on Moodle 4.0, 4.1, 4.2, 4.3, 4.4, 4.5
* It is highly recommended to activate compression to reduce transmitted data (e.g. from 4.36MB to 1.05 MB)
  * Add "zlib.output_compression = On" in php.ini
  * Or add deflate to your apache/nginx/lighttpd, see https://docs.moodle.org/en/Performance_recommendations

## Installation
* Copy/Clone to `https://YOURSITE/admin/tool/` directory
  * Alternatively use `git clone https://github.com/eLearning-TUDarmstadt/moodle-tool_supporter admin/tool/supporter` in your Moodle root
* Enable database upgrade
* Go to `https://YOURSITE/admin/tool/supporter` or `Site Administration->General->Supporter`

## Capabilities
With this plugin, we strove to implement all necessary Moodle-capabilities. As such, there is no need to give users additional caps. 
If there is a need to divide different levels of support, these caps need to be set in a system-context:

Level 1 Support (read-only): 
- moodle/site:viewparticipants
- moodle/user:viewdetails (requires site:viewparticipants)
- moodle/course:viewhiddencourses
- moodle/course:view (requires course:viewhiddencourses)
		
level 2 Support (also write):
- read-capabilities from above
- moodle/user:loginas		
- moodle/course:create	
- moodle/course:update
- enrol/manual:enrol
  * This requires the defined role to be able to "Allow role assignments" in its settings

## Configuration
* Blocks are disabled by default and the nav drawer gets closed for maximum viewing 
* The settings can be accessed with the cog in the header or by going to `/admin/settings.php?section=tool_supporter`

## Limitations
Since v4-r1 user can choose from 0 to 5 layers of course categories to be represented (from 0 to 2 layers in v1.07), however, user detail table always shows 2.

## Screenshots
### Supporter overview: courses in left column, users in right column
![Supporter_courses-users-overview](https://github.com/eLearning-TUDarmstadt/moodle-tool_supporter/assets/1639438/b71ce44b-cbb6-46d3-a466-aa53839e4e44)
<br />
<br />
<br />
### Supporter settings
![Supporter_settings](https://github.com/eLearning-TUDarmstadt/moodle-tool_supporter/assets/1639438/2f6d85fe-a71b-4ece-bc5d-e8310016371f)
<br />
<br />
<br />
### Create a new course
![Supporter_create-course](https://github.com/eLearning-TUDarmstadt/moodle-tool_supporter/assets/1639438/d0c14d13-5155-41a4-be45-e80135ec7375)
