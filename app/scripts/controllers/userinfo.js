'use strict';

/**
 * @ngdoc function
 * @name supporterApp.controller:UserinfoCtrl
 * @description
 * # UserinfoCtrl
 * Controller of the supporterApp
 */
angular.module('supporterApp')
  .controller('UserinfoCtrl', function ($scope) {
      $scope.user = null;
    this.awesomeThings = [
      'HTML5 Boilerplate',
      'AngularJS',
      'Karma'
    ];
  });
