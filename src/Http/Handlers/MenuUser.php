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

namespace Antares\Modules\Api\Http\Handlers;

use Antares\Modules\Api\Services\AuthProviderService;

class MenuUser
{

    /**
     *
     * @var AuthProviderService
     */
    protected $authProviderService;

    /**
     * Construct
     * 
     * @param AuthProviderService $authProviderService
     */
    public function __construct(AuthProviderService $authProviderService)
    {
        $this->authProviderService = $authProviderService;
    }

    /**
     * Composing menu on view event
     * 
     * @return \Antares\Foundation\Support\MenuHandler
     */
    public function compose()
    {
        if ($this->authProviderService->getEnabledDrivers()->isEmpty()) {
            return false;
        }


        return app('antares.widget')
                        ->make('menu.control.pane')
                        ->add('api')
                        ->link(handles('antares::api/user/index'))
                        ->title(trans('antares/api::title.configuration'))
                        ->icon('zmdi-device-hub');
    }

}
