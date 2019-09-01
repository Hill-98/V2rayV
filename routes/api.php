<?php
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
Route::get("server-all", "ServerController@all");
Route::prefix("server")->group(function () {
    Route::post("switch", "ServerController@switch");
    Route::get("ping", "ServerController@ping");
    Route::get("export-config", "ServerController@exportConfig");
    Route::prefix("share-url")->group(function () {
        Route::get("/export", "ShareURLController@export");
        Route::post("/import", "ShareURLController@import");
    });
});
Route::resource("server", "ServerController")->except([
    "create", "edit"
]);

Route::get("routing-all", "RoutingController@all");
Route::prefix("routing")->group(function () {
    Route::post("switch", "RoutingController@switch");
});
Route::resource("routing", "RoutingController")->except([
    "create", "edit"
]);

Route::prefix("dns")->group(function () {
    Route::get("/", "DnsController@get");
    Route::put("/", "DnsController@put");
});

Route::get("subscribe-all", "SubscribeController@all");
Route::post("subscribe/update", "SubscribeController@subscribeUpdate");
Route::resource("subscribe", "SubscribeController")->except([
    "create", "edit"
]);

Route::prefix("setting")->group(function () {
    Route::get("/", "SettingController@get");
    Route::put("/", "SettingController@save");
    Route::get("/main-server", "SettingController@getMainServer");
    Route::put("/main-server", "SettingController@setMainServer");
});

Route::prefix("version")->group(function () {
    Route::get("/", "VersionController@show");
    Route::get("/check", "VersionController@checkUpdate");
});
