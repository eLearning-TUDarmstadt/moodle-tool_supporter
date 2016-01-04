'use strict';

/**
 * @ngdoc function
 * @name supporterApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the supporterApp
 */
angular.module('supporterApp')
    .controller('MainCtrl', function ($rootScope, $state, $stateParams) {
        $rootScope.$state = $state;
        $rootScope.$stateParams = $stateParams;
        $state.transitionTo('overview.subs');
    });
