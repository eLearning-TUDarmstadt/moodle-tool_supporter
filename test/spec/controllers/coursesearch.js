'use strict';

describe('Controller: CoursesearchCtrl', function () {

  // load the controller's module
  beforeEach(module('supporterApp'));

  var CoursesearchCtrl,
    scope;

  // Initialize the controller and a mock scope
  beforeEach(inject(function ($controller, $rootScope) {
    scope = $rootScope.$new();
    CoursesearchCtrl = $controller('CoursesearchCtrl', {
      $scope: scope
      // place here mocked dependencies
    });
  }));

  it('should attach a list of awesomeThings to the scope', function () {
    expect(CoursesearchCtrl.awesomeThings.length).toBe(3);
  });
});
