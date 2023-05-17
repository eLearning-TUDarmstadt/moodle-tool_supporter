/**
 * Gruntfile for creating Asynchronous Module Definition JavaScript files in tool_supporter
 * Changes: Removed unused tasks and some other code
 *
 * @package tool_supporter
 * @author my-curiosity
 * @author Based on code originally written by G J Barnard, Joby Harding, Bas Brands, David Scotson and many other contributors.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
module.exports = function(grunt) {

    var path = require("path");

    var moodleroot = path.dirname(path.dirname(__dirname));
    var dirrootopt = grunt.option("dirroot") || process.env.MOODLE_DIR || "";

    if ("" !== dirrootopt) {
        moodleroot = path.resolve(dirrootopt);
    }

    var PWD = process.cwd();

    grunt.initConfig({
        jshint: {
            options: {
                jshintrc: true
            },
            files: ["**/amd/src/*.js"]
        },
        uglify: {
            dynamic_mappings: {
                files: grunt.file.expandMapping(
                    ["**/src/*.js", "!**/node_modules/**"],
                    "",
                    {
                        cwd: PWD,
                        rename: function(destBase, destPath) {
                            destPath = destPath.replace("src", "build");
                            destPath = destPath.replace(".js", ".min.js");
                            destPath = path.resolve(PWD, destPath);
                            return destPath;
                        }
                    }
                )
            }
        }
    });

    grunt.loadNpmTasks("grunt-contrib-uglify");
    grunt.loadNpmTasks("grunt-contrib-jshint");

    grunt.registerTask("amd", ["jshint", "uglify"]);
};
