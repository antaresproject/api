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




namespace Antares\Api\Processor;

use Antares\Api\Http\Breadcrumb\ConfigurationBreadcrumb;
use Antares\Routing\Traits\ControllerResponseTrait;
use Antares\Api\Services\AuthProviderService;
use Antares\Html\Form\FormBuilder;
use Antares\Api\Http\Form\User;
use Antares\Api\Model\ApiUsers;

class UserProcessor
{

    use ControllerResponseTrait;

    /**
     * AuthProviderService instance
     *
     * @var AuthProviderService
     */
    protected $authProviderService;

    /**
     * Breadcrumb instance
     *
     * @var ConfigurationBreadcrumb 
     */
    protected $breadcrumb;

    /**
     * Construct
     * 
     * @param ConfigurationBreadcrumb $breadcrumb
     */
    public function __construct(ConfigurationBreadcrumb $breadcrumb, AuthProviderService $authProviderService)
    {

        $this->breadcrumb          = $breadcrumb;
        $this->authProviderService = $authProviderService;
    }

    /**
     * User api configuration
     * 
     * @param mixed $id
     * @return mixed
     */
    public function index($id = null)
    {
        if ($this->authProviderService->getEnabledDrivers()->isEmpty()) {
            return $this->redirectWithMessage(handles('antares/foundation::account'), trans('antares/api::response.no_active_api_providers'), 'error');
        }

        $this->breadcrumb->onUserConfig();
        $model = ApiUsers::query()->firstOrCreate(['user_id' => user($id)->id]);
        $form  = $this->getForm($model);
        event("antares.form: user.api", [$model, $form]);
        return view('antares/api::admin.user.index', compact('form'));
    }

    /**
     * Form getter
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return FormBuilder
     */
    protected function getForm($model)
    {
        return new FormBuilder(new User($model));
    }

    /**
     * Updates user api configuration
     * 
     * @param type $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id)
    {
        $model = ApiUsers::query()->findOrFail($id);
        $form  = $this->getForm($model);
        $url   = url()->previous();
        if (!$form->isValid()) {
            return $this->redirectWithErrors($url, $form->getMessageBag());
        }
        $model->enabled = input('api', 'off') === 'on';
        if (!is_null($whitelist      = input('whitelist'))) {
            $model->whitelist = $whitelist;
        }
        if (!$model->save()) {
            return $this->redirectWithMessage($url, trans('antares/api::response.user_config_not_saved'), 'error');
        }
        return $this->redirectWithMessage($url, trans('antares/api::response.user_config_saved'));
    }

    /**
     * Resets provider token
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset()
    {
        $provider = input('provider');
        $driver   = $this->authProviderService->getDriver($provider);
        $driver->reset();
        return $this->redirectWithMessage(url()->previous(), trans('antares/api::response.user_config_token_reseted', ['name' => $driver->getLegend()]));
    }

}
