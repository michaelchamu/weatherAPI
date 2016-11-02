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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');
//rain routes and endpoints
Route::get('rain/{location}/dates/{from}/{to?}', 'RainController@rainByDate');
Route::get('rain/{location}/stationtype/{stationtype}/{interval}/{from}/{to?}', 'RainController@rainByStationType');
Route::get('rain/{location}/interval/{from}/{to}/{interval}','RainController@rainByInterval');
Route::get('rain/{location}/{year?}/{month?}', 'RainController@rainYearMonth');
//temperature routes and endpoints
Route::get('temperature/{location}/dates/{from}/{to?}', 'TemperatureController@temperatureByDates');
Route::get('temperature/{location}/stationtype/{stationtype}/{interval}/{from}/{to?}', 'TemperatureController@temperatureByStation');
Route::get('temperature/{location}/interval/{from}/{to}/{interval}', 'TemperatureController@temperatureByInterval');
Route::get('temperature/{location}/{year?}/{month?}', 'TemperatureController@temperatureYearMonth');
//other weather data end points
Route::get('other/{location}/dates/{from}/{to?}', 'OtherDataController@otherDataByDates');
Route::get('other/{location}/stationtype/{stationtype}/{interval}/{from}/{to?}', 'OtherDataController@otherDataByStation');
Route::get('other/{location}/interval/{from}/{to}/{interval}', 'OtherDataController@otherDataByInterval');
Route::get('other/{location}/{year?}/{month?}', 'OtherDataController@otherDataYearMonth');
