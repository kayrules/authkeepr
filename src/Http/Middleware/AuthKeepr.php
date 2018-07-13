<?php

namespace Kayrules\authkeepr\Http\Middleware;

use Closure;
use GuzzleHttp\Client;

class AuthKeepr
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if(!env('AUTHKEEPR_SSO_URL')) {
            return $this->_missingEnvResponse();
        }

        $authHeader = $request->header('Authorization');
        
        $client = new Client();
		$vars = [
			'http_errors' => false,
			'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => env('AUTHKEEPR_SSO_ACCEPT_HEADER'),
                'Authorization' => $authHeader
			],
		];

        $c = $client->request('GET', env('AUTHKEEPR_SSO_URL'), $vars);
		if($c->getStatusCode() != 200) {
            return $this->_oauthExceptionResponse();
		}
		else {
            $response = (object) json_decode($c->getBody(), true);
            if($response->id > 0) $request->merge(['sso_id' => $response->id]);
            else {
                return $this->_oauthExceptionResponse();
            }
		}

        return $next($request);
    }

    private function _oauthExceptionResponse()
    {
        $msg = [
            'error' => [
                'code' => 401,
                'type' => 'OAuthException',
                'message' => 'An active access token must be used to query information about the current user.'
            ]
        ];
        return response($msg, 401)->header('Content-Type', 'application/json');
    }

    private function _missingEnvResponse()
    {
        $msg = [
            'error' => [
                'code' => 500,
                'type' => 'MissingEnvironment',
                'message' => 'AUTHKEEPR_SSO_URL is missing from your environment.',
            ]
        ];
        return response($msg, 500)->header('Content-Type', 'application/json');
    }
}
