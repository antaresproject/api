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

namespace Antares\Modules\Api\Http\Breadcrumb;

use Antares\Breadcrumb\Navigation;

class ConfigurationBreadcrumb extends Navigation
{

    /**
     *
     * @var string
     */
    protected static $name = 'api';

    /**
     * Register a breadcrumb on an admin index page.
     */
    public function onIndex()
    {
        $this->breadcrumbs->register(self::$name, function($breadcrumbs) {
            $breadcrumbs->push('General Configuration');
        });

        $this->shareOnView(self::$name);
    }

    /**
     * Shows breadrcumb on user config
     */
    public function onUserConfig()
    {
        $this->breadcrumbs->register(self::$name, function($breadcrumbs) {
            $breadcrumbs->push('Api');
        });

        $this->shareOnView(self::$name);
    }

    /**
     * Shows breadrcumb on api logs
     */
    public function onApiLogs()
    {
        $this->breadcrumbs->register(self::$name, function($breadcrumbs) {
            $breadcrumbs->push(trans('antares/api::title.api_log'));
        });
        $this->shareOnView(self::$name);
    }

}
