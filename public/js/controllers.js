var admin = angular.module('admin', [  ]);

admin.controller('adminCtrl', function($scope){

  $scope.appName = 'Landing Pages';
  $scope.title = 'Title set in controller';
  $scope.landingPages = [
    { name: 'test', hidden: false },
    { name: 'test2', hidden: false },
    { name: 'test3', hidden: false },
    { name: 'test4', hidden: false },
    { name: 'test5', hidden: false },
    { name: 'test6', hidden: false },
    { name: 'test7', hidden: false },
    { name: 'test8', hidden: false },
    { name: 'test9', hidden: false },
    { name: 'test10', hidden: false }
  ];

  var animateOut = function(element, callback){
    Velocity(
          element,
          { opacity: 0, height: 0 },
          {
            duration: 500,
            complete: function(){ callback(); }
          }
        );
  };

  var deleteFromPages = function(element, i){
    console.log(element);
    console.log(i);
    console.log($scope.landingPages);
    element.parentNode.removeChild(element);
    $scope.landingPages.splice(i, 1);
  };

  $scope.removeItem = function(evnt, i){
    //$scope.landingPages.splice(i, 1).pop();
    element = document.getElementsByClassName("blocklist")[0].children[i];
    animateOut(element, function(){ deleteFromPages(element, i); });
  };

  $scope.clickedBtn = function (event){
    console.log(event);
  };
});