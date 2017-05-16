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

namespace Antares\Modules\Api\Responses;

use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Http\Response;
use Antares\Modules\Api\Contracts\ResponseContract;

class Message implements ResponseContract
{

    /**
     *
     * @var int
     */
    protected $statusCode;

    /**
     *
     * @var array
     */
    protected $content;

    /**
     * 
     * @param MessageBag $messageBag
     * @param int $statusCode (default 200)
     */
    public function __construct(MessageBag $messageBag, $statusCode = 200)
    {
        $this->statusCode = $statusCode;

        $this->content = [
            'type'     => 'message',
            'statuses' => $messageBag->keys(),
            'messages' => $messageBag->messages(),
        ];
    }

    /**
     * 
     * @return Response
     */
    public function response()
    {
        return new Response($this->content, $this->statusCode);
    }

}
