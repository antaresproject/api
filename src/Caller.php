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




/**
 * Internal API Caller
 *
 * @author Dominik Gacek <dominik@easydcim.com>
 * @modified Lukasz Cirut  <lukasz.cirut@inbs.software>
 */

namespace Antares\Api;

use Guzzle\Http\Client;

class Caller
{

    /**
     * api key
     *
     * @var String
     */
    protected $apikey;

    /**
     * Instance API.
     *
     * @param String $key
     */
    public function __construct($key)
    {
        $this->apikey = $key;
    }

    /**
     * Call internal URI with parameters.
     *
     * @param  string $uri
     * @param  string $method
     * @param  array  $headers
     * @return mixed
     */
    public function invoke($uri, $method, array $headers = [])
    {
        $client   = new Client();
        $res      = $client->createRequest(strtoupper($method), url('/') . '/' . $uri, array_merge($headers, $this->getHeaders()));
        $response = $res->send();
        $types    = $response->getHeader('Content-Type')->toArray();
        $body     = $response->getBody(true);
        foreach ($types as $type) {
            if ($type == 'application/json' && function_exists('json_decode') and is_string($body)) {
                return json_decode($body);
            }
        }

        return $body;
    }

    /**
     * gets default headers
     * 
     * @return array
     */
    protected function getHeaders()
    {
        return [
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/vnd.antares.v1+json',
            'Authorization' => 'Bearer ' . $this->apikey
        ];
    }

    /**
     * Alias call method.
     *
     * @param string $method
     * @param array $headers
     * @return mixed
     */
    public function __call($method, array $headers = [])
    {
        if (in_array($method, array('get', 'post', 'put', 'delete'))) {

            // Get URI
            $uri     = array_shift($headers);
            // Get parameters
            $headers = current($headers);
            $headers = is_array($headers) ? $headers : [];
            return $this->invoke($uri, $method, $headers);
        }
    }

}
