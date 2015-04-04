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

  $scope.retrievePages = function(){
    $http.get('v1/pages').success(function(data, status, headers, config){
      $scope.landingPages = data.body.sort(function(a, b){
        return b.creation_timestamp - a.creation_timestamp;
      });

      $scope.landingPages.forEach(function(obj){
        date = new Date( obj.creation_timestamp * 1000 );
        d = date.getDate();
        mon = date.getMonth();
        y = date.getFullYear();
        mm = date.getMinutes();
        hh = date.getHours();
        obj.date = d + '/' + mon + '/' + y + ' ' + hh + ':' + mm;
        console.log(obj);
        //return obj;
      });
    });
  };

  $scope.retrievePages();

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
    page = $scope.landingPages.splice(i, 1).pop();
    $http.delete('v1/pages/' + page.id).success(function(){
      $scope.retrievePages();
    });
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
    console.dir($scope.landingPages);
    landing = {
      name: $scope.newPageForm.name,
      id: $scope.newPageForm.email
    };
    
    $http.post('v1/pages', landing)
      .success(function(){
        $scope.retrievePages();
      })
      .error(function(){

    });

    $scope.cancelNewPage(); 
  };
});