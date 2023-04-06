### BdrenOauth Client Library for Laravel

This package provides a Laravel 7+ service provider for the [BdrenOauth](https://accounts.bdren.net.bd) OAuth2 service.

## Installation

You can install the package via composer:

```bash
composer require shuvoo/bdren-oauth-client-laravel
```

You must publish the config file with:

```bash
php artisan vendor:publish --tag="oauth-config"
```

Then add environment variables to your `.env` file:

```dotenv
OAUTH_CLIENT_ID=[your_client_id]
OAUTH_CLIENT_SECRET=[your_client_secret]
OAUTH_BASE_URL=[your_oauth_base_url]
OAUTH_USER_MODEL=[optional|user_model]
OAUTH_SUCCESS_URL=[optional|success_url]
OAUTH_ERROR_URL=[optional|failure_url]
```

Need to run migrations for the `access_tokens` table:

```bash
php artisan migrate
```

