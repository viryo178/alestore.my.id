<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
$route['default_controller'] = 'auth/login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = TRUE;

$route['login'] = 'auth/login';
$route['logout'] = 'auth/logout';
$route['admin/dashboard'] = 'dashboard';
$route['orders/create'] = 'orders/create';
$route['orders/available-account'] = 'orders/available_account';
$route['orders/update/(:num)'] = 'orders/update/$1';
$route['orders/quick'] = 'orders/quick_store';
$route['financial-reports/download'] = 'financial_reports/download';
$route['digital-accounts/feed'] = 'digital_accounts/feed';
$route['digital-accounts/password-expired'] = 'digital_accounts/password_expired';
$route['digital-accounts/password-expired/update/(:num)'] = 'digital_accounts/update_expired_password/$1';
$route['digital-accounts/password-expired/bulk-update'] = 'digital_accounts/bulk_update_expired_password';
$route['digital-accounts/bulk/create'] = 'digital_accounts/bulk_create';
$route['digital-accounts/bulk'] = 'digital_accounts/bulk_store';
$route['digital-accounts/stock'] = 'digital_accounts/stock_store';
$route['digital-accounts/products'] = 'digital_accounts/product_store';
$route['digital-accounts/products/(:num)'] = 'digital_accounts/product_update/$1';
$route['digital-accounts/products/delete/(:num)'] = 'digital_accounts/product_destroy/$1';
$route['users/change-password'] = 'users/change_password';
$route['users/update-password'] = 'users/update_password';
$route['products/(:num)/variations'] = 'products/store_variation/$1';
$route['expire-durations/(:num)/default'] = 'expire_durations/set_default/$1';
$route['warranty-claims/(:num)/approve'] = 'warranty_claims/approve/$1';
$route['warranty-claims/(:num)/reject'] = 'warranty_claims/reject/$1';
