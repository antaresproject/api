<?php

/**
 * Part of the Antares package.
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
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Modules\Api\Services;

use Illuminate\Support\Collection;
use Antares\Model\Component;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Cache\Repository as Cache;
use Exception;

class AuthProviderService
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * Registered auth drivers.
     *
     * @var Collection
     */
    protected $authDrivers;

    /**
     * Cache key
     *
     * @var string
     */
    protected static $cacheKey = 'api_auth_drivers';

    /**
     * @var array
     */
    protected $driversToUpdate = [];

    /**
     * AuthProviderService constructor.
     * @param Container $container
     * @param Cache $cache
     * @param array $authDrivers
     */
    public function __construct(Container $container, Cache $cache, array $authDrivers = null)
    {
        $this->container   = $container;
        $this->cache       = $cache;
        $this->authDrivers = new Collection();

        if (!empty($authDrivers)) {
            foreach ($authDrivers as $driverClassName) {
                $driver = $this->container->make($driverClassName);
                $this->authDrivers->put($driver->getName(), $driver);
            }
        }
    }

    /**
     * Returns available auth drivers.
     *
     * @return Collection
     */
    public function getAvailableDrivers()
    {
        return $this->authDrivers;
    }

    /**
     * Returns enabled auth drivers.
     *
     * @return Collection
     */
    public function getEnabledDrivers()
    {
        $drivers = $this->getDriversFromStorage();
        $enabled = new Collection();

        foreach ($this->getAvailableDrivers() as $driver) {
            $name = $driver->getName();

            if (isset($drivers[$name])) {
                $enabled->put($name, $driver);
            }
        }

        return $enabled;
    }

    /**
     * Driver getter
     * 
     * @param String $driver
     * @return mixed
     */
    public function getDriver($driver)
    {
        $this->validateDriver($driver);
        return array_get($this->authDrivers, $driver);
    }

    /**
     * Check if the given driver is enabled.
     *
     * @param string $driver
     * @return bool
     * @throws Exception
     */
    public function isDriverEnabled($driver)
    {
        $this->validateDriver($driver);

        $drivers = $this->getDriversFromStorage();

        return isset($drivers[$driver]);
    }

    /**
     * Check if the given driver is enabled for area.
     *
     * @param String $driver
     * @param String $name
     * @return bool
     * @throws Exception
     */
    public function isDriverEnabledForArea($driver, $name)
    {
        $drivers = $this->getDriversFromStorage();
        return (int) isset($drivers[$driver][$name]) ? $drivers[$driver][$name] : 0;
    }

    /**
     * Add the given driver to the update process.
     *
     * @param string $driver
     * @param array $settings
     * @throws Exception
     */
    public function updateDriver($driver, array $settings = [])
    {
        $this->validateDriver($driver);
        $this->driversToUpdate[$driver] = $settings;
    }

    /**
     * Check if the given driver name is available.
     *
     * @param string $driver
     * @throws Exception
     */
    protected function validateDriver($driver)
    {
        if (!$this->authDrivers->has($driver)) {
            throw new Exception('The ' . $driver . ' driver is not available.');
        }
    }

    /**
     * Returns auth drivers from the storage.
     *
     * @return array
     */
    protected function getDriversFromStorage()
    {
        if ($this->cache->has(self::$cacheKey)) {
            return $this->cache->get(self::$cacheKey, []);
        }

        $options = Component::findOneByName('antaresproject/module-api')->options;
        $drivers = array_get($options, 'auth_drivers');

        $this->cache->put(self::$cacheKey, $drivers);

        return $drivers;
    }

    /**
     * Save auth drivers in the storage.
     */
    public function save()
    {


        $component = Component::findOneByName('antaresproject/module-api');
        $options   = $component->options;

        if (!isset($options['auth_drivers'])) {
            $options['auth_drivers'] = [];
        }
        $options['auth_drivers'] = $this->driversToUpdate;
        $component->options      = $options;
        $component->save();

        $this->cache->forget(self::$cacheKey);

        $this->container->make('antares.memory')->make('component')->getHandler()->forgetCache();
        $this->driversToUpdate = [];
    }

}
