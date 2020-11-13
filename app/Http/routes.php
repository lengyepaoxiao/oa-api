<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return 'Not Allowed';
});

//微信登陆
$app->post('v1/oauth/login','OauthController@login');
$app->post('v1/user/save_user_info','UserController@saveUserInfo');
$app->post('v1/user/get_user_info','UserController@getUserInfo');
$app->post('v1/user/get_task_list','UserController@getTaskList');



$app->post('v1/task/get_lists', 'TaskController@getLists');
$app->post('v1/task/get_info', 'TaskController@getInfo');
$app->post('v1/task/upload_task_img', 'TaskController@uploadTaskImg');
$app->post('v1/task/change_user_task', 'TaskController@changeUserTask');











