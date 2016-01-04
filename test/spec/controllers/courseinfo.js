'use strict';

describe('Controller: CourseinfoCtrl', function () {

  // load the controller's module
  beforeEach(module('supporterApp'));

  var CourseinfoCtrl,
    scope;

  // Initialize the controller and a mock scope
  beforeEach(inject(function ($controller, $rootScope) {
    scope = $rootScope.$new();
    CourseinfoCtrl = $controller('CourseinfoCtrl', {
      $scope: scope
      // place here mocked dependencies
    });
  }));

  it('should attach a list of awesomeThings to the scope', function () {
    expect(CourseinfoCtrl.awesomeThings.length).toBe(3);
  });
});
