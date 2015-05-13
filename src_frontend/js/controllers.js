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

admin.directive('ngEnsureIsLoaded', function($timeout){

  return {
    link: function(scope, elmnt, attrs){
      elmnt.bind('error', function(){
        $timeout(function(){
          console.log('Error loading :', attrs.src);

          if( !elmnt.imgRetry )
            elmnt.imgRetry = 1;
          else if( elmnt.imgRetry == 3 )
            return;
          else
            elmnt.imgRetry++;

          $(elmnt).attr("src", attrs.src);
        }, 2000);
      });
    },
    scope: true,
    restrict: 'A'
  }

});

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
