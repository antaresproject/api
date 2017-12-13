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
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Modules\Api\Adapters;

use Antares\Datatables\Contracts\DatatablesRawDataContract;
use Antares\Modules\Api\Adapter;

class DatatablesConfigAdapter extends Adapter
{

    /**
     * Transform Datatables data and return as array.
     * 
     * @param DatatablesRawDataContract | JsonResponse  $data
     * @return array
     */
    public function transform($data)
    {
        $table = $data->html();
        return $table->config();
    }

}
