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

namespace Antares\Api\Http\Controllers\Admin;

use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Api\Processor\ConfigurationProcessor;
use Antares\Api\Contracts\Listeners\Configuration\Viewer as ConfigurationViewerListener;
use Antares\Api\Contracts\Listeners\Configuration\Updater as ConfigurationUpdaterListener;
use Illuminate\Http\Request;

class ConfigurationController extends AdminController implements ConfigurationViewerListener, ConfigurationUpdaterListener
{

    /**
     * @var ConfigurationProcessor
     */
    protected $processor;

    /**
     * ConfigurationController constructor.
     * @param ConfigurationProcessor $processor
     */
    public function __construct(ConfigurationProcessor $processor)
    {
        parent::__construct();

        $this->processor = $processor;
    }

    /**
     * Setup controller middleware.
     *
     * @return void
     */
    protected function setupMiddleware()
    {
        $this->middleware('antares.auth');
    }

    public function index()
    {
        return $this->processor->index($this);
    }

    public function update(Request $request)
    {
        return $this->processor->update($this, $request->all());
    }

    /**
     * Response when list available auth drivers page succeed.
     *
     * @param array $data
     * @return mixed
     */
    public function showAuthDrivers(array $data)
    {
        set_meta('title', $data['title']);

        return view('antares/api::admin.configuration.index', $data);
    }

    /**
     * Response when updating auth driver failed.
     *
     * @param array $errors
     * @return mixed
     */
    public function updateAuthFailed(array $errors)
    {
        $message = trans('antares/api::response.configuration.update.failed');
        $url     = handles('api.configuration.index');

        return $this->redirectWithMessage($url, $message, 'error');
    }

    /**
     * Response when updating auth driver succeed.
     *
     * @return mixed
     */
    public function authUpdated()
    {
        $message = trans('antares/api::response.configuration.update.success');
        $url     = handles('api.configuration.index');

        return $this->redirectWithMessage($url, $message);
    }

}
