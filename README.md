# VictronVRM-PHP

This is a PHP implementation of the [Victron VRM API](https://vrm-api-docs.victronenergy.com/#/).

## Prerequisites

To use this API, you need a registered account on [Victron VRM](https://vrm.victronenergy.com/). The account must have access to the installations you want to interact with. You can verify your access by checking what installations are visible within your VRM account.

## Authentication

The VRM API requires authentication. You can authenticate in two ways:

### 1. Login with Username & Password

You can obtain a bearer token using:

```php
$bearer = vrm_login_get_token($username, $password);
```

If authentication fails, this method returns `false`.

### 2. Using a Personal Access Token

Alternatively, you can use a [personal access token](https://vrm-api-docs.victronenergy.com/#/operations/users/idUser/accesstokens/create), which is permanent and eliminates the need to log in.

If using this method, ensure you set the `isBearerToken` parameter to `false` when making requests via `vrm_request`, `vrm_readfull`, or `vrm_get_site`.

## Usage

The API is simple to use, so no additional documentation is provided. Refer to the official [Victron VRM API documentation](https://vrm-api-docs.victronenergy.com/#/) for details on available endpoints and functionality.

## Functions Overview

### Caching

- `vrm_cache($idUser, $maxAge=30)`: Retrieves cached data for a user if it is not older than `maxAge` minutes.
- `vrm_set_cache($idUser, $data)`: Stores data in the cache with a timestamp.

### Authentication

- `vrm_login_get_token($username, $password)`: Authenticates a user and returns a bearer token.

### API Requests

- `vrm_request($method, $token, $payload, $isBearerToken=true)`: Makes API requests to VRM.
- `vrm_readfull($idUser, $token, $isBearerToken=true)`: Retrieves all installations associated with a user, with caching.
- `vrm_system_overview($idSite, $token, $isBearerToken=true)`: Retrieves system overview data for a site.
- `vrm_get_site($idSite, $idUser, $token, $isBearerToken=true, $fullData=null)`: Retrieves detailed site data.

### Site Data Extraction

- `vrm_get_site_alarms($site)`: Retrieves current alarms for a site.
- `vrm_get_site_timestamp($site)`: Retrieves the last timestamp for a site.
- `vrm_get_site_location($site)`: Retrieves the latitude and longitude of a site.
- `vrm_get_site_voltage($site)`: Retrieves battery voltage.
- `vrm_get_site_batterystate($site)`: Retrieves battery state.
- `vrm_get_site_altitude($site)`: Retrieves site altitude.
- `vrm_get_site_consumption($site)`: Retrieves site power consumption.
- `vrm_get_site_solaryield($site)`: Retrieves solar yield data.
- `vrm_get_site_batterysoc($site)`: Retrieves battery state of charge.
- `vrm_get_site_current($site)`: Retrieves battery current.
- `vrm_get_site_acinput($site)`: Retrieves AC input status.
- `vrm_get_site_systemstate($site)`: Retrieves overall system state.
- `vrm_get_site_gridpower($site)`: Retrieves grid power data.
- `vrm_get_site_switchposition($site)`: Retrieves switch position.
- `vrm_get_site_lowstateofcharge($site)`: Retrieves low state of charge warning.
- `vrm_get_site_gridalarm($site)`: Retrieves grid alarm status.

### Helper Functions

- `vrmhelper_parse_sites($data)`: Parses API response data into an associative array of site information.

## License

Include any license information here if applicable.

---

For any issues or contributions, feel free to open a pull request or issue in this repository.
