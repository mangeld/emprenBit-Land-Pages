var admin = angular.module('admin', ['ngAnimate']);

admin.controller('adminCtrl', function($scope, $animate){

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

  console.log($animate);

  $scope.removeItem = function(index){
    console.log('Item deleted');
    $scope.landingPages.splice(index, 1);
  };

  $scope.clickedBtn = function (event){
    console.log(event);
  };
});