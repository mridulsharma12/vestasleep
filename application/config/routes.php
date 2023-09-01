<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
// $route['default_controller'] = 'welcome';
$route['default_controller'] = 'Authentication';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// $route['add'] = 'ApiController/add';
// $route['find/(:any)'] = 'ApiController/find/$1';
// $route['update/(:any)'] = 'ApiController/update/$1';
// $route['delete/(:any)'] = 'ApiController/delete/$1';

$route['signin'] = 'Authentication/signin';
$route['signup'] = 'Authentication/signup';
$route['details'] = 'Authentication/details';
$route['profileupdate'] = 'Authentication/profileUpdate';
$route['updateaddress'] = 'Authentication/updateAddress';
$route['updatepersonaldetails'] = 'Authentication/updatePersonalDetails';
$route['logout'] = 'Authentication/logout';

$route['emergencycontact'] = 'EmergencyContact/emergencyContact';
$route['updateemergencycontact'] = 'EmergencyContact/updateEmergencyContact';

$route['forgetpassword'] = 'ForgetPassword/forgetPassword';

$route['getstate'] = 'States/getState';
$route['addstate'] = 'States/addState';

// $route['fetch'] = 'Authentication/fetch';