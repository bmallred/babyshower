'use strict';

angular.module("nvite", ["nviteServices"])
    .config(["$routeProvider", "$locationProvider", function ($routeProvider, $locationProvider) {
        $locationProvider.html5Mode(true);
        
        $routeProvider.when("/", {
            controller: MainController,
            templateUrl: "partials/intro.html"
        })
        .otherwise({ redirectTo: "/" });
    }]);