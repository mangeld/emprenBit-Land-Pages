admin.controller('LandingEditController', function($scope, $route, api){

  $scope.landing = {};
  $scope.showAddBlockOverlay = false;

  $scope.fetchLanding = function(){
    api.getPages().success(function(data){
      for(var i = 0; i < data.length; i++)
        if( data[i].owner == $route.current.params.landingName )
        {
          $scope.landing = data[i];
          console.log(data[i]);
          break;
        }
    });
  };

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
    if( typeof $scope.landing.cards == 'undefined' )
    {
      $scope.landing.cards = {};
      $scope.landing.cards.cardThreeColumns = [];
    }

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

  $scope.deleteCard = function(event, card)
  {
    api.deleteCard($scope.landing.id, card.id)
      .success(function(){
        $scope.fetchLanding();
      });
  };

  //TODO: RENAME TO updateCardThreeColumns
  $scope.updateCard = function(data, event)
  {
    var inputs = $(event.target).parents('form').find('input');
    var insn = angular.element(event.target).find('input');
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
          $scope.fetchLanding();
        });
    }
    else
    {
      api.updateCard(data.id, data, files)
        .success(function(responseData){
          console.log(responseData);
          $scope.fetchLanding();
        });
    }
  };

  $scope.fetchLanding();
  console.log($route.current.params.landingName);
});
