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

Route::post('login', 'LoginControllerAPI@login')->name('login');
Route::middleware('auth:api')->post('logout',
    'LoginControllerAPI@logout')->name('logout');

Route::middleware('auth:api')->get('users/me', 'UserControllerAPI@myProfile');

Route::get('/users/list', 'UserControllerAPI@listUsers');
Route::get('/wallets/num', 'WalletControllerAPI@numWallets');
Route::get('/wallets/email/{search}', 'WalletControllerAPI@getWalletsEmail');
Route::post('/users', 'UserControllerAPI@store');
Route::post('/wallets', 'WalletControllerAPI@store');
Route::post('/movements', 'MovementControllerAPI@store');
Route::get('/my/wallet', 'WalletControllerAPI@listMovements');
Route::get('/wallets/id/{email}','WalletControllerAPI@getWallet');
Route::post('/user/create','UserControllerAPI@createUser');
Route::get('/my/movements', 'MovementControllerAPI@getMyMovements');
Route::post('/movements/expense', 'MovementControllerAPI@storeExpense');
Route::get('/movements/categories', 'MovementControllerAPI@getCategories');
Route::put('/movements/{id}', 'MovementControllerAPI@update');

Route::put('/user/{id}', 'UserControllerAPI@update');
Route::delete('users/{id}', 'UserControllerAPI@destroy');
Route::get('users', 'UserControllerAPI@index');
Route::get('wallets', 'WalletControllerAPI@index');
Route::patch('users/disable/{id}', 'UserControllerAPI@disableUser');
Route::patch('users/active/{id}', 'UserControllerAPI@activeUser');
Route::get('/statics/categories','MovementControllerAPI@getStaticsByCategory');
Route::get('/statics/type','MovementControllerAPI@getStaticByType');
Route::get('/global/statics/categories','MovementControllerAPI@getAllStaticsByCategory');
Route::get('/global/statics/type','MovementControllerAPI@getAllStaticByType');
Route::get('/global/statics/usersPerType','UserControllerAPI@getUsersPerType');
Route::get('/global/statics/moneyForMonth','MovementControllerAPI@getMoneyForMonth');
Route::get('/global/statics/moneyForYear','MovementControllerAPI@getMoneyForYear');
Route::get('/movements/category/{search}','MovementControllerAPI@getMovementsCategory');

Route::get('/sum','MovementControllerAPI@sum');
