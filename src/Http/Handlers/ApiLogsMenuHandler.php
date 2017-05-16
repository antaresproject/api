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

namespace Antares\Modules\Api\Http\Handlers;

use Antares\Foundation\Support\MenuHandler;
use Antares\Contracts\Auth\Guard;

class ApiLogsMenuHandler extends MenuHandler
{

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'   => 'api-logs',
        'link' => 'antares::api/logs/index'
    ];

    /**
     * Get position.
     *
     * @return String
     */
    public function getPositionAttribute()
    {
        return $this->handler->has('logger.automation-logs') ? '>:logger.automation-logs' : '>:settings.general-config';
    }

    /**
     * Gets title attribute
     * 
     * @return String
     */
    public function getTitleAttribute()
    {

        return trans('antares/api::title.api_log');
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
        return app('antares.acl')->make('antares/api')->can('configuration');
    }

}
