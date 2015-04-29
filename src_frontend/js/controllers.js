var admin = angular.module('admin', ['ngRoute']);

admin.config(['$routeProvider', function($routeProvider){
  $routeProvider
    .when('/edit/:landingName', {
      templateUrl: 'landingEdit.html',
      controllerAs: 'LandingEditController'
    })
    .when('/', {
      templateUrl: 'adminTemplate.html',
      controller: 'landingListCtrl'
    });
}]);

admin.directive('ngFileUpload', function(){
  return {
    link: function(scope, ele){
      var input = ele.find('input');
      input.on('change', function(){
        scope.$emit('fileSelected', input);
      });
    },
    scope: true,
    restrict: 'A',
    templateUrl: 'ngFileUpload.html'
  }
});
