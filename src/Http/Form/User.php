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




namespace Antares\Api\Http\Form;

use Antares\Contracts\Html\Form\Fieldset;
use Antares\Html\Form\Grid;

class User extends Grid
{

    /**
     * constructing
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function __construct($model)
    {
        publish('api', ['js/switcher.js']);
        parent::__construct(app());
        $this->name('Api');
        $this->resourced(handles('antares::api/user'), $model, ['class' => 'col-dt-12']);
        $this->fieldset(function (Fieldset $fieldset) use($model) {
            $fieldset->legend(trans('antares/api::labels.api_configuration'));
            $control = $fieldset->control('switch', 'api')
                    ->label('Api')
                    ->wrapper(['class' => 'w200'])
                    ->attributes(['class' => 'switch-api']);

            if ($model->enabled) {
                $control->checked();
                $config = config('api.whitelist_required_areas', config('antares/api::whitelist_required_areas'));

                if (in_array(area(), $config)) {
                    $fieldset->control('input:text', 'whitelist')
                            ->label(trans('antares/api::labels.whitelist'))
                            ->help(trans('antares/api::labels.whitelist_help'))
                            ->attributes(['class' => 'w270']);
                }

                $fieldset->control('button', 'button')
                        ->attributes(['type' => 'submit', 'class' => 'btn btn--md btn--primary mdl-button mdl-js-button'])
                        ->value(trans('antares/logger::labels.save_changes'));
            }
        });


        $this->rules([
            'whitelist' => ['ip'],
        ]);
    }

}
