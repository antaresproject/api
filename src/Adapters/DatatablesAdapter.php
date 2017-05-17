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

namespace Antares\Modules\Api\Adapters;

use Antares\Datatables\Contracts\DatatablesRawDataContract;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Antares\Modules\Api\Adapter;

class DatatablesAdapter extends Adapter
{

    /**
     * Transform Datatables data and return as array.
     * 
     * @param DatatablesRawDataContract | JsonResponse  $data
     * @return array
     */
    public function transform($data)
    {
        $pagination = config('antares/api::pagination');
        $perPage    = request('per_page', array_get($pagination, 'per_page'));
        $pageName   = request('page_name', array_get($pagination, 'page_name'));
        $page       = request('page', 1);
        $query      = $data->getQuery();
        if ($query instanceof Builder) {
            return $query->paginate($perPage, ['*'], $pageName, $page);
        }
        $defered = $data->getDeferedData();
        if (is_null($query) and is_array($defered)) {
            return new LengthAwarePaginator($defered, count($defered), $perPage, $page);
        }
        if ($data instanceof DatatablesRawDataContract) {
            return $data->getRawData();
        }
        if ($data instanceof JsonResponse) {
            return $data->getData();
        }
    }

}
