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
    $scope.landing.cards.cardThreeColumns.push(
      {
        fieldTitle: [],
        fieldText: [],
        fieldImage: [],
        cardType: 'cardThreeColumns'
      }
    );
    console.log($scope.landing.cards.cardThreeColumns);
  };

  //TODO: RENAME TO updateCardThreeColumns
  $scope.updateCard = function(data, event)
  {
    var inputs = angular.element(event.target).find('input');
    var files = [];

    for(i=0; i<inputs.length;i++)
      if( inputs[i].files )
        files.push(inputs[i].files[0]);

    if( typeof data.id == 'undefined' )
    {
      api.uploadCard($scope.landing.id, data, files)
        .success( function(responseData){
          console.log("CARD SUBIDO");
          console.log(files);
          console.log(responseData);
        });
    }
    else
    {
      api.updateCard(data.id, data, files)
        .success(function(responseData){
          console.log(responseData);
        });
    }
  };

  console.log($route.current.params.landingName);
});
