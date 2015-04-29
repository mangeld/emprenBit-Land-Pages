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
