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




namespace Antares\Api\Http\Handlers;

use Antares\Api\Services\AuthProviderService;
use Antares\Foundation\Support\MenuHandler;
use Antares\Contracts\Auth\Guard;

class Menu extends MenuHandler
{

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'    => 'api-configuration',
        'title' => 'antares/api::title.configuration',
        'link'  => 'antares::api/configuration',
        'icon'  => 'zmdi-device-hub',
    ];

    /**
     * Get position.
     *
     * @return string
     */
    public function getPositionAttribute()
    {
        return $this->handler->has('dashboard') ? '^:settings' : '>:home';
    }

    /**
     * Returns the title.
     *
     * @param $value
     * @return mixed
     */
    public function getTitleAttribute($value)
    {
        return $this->container->make('translator')->trans($value);
    }

    /**
     * Check whether the menu should be displayed.
     *
     * @param  \Antares\Contracts\Auth\Guard  $auth
     *
     * @return bool
     */
    public function authorize(Guard $auth)
    {
        $service = app(AuthProviderService::class);
        $areas   = array_keys(config('areas.areas'));
        $drivers = app(AuthProviderService::class)->getAvailableDrivers();
        $enabled = false;
        foreach ($drivers as $driver => $instance) {
            foreach ($areas as $name) {
                if ($service->isDriverEnabledForArea($driver, $name)) {
                    $enabled = true;
                    break;
                }
            }
        }

        return $enabled && app('antares.acl')->make('antares/api')->can('configuration');
    }

}
