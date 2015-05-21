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

  $scope.updateLanding = function(event)
  {
    var logoInput = $(event.target).parents('form').find('input[type="file"]');
    var logoFile = logoInput[0].files.item(0);

    api.updatePage($scope.landing, logoFile)
      .success(function(){
        $scope.fetchLanding();
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

  $scope.toggleUploadingHint = function()
  {
    $scope.uploadingLanding = !$scope.uploadingLanding;
  }

  $scope.initCardsIfEmpty = function()
  {
    if( typeof $scope.landing.cards == 'undefined' )
    {
      $scope.landing.cards = {};
      $scope.landing.cards.cardThreeColumns = [];
      $scope.landing.cards.cardForm = {};
      $scope.landing.cards.cardCarousel = [];
    }

    if( typeof $scope.landing.cards.cardThreeColumns == 'undefined' )
      $scope.landing.cards.cardThreeColumns = [];
  };

  $scope.addFormCard = function()
  {
    $scope.initCardsIfEmpty();
  };

  $scope.addThreeColumns = function()
  {
    $scope.closeOverlay();
    $scope.initCardsIfEmpty();

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

  $scope.pushCarouselImage = function(carousel)
  {
    carousel.images.push(
      { src: "", text: "" }
    );
  }

  $scope.addCarousel = function()
  {
    $scope.closeOverlay();
    $scope.initCardsIfEmpty();

    $scope.landing.cards.cardCarousel.push(
      {
        images: [ { src: "", text: ""} ]
        //id
      }
    );
  }

  $scope.uploadCarousel = function(event, carousel)
  {
    var inputs = $(event.target).parents('form').find('.inline_input');
    var images_count = inputs.length;
    var upload_count = 0;
    var images = new Array(); // [ { id: file | string, text: string } ]

    var callit = function(ids)
    {
      for( i = 0; i < ids.length; i++ )
      {
        images[i] = {};
        images[i].id = ids[i].id;
        images[i].text = ids[i].file.temporalText;
        upload_count++;
      }
      api.uploadCarousel(images, $scope.landing.id)
        .success(function(){ $scope.fetchLanding(); });
    }

    var filesToUpload = [];

    for( var i = 0; i < images_count; i++ )
    {
      var inputFile = $(inputs).find('input[type="file"]')[i].files[0];
      var inputText = $(inputs).find('input[type="text"]').get(i).value;
      inputFile.temporalText = inputText;
      filesToUpload.push( inputFile );
    }
    api.uploadMedia(filesToUpload, $scope.landing.id, callit);
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
    var files = [];

    $scope.toggleUploadingHint();

    for(var i=0; i<inputs.length;i++)
      if( inputs[i].files )
        files.push(inputs[i].files[0]);

    var completed = function(responseData){
          console.log("CARD SUBIDO");
          console.log(files);
          console.log(responseData);
          $scope.fetchLanding();
          $scope.toggleUploadingHint();
    };

    var result = {};
    if( typeof data.id == 'undefined' )
      api.uploadCard($scope.landing.id, data, files, completed);
    else
      api.updateCard($scope.landing.id, data, files, completed);

  };

  $scope.fetchLanding();
  console.log($route.current.params.landingName);
});
