'use strict';

var app = angular.module('myApp', [
  'ngRoute',
  'ngSanitize',
  'ui.bootstrap',
  'ui.select',
  'angular-momentjs',
  'ngDialog',
  'ngTable',
  'myApp.version',
  'myApp.range',
  'myApp.database',
  'myApp.utilities',
  'oauth',
  'angular-confirm',
  'chart.js',
  'ui.bootstrap.datetimepicker'
])
/*.config(function($locationProvider) { // OAuth html5 mode seems to break our routing
    $locationProvider.html5Mode(true).hashPrefix('!')
})*/
.config([
      '$routeProvider', function($routeProvider) {
    $routeProvider.when('/access_token=:accessToken', {
        template: '',
        controller: function ($location, AccessToken) {
            var hash = $location.path().substr(1);
            AccessToken.setTokenFromString(hash);
            $location.path('/');
            $location.replace();
        }
    });
	$routeProvider.when('/boat/checkout/:boat_id', {
	  templateUrl: 'templates/boat/checkout.html',
	  controller: 'BoatCtrl'
	});
	$routeProvider.when('/rowers/', {
	  templateUrl: 'templates/rowers/rower.html',
	  controller: 'RowerCtrl'
	});
	$routeProvider.when('/damages/', {
	  templateUrl: 'templates/damages.html',
	  controller: 'BoatCtrl'
	});
	$routeProvider.when('/today/', {
	  templateUrl: 'templates/today.html',
	  controller: 'TodayCtrl'
	});
	$routeProvider.when('/admin/', {
	  templateUrl: 'templates/admin.html',
	  controller: 'AdminCtrl'
	});
	$routeProvider.when('/ud/', {
	  templateUrl: 'templates/boat/checkout.html',
	  controller: 'BoatCtrl'
	});
	$routeProvider.when('/ind/', {
	  templateUrl: 'templates/boat/checkin.html',
	  controller: 'BoatCtrl'
	});
	$routeProvider.when('/statoverview/', {
	  templateUrl: 'templates/stats/statoverview.html',
	  controller: 'StatCtrl'
	});
	$routeProvider.when('/', {redirectTo: '/ud'});
	$routeProvider.otherwise({
	  templateUrl: 'templates/notimplementet.html',
	});
      }])
    .config(['uiSelectConfig', function(uiSelectConfig) {
      uiSelectConfig.theme = 'bootstrap';
    }])
    .config(['ChartJsProvider', function (ChartJsProvider) {
    // Configure all charts
    ChartJsProvider.setOptions({
      colours: ['#FF5252', '#FF8A80'],
      animation: false
    });
    // Configure all line charts
    ChartJsProvider.setOptions('Line', {
      datasetFill: true
    });
  }])
;
