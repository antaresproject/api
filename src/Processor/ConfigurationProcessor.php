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

namespace Antares\Modules\Api\Processor;

use Antares\Modules\Api\Contracts\Listeners\Configuration\Updater as ConfigurationUpdaterListener;
use Antares\Modules\Api\Contracts\Listeners\Configuration\Viewer as ConfigurationViewerListener;
use Antares\Modules\Api\Http\Presenters\ConfigurationPresenter;
use Antares\Modules\Api\Services\AuthProviderService;
use Exception;
use Log;

class ConfigurationProcessor
{

    /**
     * @var AuthProviderService
     */
    protected $authProviderService;

    /**
     * @var ConfigurationPresenter
     */
    protected $presenter;

    /**
     * ConfigurationProcessor constructor.
     * @param AuthProviderService $authProviderService
     * @param ConfigurationPresenter $presenter
     */
    public function __construct(AuthProviderService $authProviderService, ConfigurationPresenter $presenter)
    {
        $this->authProviderService = $authProviderService;
        $this->presenter           = $presenter;
    }

    /**
     * @param ConfigurationViewerListener $listener
     * @return mixed
     */
    public function index(ConfigurationViewerListener $listener)
    {
        $availableDrivers = $this->authProviderService->getAvailableDrivers();
        $enabledDrivers   = $this->authProviderService->getEnabledDrivers();
        $form             = $this->presenter->authDrivers($this->authProviderService);
        $title            = trans('antares/api::title.configuration');

        $data = compact('availableDrivers', 'enabledDrivers', 'form', 'title');

        return $listener->showAuthDrivers($data);
    }

    /**
     * Updates api providers configuration
     * 
     * @param ConfigurationUpdaterListener $listener
     * @param array $input
     * @return type
     */
    public function update(ConfigurationUpdaterListener $listener, array $input)
    {
        try {

            foreach (array_get($input, 'drivers', []) as $driver => $config) {
                $this->authProviderService->updateDriver($driver, $config);
                event('api.driver.' . $driver . '.update', [$input, $this->authProviderService]);
            }

            $this->authProviderService->save();
        } catch (Exception $e) {
            Log::emergency($e);
            return $listener->updateAuthFailed(['error' => $e->getMessage()]);
        }

        return $listener->authUpdated();
    }

}
