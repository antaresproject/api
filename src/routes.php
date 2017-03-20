<?php

/**
 * Part of the Antares Project package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Api
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */
use Illuminate\Routing\Router;

$router->group(['prefix' => 'api'], function (Router $router) {

    $router->get('configuration', 'ConfigurationController@index')->name('api.configuration.index');
    $router->post('configuration/update', 'ConfigurationController@update')->name('api.configuration.update');

    $router->match(['GET', 'POST'], 'logs/index', 'LogsController@index');
    $router->match(['GET', 'POST'], 'user/index', 'UserController@index');
    $router->get('user/reset', 'UserController@reset');
    $router->match(['GET', 'POST'], 'user/{id?}', 'UserController@index');

    $router->resource('user', 'UserController');
});
