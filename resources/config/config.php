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
return [
    /**
     * default pagination configuration
     */
    'pagination'               => [
        'per_page'  => 10,
        'page_name' => 'page'
    ],
    'adapters'                 => [
        'datatables' => \Antares\Modules\Api\Adapters\DatatablesAdapter::class,
        'datatable'  => \Antares\Modules\Api\Adapters\DatatablesConfigAdapter::class,
        'form'       => \Antares\Modules\Api\Adapters\FormAdapter::class,
        'widgets'    => \Antares\Modules\Api\Adapters\WidgetAdapter::class,
    ],
    'maps'                     => [
        \Antares\Datatables\Html\Builder::class                  => 'datatables',
        \Antares\Datatables\Factory::class                       => 'datatables',
        \Antares\Datatables\Services\DataTable::class            => 'datatable',
        \Antares\Html\Form\FormBuilder::class                    => 'form',
        \Antares\UI\UIComponents\Adapter\AbstractTemplate::class => 'widgets',
    ],
    'auth'                     => [
        'drivers' => [
            \Antares\Modules\Api\Providers\Basic::class,
            \Antares\Modules\Api\Providers\Jwt::class,
            \Antares\Modules\Api\Providers\PublicPrivate::class,
        ],
    ],
    'whitelist_required_areas' => [
        'administrators'
    ],
    'failed_login_delay'       => 3,
];
