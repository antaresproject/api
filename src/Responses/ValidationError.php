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

namespace Antares\Modules\Api\Responses;

use Illuminate\Support\ViewErrorBag;
use Illuminate\Http\Response;
use Antares\Modules\Api\Contracts\ResponseContract;

class ValidationError implements ResponseContract
{

    /**
     *
     * @var int 
     */
    protected static $statusCode = 400;

    /**
     *
     * @var array
     */
    protected $content;

    /**
     * 
     * @param ViewErrorBag $errorBag
     */
    public function __construct(ViewErrorBag $errorBag)
    {
        $this->content = [
            'type'   => 'validation error',
            'fields' => $errorBag->messages(),
        ];
    }

    /**
     * 
     * @return Response
     */
    public function response()
    {
        return new Response($this->content, self::$statusCode);
    }

}
