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

namespace Antares\Modules\Api\Http\Presenters;

use Antares\Modules\Api\Services\AuthProviderService;
use Antares\Modules\Api\Contracts\AuthProviderContract;
use Antares\Modules\Api\Http\Breadcrumb\ConfigurationBreadcrumb;
use Antares\Contracts\Html\Form\Factory as FormFactory;
use Antares\Contracts\Html\Form\Fieldset;
use Antares\Contracts\Html\Form\Grid as FormGrid;

class ConfigurationPresenter
{

    /**
     * @var ConfigurationBreadcrumb
     */
    protected $breadcrumb;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * ConfigurationPresenter constructor.
     * @param ConfigurationBreadcrumb $breadcrumb
     * @param FormFactory $formFactory
     */
    public function __construct(ConfigurationBreadcrumb $breadcrumb, FormFactory $formFactory)
    {
        $this->breadcrumb  = $breadcrumb;
        $this->formFactory = $formFactory;
    }

    /**
     * @param AuthProviderService $authProviderService
     * @return \Antares\Contracts\Html\Builder
     */
    public function authDrivers(AuthProviderService $authProviderService)
    {
        $this->breadcrumb->onIndex();
        publish('api', ['js/switcher.js']);
        return $this->formFactory->of('antares.api.configuration.index', function(FormGrid $form) use($authProviderService) {
                    $url = handles('api.configuration.update');

                    $form->name('API Auth Drivers Configuration');
                    $form->simple($url, ['id' => 'api-configuration-form']);
                    $form->layout('antares/api::admin.configuration.form');

                    $authFieldset = trans('antares/api::title.auth_drivers');
                    foreach ($authProviderService->getAvailableDrivers() as $availableDriver) {

                        $form->fieldset($authFieldset, function(Fieldset $fieldset) use($authProviderService, $availableDriver) {
                            $this->setupAuthDriver($fieldset, $authProviderService, $availableDriver);
                        });
                        $driver = $availableDriver->getName();
                        $form->fieldset($driver, function(Fieldset $fieldset) use($driver, $authProviderService) {
                            $areas = config('areas.areas');
                            $fieldset->layout('antares/api::admin.partials._area');

                            foreach ($areas as $name => $title) {
                                $control = $fieldset->control('input:checkbox', "drivers[$driver][$name]")
                                        ->label($title . ' area')
                                        ->value(1);
                                if ($authProviderService->isDriverEnabledForArea($driver, $name)) {
                                    $control->checked();
                                }
                            }
                        });
                    }

                    $form->fieldset(null, function(Fieldset $fieldset) {
                        $buttonAttrs = [
                            'type'  => 'submit',
                            'class' => 'btn btn-primary',
                        ];
                        $fieldset->control('button', 'cancel')
                                ->field(function() {
                                    return app('html')->link(handles("antares::/"), trans('antares/foundation::label.cancel'), ['class' => 'btn btn--md btn--default mdl-button mdl-js-button']);
                                });
                        $fieldset->control('button', 'button')
                                ->attributes($buttonAttrs)
                                ->value(trans('antares/foundation::label.save_changes'));
                    });
                });
    }

    /**
     * Creates form controls for every api provider
     * 
     * @param Fieldset $fieldset
     * @param AuthProviderService $authProviderService
     * @param AuthProviderContract $availableAuthDriver
     */
    protected function setupAuthDriver(Fieldset $fieldset, AuthProviderService $authProviderService, AuthProviderContract $availableAuthDriver)
    {


        $attributes = [
            'id'    => 'api-configuration-' . $availableAuthDriver->getName() . '-driver',
            'class' => 'api-auths'
        ];

        $isEnabled = $authProviderService->isDriverEnabled($availableAuthDriver->getName());

        if ($isEnabled) {
            $attributes['checked'] = 'checked';
        }

        $fieldset->control('input:checkbox', '')
                ->label('<p><b>' . $availableAuthDriver->getLabel() . '</b> - &nbsp;' . $availableAuthDriver->getDescription() . '</p>')
                ->value($availableAuthDriver->getName())
                ->attributes($attributes);
    }

}
