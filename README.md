# VictronVRM-PHP
This is an PHP implementation of the Victron VRM API (https://docs.victronenergy.com/vrmapi/overview.html)

The VRM API requires an authentication, so you need an account registered to the VRM (https://vrm.victronenergy.com/).
The Account must have access to the installations you want to access via the API. (To verify this, check what you can see with the Account in the VRM)

# Usage
The Login to the API can be done via

```php
$bearer = vrm_login_get_token($username, $password);
```

The Method returns false, if the authentication fails.

Alternatively you can also issue a personal access token (https://docs.victronenergy.com/vrmapi/overview.html#personal-access-token-endpoints) which is permanent, so you don't have to perform the login.

If you use this Method, ensure you set the isBearerToken parameter, by calls to vrm_request, vrm_readfull or vrm_get_site to false.

The API is very simple to use, so I don't create a documentation.