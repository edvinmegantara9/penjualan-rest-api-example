<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api/v1'], function () use ($router) {
    $router->get('barang', 'BarangController@index');
    $router->post('barang', 'BarangController@store');
    $router->get('barang/{id}', 'BarangController@show');
    $router->put('barang/{id}', 'BarangController@update');
    $router->delete('barang/{id}', 'BarangController@destroy');

    $router->get('penjualan', 'PenjualanController@index');
    $router->post('penjualan', 'PenjualanController@store');
    $router->get('penjualan/{id}', 'PenjualanController@show');
    $router->put('penjualan/{id}', 'PenjualanController@update');
    $router->delete('penjualan/{id}', 'PenjualanController@destroy');

    $router->get('pembelian', 'PembelianController@index');
    $router->post('pembelian', 'PembelianController@store');
    $router->get('pembelian/{id}', 'PembelianController@show');
    $router->put('pembelian/{id}', 'PembelianController@update');
    $router->delete('pembelian/{id}', 'PembelianController@destroy');
});
