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

namespace Antares\Modules\Api\Providers;

use Antares\Modules\Api\Contracts\AuthProviderContract;
use Antares\Modules\Api\Contracts\AuthProviderPresenterContract;
use Illuminate\Contracts\Container\Container;

abstract class AuthProvider implements AuthProviderContract
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $label;

    /**
     * AuthProvider constructor.
     * @param Container $container
     * @param $name
     * @param $label
     */
    public function __construct(Container $container, $name, $label)
    {
        $this->container = $container;
        $this->name      = $name;
        $this->label     = $label;
    }

    /**
     * Returns the name (id) of the auth provider.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the user friendly label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return AuthProviderPresenterContract | null
     */
    public abstract function getPresenter();

    /**
     * Extends the Auth by the provider.
     */
    public abstract function registerAuth();

    /**
     * Documentation links getter
     * 
     * @return String
     */
    public function getDocumentationLink()
    {
        return '';
    }

    /**
     * Reset link getter
     * 
     * @return String
     */
    public function getResetLink()
    {
        return '';
    }

    /**
     * Legend getter
     * 
     * @return String
     */
    public function getLegend()
    {
        return $this->label;
    }

    /**
     * Resets api key
     * 
     * @return String
     */
    public function reset()
    {
        return;
    }

}
