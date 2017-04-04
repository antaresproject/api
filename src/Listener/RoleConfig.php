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

namespace Antares\Api\Listener;

use Antares\Html\Form\Grid as FormGrid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Antares\Html\Form\FormBuilder;
use Antares\Html\Form\Fieldset;
use Antares\Api\Model\ApiRoles;
use Exception;

class RoleConfig
{

    /**
     * form builder handler
     *
     * @param FormBuilder $form
     */
    public function handle($name, array $params = [])
    {
        $model = $params[0];
        $form  = $params[1];
        $form->extend(function(FormGrid $grid) use($model) {
            $grid->fieldset('api_configuration', function (Fieldset $fieldset) use($model) {
                $fieldset->legend(trans('antares/api::labels.api_configuration'));
                $control = $fieldset->control('checkbox', 'enabled')
                        ->label(trans('antares/api::labels.api_enabled'))
                        ->value(1);
                $apiRole = ApiRoles::where(['role_id' => $model->id])->first();

                if (!is_null($apiRole) and $apiRole->enabled) {
                    $control->attributes(['checked' => 'checked']);
                }
            });
        });
    }

    /**
     * fires on save role 
     * 
     * @param Model $model
     * @throws Exception
     */
    public function onSave($model)
    {
        $apiRoles          = ApiRoles::firstOrNew(['role_id' => $model->id]);
        $apiRoles->enabled = (int) Input::get('enabled');
        try {
            $apiRoles->save();
        } catch (Exception $ex) {
            Log::emergency($ex);
            throw new Exception('Unable to save role.');
        }
    }

}
