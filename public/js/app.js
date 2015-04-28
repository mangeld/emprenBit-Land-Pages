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

admin.controller('landingListCtrl', function($scope, $http){

  $scope.appName = 'Landing Pages';
  $scope.title = 'Title set in controller';
  $scope.imgName = '';
  $scope.landingPages = [ ];
  $scope.show_overlay = false;
  $scope.show_button_new_page = true;
  $scope.show_form_new_page = false;
  $scope.showFileUpload = true;
  $scope.show_main = true;
  $scope.show_edit_page = false;

  $scope.newPageForm = {
    name: "",
    email: "",
    title: "",
    description: ""
  };

  $scope.$on('fileSelected', function(evnt, args){
    var size = (args[0].files[0].size / 1024 / 1024).toFixed(2);
    $scope.imgName = args[0].files[0].name + ' ' + '(' + size + 'MB)';
    console.log(evnt);
    $scope.$apply();
    $scope.newPageFormImage = args[0].files[0];
  });

  $scope.retrievePages = function(){
    $http.get('v1/pages').success(function(data, status, headers, config){
      $scope.landingPages = data.body.sort(function(a, b){
        return b.creation_timestamp - a.creation_timestamp;
      });
      console.log($scope.landingPages);
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

  $scope.toggleShowFormNewPage = function(){
    $scope.show_form_new_page = !$scope.show_form_new_page;
    $scope.show_button_new_page = !$scope.show_button_new_page;
  };

  $scope.cancelNewPage = function(){
    $scope.toggleShowFormNewPage();
    $scope.newPageForm = {};
    $scope.imgName = '';
    $scope.showFileUpload = false;
    $scope.showFileUpload = true;
  };

  $scope.newPage = function(){

    $scope.show_overlay = true;
    $http({
      method: 'POST',
      url: 'v1/pages',
      headers: { 'Content-Type': undefined },
      data: { jsonData: $scope.newPageForm, image: $scope.newPageFormImage },
      transformRequest: function(data){
        var fdata = new FormData();
        fdata.append( "data", angular.toJson(data.jsonData) );
        fdata.append( 'image', data.image );
        return fdata;
      }
    })
      .success(function(data, status, headers, config){
        $scope.cancelNewPage();
        $scope.retrievePages();
        $scope.show_overlay = false;
      })
      .error(function(data, status, headers, config){
        alert("Error: " + status);
        $scope.cancelNewPage();
        $scope.show_overlay = false;
      });
  };
});

admin.controller('PageEditController', function($scope){

});

admin.factory('api', ['$http', function($http){
  var api = {};
  api.getPages = function(){
    return $http({
      method: 'GET',
      url: 'v1/pages',
      transformResponse: function(data){
        var toSort = angular.fromJson(data);
        sorted = toSort.body.sort(function (a, b) {
          return b.creation_timestamp - a.creation_timestamp;
        });
        return sorted;
      }
    });
  };

  api.updateCard = function(cardId, jsonData, images){
    return $http({
      method: 'POST',
      url: 'v1/cards/' + cardId,
      headers: { 'Content-Type': undefined },
      data: { jsonData: jsonData, images: images },
      transformRequest: function(data){
        var fdata = new FormData();
        fdata.append( "data", angular.toJson(data.jsonData) );
        data.images.forEach(function(image, index, array){
          fdata.append("image" + index, image);
        });
        console.log(fdata);
        return fdata;
      }
    });
  };

  api.uploadPage = function(jsonData, images){
    return $http({
      method: 'POST',
      url: 'v1/pages',
      headers: { 'Content-Type': undefined },
      data: { jsonData: jsonData, images: images },
      transformRequest: function(data){
        var fdata = new FormData();
        fdata.append( "data", angular.toJson(data.jsonData) );
        data.images.forEach(function(image, index, array){
          fdata.append("image" + index, image);
        });
        console.log(fdata);
        return fdata;
      }
    });
  };
  return api;
}]);

admin.controller('LandingEditController', function($scope, $route, api){

  $scope.landing = {};
  $scope.showAddBlockOverlay = false;

  api.getPages().success(function(data){
    for(var i = 0; i < data.length; i++)
      if( data[i].owner == $route.current.params.landingName )
      {
        $scope.landing = data[i];
        console.log(data[i]);
        break;
      }
  });

  $scope.addBlock = function()
  {
    $scope.showAddBlockOverlay = true;
  };

  $scope.closeOverlay = function()
  {
    $scope.showAddBlockOverlay = false;
  };

  $scope.addThreeColumns = function()
  {
    $scope.closeOverlay();
    $scope.landing.cards.cardThreeColumns = [
      {
        fieldTitle: [],
        fieldDescription: [],
        fieldImage: []
      }
    ];
  };

  $scope.updateCard = function(data, event)
  {
    var inputs = angular.element(event.target).find('input');
    var files = [];

    for(i=0; i<inputs.length;i++)
      if( inputs[i].files )
        files.push(inputs[i].files[0]);

    api.updateCard(data.id, data, files)
      .success(function(responseData){
        console.log(responseData);
      });
  };

  console.log($route.current.params.landingName);
});