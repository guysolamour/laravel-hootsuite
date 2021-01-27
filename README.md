# Hootsuite

[![Packagist](https://img.shields.io/packagist/v/guysolamour/laravel-hootsuite.svg)](https://packagist.org/packages/guysolamour/laravel-hootsuite)
[![Packagist](https://poser.pugx.org/guysolamour/laravel-hootsuite/d/total.svg)](https://packagist.org/packages/guysolamour/laravel-hootsuite)
[![Packagist](https://img.shields.io/packagist/l/guysolamour/laravel-hootsuite.svg)](https://packagist.org/packages/guysolamour/laravel-hootsuite)


## Installation

Install via composer

```bash
composer require guysolamour/laravel-hootsuite
```

Publish package assets

```bash
php artisan vendor:publish --provider="Guysolamour\Hootsuite\ServiceProvider"
```

Publish spatie [laravel settings](https://github.com/spatie/laravel-settings) package assets


In case you are already using this package in your project, you can skip this step

```bash
php artisan vendor:publish --provider="Spatie\LaravelSettings\LaravelSettingsServiceProvider" --tag="settings"
```

Add the configuration to the *config/settings* file generated previously

```php
'settings' => [
  ...
  Guysolamour\Hootsuite\Settings\HootsuiteSettings\HootsuiteSettings::class,
]
```

If you wanted to shorten the urls used in your posts. You should get an api key from [bitly](https://dev.bitly.com) and add it to your *.env* file

```php
BITLY_ACCESS_TOKEN=
```

Then pass this option (s) to true

```php
'bitly_text_link' => true, // for the publication main link
'bity_all_links'  => true, // for all links in publication text
```



## Usage

This package allows you to interact with the hootsuite API from its Laravel application. To do this, you must have a hootsuite account and allow the package to access this account.

click  [here to grant permissions](https://platform.hootsuite.com/oauth2/auth/?client_id=dee91d5b-7c0a-454e-9149-fc40f91bbb40&response_type=code&scope=offline&redirect_uri=https%3A%2F%2Faswebagency.com%2Fapi%2Flaravel-hootsuite&state=http%3A%2F%2Flocalhost%3A8000%2Flaravel-hootsuite)

You can also get the link with this artisan command

```php
php artisan hootsuite:oauth:url
```

Make a publication

```php
use Guysolamour\Hootsuite\Facades\Hootsuite;

Hootsuite::publish([
  'text'        => "This is a text", // required
  'hashtags'    => "#this #is #a #test",
  'networks'    => "Facebook, Twitter, Linkedin", // required
  'notify'      => false, // send message when post is published
  'image'       => 'https://domain.com/imagelink.jpg',
  'link'        => 'https://link.com',
]);
```

Schedule a publication

```php

use Guysolamour\Hootsuite\Facades\Hootsuite;

Hootsuite::schedule([
  'text'        => "This is a text", // required
  'hashtags'    => "#this #is #a #test",
  'networks'    => "Facebook, Twitter, Linkedin",
  'notify'      => false, // send message when post is published
  'image'       => 'https://domain.com/imagelink.jpg', // image url pour presenter la publication
  'link'        => 'https://link.com', /
  'schedule_at' => '2021-01-15 08:59:12' // or carbon instance | required when schedule
]);
```

Delete a scheduled post that is not yet published

```php
use Guysolamour\Hootsuite\Facades\Hootsuite;


Hotsuite::destroy(int $messageId) :bool;
```

Retrieve information about your account

```php
use Guysolamour\Hootsuite\Facades\Hootsuite;

Hootsuite::user();
```

Check if the post is pending

```php
use Guysolamour\Hootsuite\Facades\Hootsuite;

Hootsuite::messageIsStillScheduled(int $messageId);
```

Get scheduled post

```php
use Guysolamour\Hootsuite\Facades\Hootsuite;

Hootsuite::getMessage(int $messageId);
```

You can type the API directly with these different methods

```php
use Guysolamour\Hootsuite\Facades\Hootsuite;

// Get
Hootsuite::get(string $url);
// Post
Hootsuite::post(string $url);
// Put
Hootsuite::put(string $url);
// Delete
Hootsuite::delete(string $url);
```


## Security

If you discover any security related issues, please email rolandassale@gmail.com
instead of using the issue tracker.

## Credits

- [Guy-roland ASSALE](https://github.com/guysolamour)
- [All contributors](https://github.com/guysolamour/hootsuite/graphs/contributors)

This package is bootstrapped with the help of
[melihovv/laravel-package-generator](https://github.com/melihovv/laravel-package-generator).
