<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route to create a new role
Route::post('role', 'JwtAuthenticateController@createRole');
// Route to create a new permission
Route::post('permission', 'JwtAuthenticateController@createPermission');
// Route to assign role to user
Route::post('assign-role', 'JwtAuthenticateController@assignRole');
// Route to attache permission to a role
Route::post('attach-permission', 'JwtAuthenticateController@attachPermission');

Route::group(['prefix' => 'users', 'middleware' => ['ability:admin,manage-users']], function()
{
    Route::get('/get', 'AdminsController@getUsers');
    Route::get('/get/{id}', 'AdminsController@getUser');
    Route::put('/edit/{id}', 'AdminsController@editUser');
    Route::post('/create', 'AdminsController@createUser');
    Route::post('/changePassword/{id}', 'AdminsController@changeUserPassword');
    Route::delete('/delete/{id}', 'AdminsController@deleteUser');
});

Route::group(['prefix' => 'stops', 'middleware' => ['ability:admin,manage-users']], function()
{
    Route::post('', 'StopsController@createStop');
    Route::get('', 'StopsController@getStops');
    Route::delete('/{id}', 'StopsController@removeStop');
});

Route::group(['prefix' => 'routes', 'middleware' => ['ability:admin,manage-users']], function()
{
    Route::post('', 'TemplatesController@createTemplate');
    Route::get('', 'TemplatesController@getTemplates');
    Route::get('/{id}', 'TemplatesController@getTemplate');
    Route::put('/{id}', 'TemplatesController@editTemplate');
    Route::delete('/{id}', 'TemplatesController@deleteTemplate');
});

Route::group(['prefix' => 'templatesUser', 'middleware' => ['ability:user,manage-routes']], function()
{
    Route::get('', 'TemplatesController@getTemplates');
    Route::get('/{id}', 'TemplatesController@getTemplate');
});

Route::group(['prefix' => 'stopsUser', 'middleware' => ['ability:user,manage-routes']], function()
{
    Route::get('', 'StopsController@getStops');
});

Route::group(['prefix' => 'passagesUser', 'middleware' => ['ability:user,manage-routes']], function()
{
    Route::post('', 'PassagesController@createPassage');
    Route::get('/{company_id}', 'PassagesController@getUserPassages');
    Route::get('/show/{id}', 'PassagesController@getPassage');
    Route::put('/{id}', 'PassagesController@editPassage');
    Route::delete('/{id}', 'PassagesController@deletePassage');
});

Route::group(['prefix' => 'publicApi'], function()
{
    Route::get('/passages/{from}/{to}/{time}/{date}/{year}', 'PassagesController@getPassages');
    Route::get('/stops', 'StopsController@getStops');
    Route::get('/buses', 'AdminsController@getCompanies');
});

Route::group(['prefix' => 'test'], function()
{
    Route::post('create', 'TestController@createTestPassage');
});

// Authentication route
Route::post('authenticate', 'JwtAuthenticateController@authenticate');
