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




namespace Antares\Api\Http\Datatable;

use Antares\Logger\Http\Datatables\ActivityLogs;
use Antares\Logger\Model\Logs as LogsModel;
use Illuminate\Support\Facades\DB;

class Logs extends ActivityLogs
{

    /**
     * Ajax url
     * 
     * @var String
     */
    protected $ajax = 'antares::api/logs/index';

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        $query = LogsModel::withoutGlobalScopes()
                ->select([
                    'tbl_brands.name as brand_name',
                    'tbl_log_types.name as component_name',
                    'tbl_log_priorities.name as priority_name',
                    'tbl_logs.*',
                ])
                ->leftJoin('tbl_brands', 'tbl_logs.brand_id', '=', 'tbl_brands.id')
                ->leftJoin('tbl_log_priorities', 'tbl_logs.priority_id', '=', 'tbl_log_priorities.id')
                ->leftJoin('tbl_log_types', 'tbl_logs.type_id', '=', 'tbl_log_types.id')
                ->leftJoin('tbl_logs_translations', function($join) {
                    $join
                    ->on('tbl_logs_translations.log_id', '=', 'tbl_logs.id')
                    ->on('tbl_logs_translations.lang_id', '=', DB::raw(lang_id()));
                })
                ->where('tbl_logs.brand_id', brand_id())
                ->where('tbl_logs.is_api_request', 1);
        if (!request()->ajax()) {
            $query->orderBy('tbl_logs.created_at', 'desc');
        }
        listen('datatables.order.operation', function($query, $direction) {
            $query->orderBy('tbl_logs.name', $direction);
        });
        listen('datatables.order.operation', function($query, $direction) {
            $query->orderBy('tbl_logs.type_id', $direction);
        });
        return $query;
    }

}
