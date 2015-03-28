var admin = angular.module('admin', ['ngAnimate']);

admin.controller('adminCtrl', function($scope){

  $scope.title = 'Title set in controller';
  $scope.landingPages = [
    { name: 'test' },
    { name: 'test2' },
    { name: 'test3' },
    { name: 'test4' },
    { name: 'test5' },
    { name: 'test6' },
    { name: 'test7' },
    { name: 'test8' },
    { name: 'test9' },
    { name: 'test10'}
    ];

  $scope.removeItem = function(item){
    var i = $scope.landingPages.indexOf(item);
    $scope.landingPages.splice(i, 1);
  };

  $scope.clickedBtn = function (event){
    console.log(event);
  };
});