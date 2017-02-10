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
use Antares\Api\Processor\UserProcessor;

class UserController extends AdminController
{

    /**
     * Processor instance
     *
     * @var UserProcessor 
     */
    protected $processor;

    /**
     * Construct
     * 
     * @param UserProcessor $processor
     */
    public function __construct(UserProcessor $processor)
    {
        $this->processor = $processor;
        parent::__construct();
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

    /**
     * Shows api user configuration
     * 
     * @param mixed $id
     * @return \Illuminate\View\View
     */
    public function index($id = null)
    {
        return $this->processor->index($id);
    }

    /**
     * Updates user api configuration
     * 
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id)
    {
        return $this->processor->update($id);
    }

    /**
     * Reset user token
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset()
    {
        return $this->processor->reset();
    }

}
