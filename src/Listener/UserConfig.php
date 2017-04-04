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

namespace Antares\Api\Listener;

use Antares\Api\Http\Presenters\ConfigurationPresenter;
use Antares\Api\Services\AuthProviderService;
use Illuminate\Database\Eloquent\Model;
use Antares\Html\Form\Grid as FormGrid;
use Antares\Api\Providers\AuthProvider;
use Antares\Html\Form\FormBuilder;
use Antares\Html\Form\Fieldset;

class UserConfig
{

    /**
     *
     * @var AuthProviderService
     */
    protected $authProviderService;

    /**
     * @var ConfigurationPresenter
     */
    protected $presenter;

    /**
     * UserConfig constructor.
     * @param AuthProviderService $authProviderService
     * @param ConfigurationPresenter $presenter
     */
    public function __construct(AuthProviderService $authProviderService, ConfigurationPresenter $presenter)
    {
        $this->authProviderService = $authProviderService;
        $this->presenter           = $presenter;
    }

    /**
     * Handles event
     *
     * @param Model $user
     * @param FormBuilder $form
     */
    public function handle(Model $user, FormBuilder $form)
    {
        $this->extendForm($user, $form);
    }

    /**
     * Add a link to reset configuration inside the user edit form.
     *
     * @param Model $api
     * @param FormBuilder $form
     */
    protected function extendForm(Model $api, FormBuilder $form)
    {
        $drivers = $this->getDriversConfigurableByUser();
        if (empty($drivers) or ! $api->enabled) {
            return;
        }

        $form->extend(function(FormGrid $form) use($drivers, $api) {
            $fieldsetName = trans('antares/api::title.enabled_auth_drivers');

            foreach ($drivers as $name => $driver) {
                if (!$this->authProviderService->isDriverEnabledForArea($driver->getName(), user()->getArea())) {
                    continue;
                }
                $form->findFieldsetOrCreateNew($name, function(Fieldset $fieldset) use($driver, $api) {

                    $presenter = $driver->getPresenter();
                    if ($presenter) {
                        $presenter->fieldset($fieldset, $api->user);
                    }
                });
            }
        });
    }

    /**
     * @return AuthProvider[]
     */
    protected function getDriversConfigurableByUser()
    {
        $drivers      = $this->authProviderService->getEnabledDrivers();
        $configurable = [];

        foreach ($drivers as $driver) {
            if ($driver->isConfigurablePerUser()) {
                $configurable[] = $driver;
            }
        }

        return $configurable;
    }

}
