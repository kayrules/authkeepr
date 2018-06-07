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

## declare env variable to token provider url
```
AUTHKEEPER_SSO_URL=http://domain.com/auth/token
```