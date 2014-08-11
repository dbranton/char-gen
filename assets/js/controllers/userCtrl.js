'use strict';

var userModule = angular.module('user', []);
userModule.factory('userService', function($http) {
    var service = {
        getCharacters: function() {
            return $http.get('getCharacters');
        },
        deleteCharacter: function(charId) {
            return $http.post('deleteCharacter/' + charId);
        }
    };
    return service;
});
function User($scope, userService) {

    $scope.deleteCharacter = function(charId) {
        bootbox.confirm("Are you sure you want to delete this character?", function(value) {
            if (value) {
                userService.deleteCharacter(charId).success(function() {
                    userService.getCharacters().success(function(data, status, headers, config) {
                        $scope.characters = data;   // refresh data
                    });
                });
            }
        });
    };

    $scope.init = function() {
        userService.getCharacters().success(function(data, status, headers, config) {
            $scope.characters = data;
        });
    };
}
