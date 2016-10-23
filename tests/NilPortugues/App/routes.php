<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::group(['prefix' => ''], function () {
        Route::resource('employees', 'EmployeesController');
        Route::get('employees/{employee_id}/orders', ['as' => 'employees.orders', 'uses' => 'EmployeesController@getOrdersByEmployee']);
        Route::resource('orders', 'OrdersController');
    });
