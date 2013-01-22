'use strict';

function MainController($scope, $routeParams, Response) {
  $scope.attendanceValues = [{
    "id": "Yes",
    "name": "Yes"
  },
  {
    "id": "No",
    "name": "No"
  },
  {
    "id": "Maybe",
    "name": "Maybe"
  }];

  $scope.guestsValues = [{
    "id": "1",
    "name": "Flying solo!"
  },
  {
    "id": "2",
    "name": "Two"
  },
  {
    "id": "3",
    "name": "Three"
  },
  {
    "id": "4",
    "name": "Four"
  },
  {
    "id": "5 or more",
    "name": "The whole gang!"
  }];

  $scope.attending = $scope.attendanceValues[0];
  $scope.guests = $scope.guestsValues[0];

  $scope.rsvp = function (e) {
  	$(".rsvp").addClass("flip");
  	e.preventDefault();
  };

  $scope.back = function (e) {
  	$(".rsvp").removeClass("flip");
  	e.preventDefault();
  };

  $scope.submit = function (e) {
    if ($scope.firstName && $scope.lastName) {
      
      if (!$scope.phone) {
        $scope.phone = "";
      }

      if (!$scope.email) {
        $scope.email = "";
      }

      var response = Response.add({ 
          client: "clientname",
          event: "eventname",
          attending: $scope.attending.id, 
          firstName: $scope.firstName, 
          lastName: $scope.lastName, 
          phone: $scope.phone, 
          email: $scope.email, 
          guests: $scope.guests.id
        },
        function (data) {
          if (data) {
            $(".rsvp").removeClass("flip");
          }
        });
    }
  };
}