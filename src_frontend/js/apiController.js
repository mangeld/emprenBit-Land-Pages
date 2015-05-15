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

  api.uploadCard = function(landingId, jsonData, images){

    return $http({
      method: 'POST',
      url: 'v1/pages/'+landingId+"/cards",
      headers: { 'Content-Type': undefined },
      data: { jsonData: jsonData, images: images},
      transformRequest: function(data){ return api.requestTransform(data); }
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
      "email": landing.owner
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
