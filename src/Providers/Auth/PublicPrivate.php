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




namespace Antares\Api\Providers\Auth;

use Tymon\JWTAuth\Contracts\Providers\Auth;
use Dingo\Api\Auth\Provider\Authorization;
use Antares\Api\Model\ApiPublicPrivate;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Route;
use Antares\Api\Autoban;
use Antares\Model\User;
use Exception;

class PublicPrivate extends Authorization
{

    /**
     * Auth instance
     *
     * @var Auth 
     */
    protected $auth;

    /**
     * Construct
     * 
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(Request $request, Route $route)
    {
        $this->validateAuthorizationHeader($request);
        $publicHash  = $request->header('X-Public');
        $contentHash = $request->header('X-Hash');

        $privateHash = $this->privateKey();
        $input       = $request->input();
        $hash        = hash_hmac('sha256', json_encode($input), $privateHash);

        if (!empty($input) && $hash !== $contentHash) {
            throw new Exception('Public / private keys does not match.', 500);
        }
        try {
            $user = ApiPublicPrivate::query()->where(['public_key' => $publicHash])->firstOrFail();
        } catch (Exception $ex) {
            throw new Exception('Invalid public key', 401);
        }
        $autoban = app(Autoban::class);
        $autoban->setUser($user->user)->checkEnabledInArea('public')->checkWhiteList();
        try {
            $this->auth->byId($user->user->id);
        } catch (Exception $ex) {
            if (!$this->isValid()) {
                $autoban->delay();
            }
            throw $ex;
        }
    }

    /**
     * Verify request is an instance of Dingo
     * 
     * @param \Illuminate\Http\Response $response
     * @return boolean
     */
    protected function isValid()
    {
        return !app('request') instanceof Request;
    }

    /**
     * User private key getter
     * 
     * @param User $user
     * @param boolean $reset
     * @return String
     */
    public function publicKey(User $user, $reset = false)
    {
        $model = ApiPublicPrivate::query()->firstOrNew(['user_id' => $user->id]);
        if (!$model->exists or $reset) {
            $model->public_key = hash('sha256', openssl_random_pseudo_bytes(32));
            $model->save();
        }
        return $model->public_key;
    }

    /**
     * Generates private key
     * 
     * @return String
     */
    public function privateKey()
    {
        return hash('sha256', env('APP_KEY'));
    }

    /**
     * Gets authorization method
     * 
     * @return String
     */
    public function getAuthorizationMethod()
    {
        return 'hmac';
    }

}
