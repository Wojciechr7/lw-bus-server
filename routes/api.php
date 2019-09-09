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


// API route group that we need to protect
/*Route::group(['middleware' => ['ability:admin,create-users']], function()
{
    Route::get('users', 'JwtAuthenticateController@index');
});*/

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
    Route::post('', 'AdminsController@createStop');
    Route::get('', 'AdminsController@getStops');
    Route::delete('/{id}', 'AdminsController@removeStop');
});

Route::group(['prefix' => 'routes', 'middleware' => ['ability:admin,manage-users']], function()
{
    Route::post('', 'AdminsController@createRoute');
    Route::get('', 'AdminsController@getRoutes');
    Route::get('/{id}', 'AdminsController@getRoute');
    Route::put('/{id}', 'AdminsController@editRoute');
    Route::delete('/{id}', 'AdminsController@deleteRoute');
});

Route::group(['prefix' => 'routesUser', 'middleware' => ['ability:user,manage-routes']], function()
{
    Route::get('', 'AdminsController@getRoutes');
    Route::get('/{id}', 'AdminsController@getRoute');
});

Route::group(['prefix' => 'stopsUser', 'middleware' => ['ability:user,manage-routes']], function()
{
    Route::get('', 'AdminsController@getStops');
});

Route::group(['prefix' => 'passagesUser', 'middleware' => ['ability:user,manage-routes']], function()
{
    Route::post('', 'AdminsController@createPassage');
    Route::get('/{company_id}', 'AdminsController@getPassages');
    Route::get('/show/{id}', 'AdminsController@getPassage');
    Route::put('/{id}', 'AdminsController@editPassage');
    Route::delete('/{id}', 'AdminsController@deletePassage');
});

Route::group(['prefix' => 'publicApi'], function()
{
    Route::get('/passages/{from}/{to}/{time}/{date}/{year}', 'PassagesController@getPassages');
    Route::get('/stops', 'AdminsController@getStops');
});



// Authentication route
Route::post('authenticate', 'JwtAuthenticateController@authenticate');
