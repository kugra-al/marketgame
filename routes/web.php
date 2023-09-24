<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\AdminGameController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\AdminItemController;
use App\Http\Controllers\AdminBuildingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

require __DIR__.'/auth.php';

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//App\Models\Game::newGame();
Route::group(['middleware' => ['auth']], function() {

	Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');

	Route::resource('player', PlayerController::class);

	Route::get('/city/{id}', [CityController::class, 'show'])->name('city.view');
	Route::get('/city/data/{id}', [CityController::class, 'getData'])->name('city.data');
	Route::get('/city', [CityController::class, 'index'])->name('city.index');
	Route::post('/city/building/upgrade', [CityController::class, 'buildingUpgrade'])->name('city.building.upgrade');
	Route::post('/city/upgrade', [CityController::class, 'cityUpgrade'])->name('city.upgrade');
	Route::post('/city/building/create', [CityController::class, 'buildingCreate'])->name('city.building.create');

	Route::post('/city/army/send', [CityController::class, 'sendArmy'])->name('city.army.send');
	Route::post('/city/army/move', [CityController::class, 'moveArmy'])->name('city.army.move');

	Route::post('/city/buy/troop', [CityController::class, 'buyTroop'])->name('city.buy.troop');
	Route::post('/city/buy/transport', [CityController::class, 'buyTransport'])->name('city.buy.transport');

	Route::post('/city/craft/item',[CityController::class, 'craftItem'])->name('city.craft.item');
	Route::post('/city/craft/delete',[CityController::class, 'deleteCraft'])->name('city.craft.delete');
	Route::post('/city/craft/edit',[CityController::class, 'editCraft'])->name('city.craft.edit');

	Route::get('/map', [MapController::class, 'index'])->name('map');
	Route::post('/map/update', [MapController::class, 'getMapData'])->name('map.update');
	Route::get('/market', [MarketController::class, 'index'])->name('market');
	Route::post('/market', [MarketController::class, 'order'])->name('market.order');
	Route::post('/market/order/{id}', [MarketController::class, 'orderAccept'])->name('market.order.accept');
	Route::post('/market/order/{id}/delete', [MarketController::class, 'orderDelete'])->name('market.order.delete');

	//Route::group(['middleware' => ['role:admin']], function() {
		Route::prefix('admin')->group(function() {
			Route::get('/game', [AdminGameController::class, 'index'])->name('admin.game');
			Route::post('/game', [AdminGameController::class, 'post']);
			Route::resource('/user', AdminUserController::class);
			Route::resource('/item', AdminItemController::class,['as'=>'admin']);
			Route::resource('/building', AdminBuildingController::class,['as'=>'admin']);
		});
	//});
});








