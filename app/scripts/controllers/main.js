'use strict';

/**
 * @ngdoc function
 * @name supporterApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the supporterApp
 */
angular.module('supporterApp')
    .controller('MainCtrl', function ($rootScope, $state, $stateParams, $http, $scope, $log) {
        var REST_URL = window.location.origin + window.location.pathname + 'rest/rest.php';
        
        $scope.courses = null;
        $scope.course = null;
        
        $scope.users = null;
        $scope.user = null;
        
        $scope.translations = null;
        
        
        $http.get(REST_URL + '/users').then(function(resp) {
            $scope.users = Object.keys(resp.data).map(function (key) {return resp.data[key];});
        });
        
    });
