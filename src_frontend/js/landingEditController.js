admin.controller('LandingEditController', function($scope, $route, $http, api){

  $scope.landing = {};
  $scope.showAddBlockOverlay = false;
  $scope.uploadingCarousel = false;

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

  $scope.toggleUploadCarouselHint = function()
  {
    $scope.uploadingCarousel = !$scope.uploadingCarousel;
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

    if( typeof $scope.landing.cards.cardCarousel == 'undefined' )
      $scope.landing.cards.cardCarousel = [];
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

  $scope.updateCarousel = function(inputs, carousel)
  {
    $scope.toggleUploadCarouselHint();
    var getIds = function(ids)
    {
      var images = [];
      console.log("IDS: ", ids);
      for( var e = 0; e < inputs.length; e++)
      {
        images[e] = {};
        images[e].text = $(inputs).find('input[type="text"]').get(e).value;
      }

      ids.forEach(function(idObj){
        images[ idObj.file.imageIndex ].src = idObj.id;
      });

      $http({
        method: 'POST',
        url: 'v1/pages/' + $scope.landing.id + '/cards/' + carousel.id,
        headers: {
          "X-HTTP-Method-Override": "PUT",
          'Content-Type': undefined
        },
        transformRequest: function()
        {
          var f = new FormData();
          for( var m = 0; m < images.length; m++ ){
            f.append('texts[]', images[m].text);
            if( images[m].src )
              f.append('images[]', images[m].src);
            else
              f.append('images[]', null);
          }
          f.append('type', 'cardCarousel');
          f.append("color", carousel.color);
          f.append("backgroundColor", carousel.backgroundColor);
          return f;
        }
      })
        .success(function(){
          $scope.fetchLanding();
          $scope.toggleUploadCarouselHint();
        })
        .error(function(){
          $scope.fetchLanding();
          $scope.toggleUploadCarouselHint();
        });
    }

    var files = [];

    for( var i = 0; i < inputs.length; i++ )
    {
      var fil = $(inputs).find('input[type="file"]')[i].files[0];
      if( fil ){
        fil.imageIndex = i;
        files[i] = fil;
      }
    }

    console.log("FILEES: ", files.length);

    if( files.length > 0 )
      api.uploadMedia(files, $scope.landing.id, getIds);
    else
    {
      var fakeIds = [];
      getIds(fakeIds);
    }
  };

  $scope.uploadCarousel = function(event, carousel)
  {
    var inputs = $(event.target).parents('form').find('.inline_input');
    var images_count = inputs.length;
    var upload_count = 0;
    var images = new Array(); // [ { id: file | string, text: string } ]

    if( carousel.id )
    {
      $scope.updateCarousel(inputs, carousel);
      return;
    }

    $scope.toggleUploadCarouselHint();

    var callit = function(ids)
    {
      for( i = 0; i < ids.length; i++ )
      {
        images[i] = {};
        images[i].id = ids[i].id;
        images[i].text = ids[i].file.temporalText;
        upload_count++;
      }
      console.log("IDS: ", ids, "CAROUSEEEL: ", carousel);
      api.uploadCarousel(images, $scope.landing.id, carousel.color, carousel.backgroundColor)
        .success(function(){
          $scope.fetchLanding();
          $scope.toggleUploadCarouselHint();
        })
        .error(function(){
          $scope.fetchLanding();
          $scope.toggleUploadCarouselHint();
        });
    }

    var filesToUpload = [];

    for( var i = 0; i < images_count; i++ )
    {
      var inputFile = $(inputs).find('input[type="file"]')[i].files[0];
      var inputText = $(inputs).find('input[type="text"]').get(i).value;
      if( inputFile )
      {
        filesToUpload.push( inputFile );
        inputFile.temporalText = inputText;
      }
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
      api.updateCard($scope.landing.id, data, files).success(function(){completed();});

  };

  $scope.fetchLanding();
  console.log($route.current.params.landingName);
});
