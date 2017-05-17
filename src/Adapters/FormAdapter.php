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

use Antares\Modules\Api\Adapter;
use Antares\Html\Form\FormBuilder;
use Antares\Html\Form\Fieldset;

class FormAdapter extends Adapter
{

    /**
     *
     * @var array
     */
    protected $fieldsets = [];

    /**
     * Field types which should not be included into a result array.
     * 
     * @var array
     */
    protected static $protectedTypes = [
        'button', 'input:hidden'
    ];

    /**
     * Field attributes which should not be included into a result array.
     * 
     * @var array
     */
    protected static $unusedFieldAttrs = [
        'id', 'attributes', 'field', 'wrapper'
    ];

    /**
     * Transform FormBuilder data and return as array.
     * 
     * @param FormBuilder $data
     * @return array
     */
    public function transform($data)
    {
        if ($data instanceof FormBuilder) {
            $response = $data->getRawResponse();

            foreach ($response['fieldsets'] as $fieldset) {
                $this->transformFieldset($fieldset);
            }

            return [
                'form' => [
                    'name'      => array_get($response, 'name'),
                    'rules'     => array_get($response, 'rules', []),
                    'fieldsets' => $this->fieldsets,
                ],
            ];
        }

        return parent::transform($data);
    }

    /**
     * Transform a Fieldset object and compute a results array.
     * 
     * @param Fieldset $fieldset
     */
    protected function transformFieldset(Fieldset $fieldset)
    {
        $controls = [];

        foreach ($fieldset->controls() as $control) {
            $type = $control->get('type');

            if (!in_array($type, self::$protectedTypes)) {
                $controls[] = $this->transformControlField($control);
            }
        }

        $this->fieldsets[] = [
            'name'     => $fieldset->getName(),
            'controls' => $controls,
        ];
    }

    /**
     * Returns an array of a field without unused field attributes.
     * 
     * @param \Antares\Html\Form\Field $control
     * @return array
     */
    protected function transformControlField($control)
    {
        $attributes = $control->getAttributes();

        foreach (self::$unusedFieldAttrs as $attr) {
            if (isset($attributes[$attr])) {
                unset($attributes[$attr]);
            }
        }

        return $attributes;
    }

}
