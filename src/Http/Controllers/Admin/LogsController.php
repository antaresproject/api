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

namespace Antares\Modules\Api\Http\Controllers\Admin;

use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Modules\Api\Http\Breadcrumb\ConfigurationBreadcrumb;
use Antares\Modules\Api\Http\Datatable\Logs;

class LogsController extends AdminController
{

    /**
     * Setup controller middleware.
     *
     * @return void
     */
    protected function setupMiddleware()
    {
        $this->middleware('antares.auth');
    }

    /**
     * Shows Api Logs
     * 
     * @param Logs $datatable
     * @param ConfigurationBreadcrumb $breadcrumb
     * @return \Illuminate\View\View
     */
    public function index(Logs $datatable, ConfigurationBreadcrumb $breadcrumb)
    {
        $breadcrumb->onApiLogs();
        return $datatable->render('antares/api::admin.logs.index');
    }

}
