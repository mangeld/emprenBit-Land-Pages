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
