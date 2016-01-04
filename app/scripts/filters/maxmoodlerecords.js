'use strict';

/**
 * @ngdoc filter
 * @name supporterApp.filter:maxMoodleRecords
 * @function
 * @description
 * # maxMoodleRecords
 * Filter in the supporterApp.
 */
angular.module('supporterApp')
    .filter('maxMoodleRecords', function () {
        return function (input, number) {
            var out = [];
            var count = 0;
            if (input) {
                input.forEach(function (element, index) {
                    if (count < number) {
                        out.push(element);
                        count++;
                    } else {
                        return out;
                    }
                });
            }
            return out;
        };
    });
