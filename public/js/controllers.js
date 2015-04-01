var admin = angular.module('admin', []);

admin.controller('adminCtrl', function($scope, $http){

  $scope.appName = 'Landing Pages';
  $scope.title = 'Title set in controller';
  $scope.landingPages = [ ];
  $scope.show_button_new_page = true;
  $scope.show_form_new_page = false;

  $scope.newPageForm = {
    name: "",
    email: ""
  };

  $http.get('v1/pages').success(function(data, status, headers, config){
    $scope.landingPages = data.body;
  });

  var animateOut = function(element, callback){
    Velocity(
          element,
          { opacity: 0, height: 0, padding: 0 },
          {
            duration: 500,
            complete: function(){ callback(); }
          }
        );
  };

  var deleteFromPages = function(element, i){
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

  $scope.toggleShowFormNewPage = function(){
    $scope.show_form_new_page = !$scope.show_form_new_page;
    $scope.show_button_new_page = !$scope.show_button_new_page;
  };

  $scope.cancelNewPage = function(){
    $scope.toggleShowFormNewPage();
    $scope.newPageForm = {};
  };

  $scope.newPage = function(){
    console.log($scope.newPageForm);
    landing = {
      name: $scope.newPageForm.name,
      id: $scope.newPageForm.email
    };
    $scope.landingPages.push(landing);
    $scope.cancelNewPage(); 
  };
});