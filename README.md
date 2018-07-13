# Installation for lumen

## install
```
composer require kayrules/authkeepr
```

## define middleware in bootstrap/app.php
```
$app->routeMiddleware([
    'authkeepr' => Kayrules\AuthKeepr\Http\Middleware\AuthKeepr::class,
]);
```

## using the middleware in routes/web.php
```
$api->version('v1', [
	'middleware' => 'authkeepr',
], function ($api)
{
	//
});
```

## declare .env variable to token provider url
```
AUTHKEEPR_SSO_URL=http://domain.com/auth/token
```

## if the SSO provider apply strict header match, enter this in .env
```
AUTHKEEPR_SSO_ACCEPT_HEADER=application/vnd.strict.v1+json
```