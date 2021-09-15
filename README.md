# breezer

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![GitHub issues](https://img.shields.io/github/issues/bitfumes/laravel-breezer)](https://github.com/bitfumes/laravel-breezer/issues)
[![Total Downloads](https://img.shields.io/packagist/dt/bitfumes/breezer.svg?style=flat-square)](https://packagist.org/packages/bitfumes/breezer)
[![Build Status](https://travis-ci.org/bitfumes/laravel-breezer.svg?branch=master)](https://travis-ci.org/bitfumes/laravel-breezer)

# Install

`composer require bitfumes/laravel-breezer`

# Steps to follow

<!-- add mustVerifyEmail interface on user model if want to verify -->
<!-- user will not be able to login if email is not verified -->

## Steps 1

1. Add `Breezer` trait to your user model.

2. Add Contract `MustVerifyEmail` to your authenticatable model if you want to enable email verification.

```php

use Bitfumes\Breezer\Traits\Breezer;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, Breezer;
    ...
}
```

## Step 2

Now publish two new migrations

1.  To add avatar field to your use model.
2.  To add social login profile.

```bash
php artisan vendor:publish --tag=breezer:migrations
```

## Step 3

After getting migrations in your laravel application, its time to have these tables in your database.

```bash
php artisan migrate
```

## Step 4

Set your frontend URL on your .env file
FRONT_URL

Set frontend Verify Email URL  on your .env file
BREEZER_VERIFY_URL

Set frontend password reset URL  on your .env file
BREEZER_RESET_URL

## Step 5

Because every user need to verify its email and to send email we are going to use laravel queue.

Now add queue driver on your `.env` file

That's it, now enjoy api auth with JWT

```
QUEUE_DRIVER=database
```

## Testing

Run the tests with:

```bash
vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email sarthak@bitfumes.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](/LICENSE.md) for more information.
