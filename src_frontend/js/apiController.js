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

  api.requestTransform = function(data)
  {
    var fdata = new FormData();
    fdata.append( "data", angular.toJson(data.jsonData) );
    data.images.forEach(function(image, index, array){
      fdata.append("image" + index, image);
    });
    console.log(data.jsonData);
    return fdata;
  };

  api.deleteCard = function(landingId, cardId){

    return $http({
      method: 'DELETE',
      url: 'v1/pages/'+landingId+'/cards/'+cardId
    });

  };

  api.uploadMedia = function(files, landingId, callback)
  {
    if( !$.isArray(files) ) files = [files];
    var media_count = files.length;
    var media_ids = [];
    var success_count = 0;
    var loop = true

    var should_continue = function()
    {
      if( media_count <= success_count ){
        loop = false;
        callback(media_ids);
      } else {
        do_http(files[success_count])
        success_count++;
      }
    }

    var do_http = function(file)
    {
      console.log("INDIVIDUAL FILE", file);
      console.log("MEDIA COUNT: ", media_count, "SUCCESS_COUNT: ", success_count);
      if( typeof file == 'undefined' ){
        success_count++;
        should_continue();
        return;
      }
      $http({
        method: 'POST',
        url: 'v1/upload',
        headers: { 'Content-Type': undefined },
        transformRequest: function(data){
          var f = new FormData();
          f.append("data", file);
          f.append("page_id", landingId);
          return f;
        }
      }).success(function(data){
        media_ids.push( { file: file, id: data.id } );
        should_continue();
      });
    }

    should_continue();
  }

  api.uploadCarousel = function(images, landingId, color, backgroundColor)
  {
    return $http({
      method: 'POST',
      url: 'v1/pages/'+landingId+'/cards',
      headers: { 'Content-Type': undefined },
      transformRequest: function(data){
        var f = new FormData();
        for(var i = 0; i < images.length; i++ )
        {
          f.append("images[]", images[i].id);
          f.append("texts[]", images[i].text);
        }
        f.append("type", "cardCarousel");
        f.append("color", color);
        f.append("backgroundColor", backgroundColor);
        return f;
      }
    });
  }

  api.uploadCard = function(landingId, jsonData, images, callback){

    console.log(images);
    api.uploadMedia(images, landingId, function(ids){
      $http({
        method: 'POST',
        url: 'v1/pages/'+landingId+"/cards",
        headers: { 'Content-Type': undefined },
        data: { jsonData: jsonData, images: images},
        transformRequest: function(data)
        {
          var f = new FormData();
          console.log(ids)
          //f.append("title1", data.);
          f.append("medias[]", ids);
          f.append("title1", jsonData.fieldTitle[1]);
          f.append("title2", jsonData.fieldTitle[2]);
          f.append("title3", jsonData.fieldTitle[3]);
          f.append("body1", jsonData.fieldText[1]);
          f.append("body2", jsonData.fieldText[2]);
          f.append("body3", jsonData.fieldText[3]);
          f.append("image1", ids[0].id);
          f.append("image2", ids[1].id);
          f.append("image3", ids[2].id);
          f.append("color", jsonData.color);
          f.append("backgroundColor", jsonData.backgroundColor);
          f.append("type", "cardThreeColumns");
          return f;
        }
      }).then(function(data){
        callback(data);
      });
    });
  };


  api.updateCard = function(landingId, jsonData, images){
    return $http({
      method: 'POST',
      url: 'v1/pages/' + landingId + '/cards/' + jsonData.id,
      headers: {
        "X-HTTP-Method-Override": "PUT",
        'Content-Type': undefined
      },
      data: { jsonData: jsonData, images: images },
      transformRequest: function(data){
        var fdata = new FormData();
        fdata.append( "data", angular.toJson(data.jsonData) );
        data.images.forEach(function(image, index, array){
          fdata.append("image" + index, image);
        });
        fdata.append("type", "cardThreeColumns");
        return api.requestTransform(data);
      }
    });
  };

  api.updatePage = function(landing, image) {

    var cleanLanding =
    {
      "title": landing.title,
      "name": landing.name,
      "description": landing.description,
      "email": landing.owner,
      "color": landing.color,
      "backgroundColor": landing.backgroundColor
    };

    return $http({
      method: 'POST',
      url: 'v1/pages/' + landing.id,
      headers: {
        "X-HTTP-Method-Override": "PUT",
        "Content-Type": undefined
      },
      data: { landingData: cleanLanding, image: image },
      transformRequest: function(data) {
        var fdata = new FormData();
        fdata.append( 'data', angular.toJson(data.landingData) );
        fdata.append( 'logo', image );
        return fdata;
      }
    });

  }

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
