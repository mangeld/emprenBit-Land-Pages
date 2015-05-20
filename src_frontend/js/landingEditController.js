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
      $scope.landing.cards.carousels = [];
    }
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

    $scope.landing.cards.carousels.push(
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
    var images = new Array();

    //console.log(carousel.images);
    //console.log($(inputs).find('input[type="text"]'));

    var notify = function() {
      if (upload_count == images_count - 1) {
        console.log("Uploading images", images, "IMAGES SIZE: " + images.length);
        api.uploadCarousel(images, $scope.landing.id);
      } else {
        console.log("UPLOAD COUNT: " + upload_count, "IMAGES: " + images_count)
        upload_count++;
      }
    }

    for( var i = 0; i < images_count; i++ )
    {
      var inputFile = $(inputs).find('input[type="file"]')[i].files[0];
      var inputText = $(inputs).find('input[type="text"]').get(i).value;
      var callit = function(ids)
      {
        for( var e = 0; e < images.length; e++)
        {
          console.log("For loop: ", images);
          if( images[e].id.name == ids[0].file.name ){
            images[e].id = ids[0].id;
            break;
          }
        }
        notify();
      }

      images.push( { id: inputFile, text: inputText } );
      console.log("ARCHIVOOOOOO", inputFile);
      api.uploadMedia(inputFile, $scope.landing.id, callit);
    }
    console.log("Final de upload carousel ",images);
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
