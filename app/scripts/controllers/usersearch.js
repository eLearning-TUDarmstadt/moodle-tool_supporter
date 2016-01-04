'use strict';

/**
 * @ngdoc function
 * @name supporterApp.controller:UsersearchCtrl
 * @description
 * # UsersearchCtrl
 * Controller of the supporterApp
 */
angular.module('supporterApp')
  .controller('UsersearchCtrl', function ($scope, $log) {
    $scope.searchUser = '';
    $scope.sortType = null;
    $scope.sortReverse = false;
    
    $scope.setUser = function(user) {
        $log.info(user);
        $scope.user = user;
    };
  });
