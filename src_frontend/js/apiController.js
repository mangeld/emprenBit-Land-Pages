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
