# Hootsuite

[![GitHub Workflow Status](https://github.com/guysolamour/hootsuite/workflows/Run%20tests/badge.svg)](https://github.com/guysolamour/hootsuite/actions)
[![styleci](https://styleci.io/repos/CHANGEME/shield)](https://styleci.io/repos/CHANGEME)

[![Packagist](https://img.shields.io/packagist/v/guysolamour/hootsuite.svg)](https://packagist.org/packages/guysolamour/hootsuite)
[![Packagist](https://poser.pugx.org/guysolamour/hootsuite/d/total.svg)](https://packagist.org/packages/guysolamour/hootsuite)
[![Packagist](https://img.shields.io/packagist/l/guysolamour/hootsuite.svg)](https://packagist.org/packages/guysolamour/hootsuite)

Package description: CHANGE ME

## Installation

Install via composer
```bash
composer require guysolamour/laravel-hootsuite
```

### Publish package assets

```bash
php artisan vendor:publish --provider="Guysolamour\Hootsuite\ServiceProvider"
```

## Usage

Ce package a été créé pour interagir de manière tres simple avec l'API de hootsuite.
Vous devez obtentir vos cles api sur le site developer de hootsuite.com

cliquez ici pour
https://platform.hootsuite.com/oauth2/auth/?client_id=l7xx225f065c3e6e4da2b9e287824299f6de&response_type=code&scope=offline&redirect_uri=https%3A%2F%2Fwww.wpzinc.com%2F%3Foauth%3Dhootsuite&state=http%3A%2F%2Flocalhost%3A8000%2Flaravel-hootsuite

Enregister les clés d'api dans le fichier .env

HOOTSUITE_CLIENT_ID=dee91d5b-7c0a-454e-9149-fc40f91bbb40
HOOTSUITE_CLIENT_SECRET=mieA_qn.a8j_


Si vous utilisez le raccourcissuer d'url vous pouvez obtenit aussi une clé api
chez bitly et l'enregister dans votre fichier .env

BITLY_ACCESS_TOKEN=e358d77ad20b29706378cd447822cd1e9de4c099

Pour le code, utiliser la command
php artisan hootsuite:oauth:url
HOOTSUITE_CLIENT_CODE=Y9sSrF6nxpDzbxhg9Gn0cPzXztGGJslABj6243WNKkA.OmSqFtADkuX_HEkvRue-n-MS4zr0uuwLrFZ45vE6yKA

ne pas oublier de mettre a jour la variable APP_URL dans le fichier .env et si vous etes
en local ajouter le port

Faire une publication
Hootsuite::message([
  'text'     => "This is a text", // requred
  'hashtags' => "#this #is #a #test",
  'networks' => "Facebook, Twitter, Linkedin", // required
  'notify'   => false, // send message when post is scheduled
  'image'    => 'https://domain.com/imagelink.jpg',
  'link'     => 'https://link.com', // add a link to publication
]);

Hootsuite::publish();

Programmer une publiction
Hootsuite::schedule() // le schedule at est obligatorie

Supprimer une publication
Hotsuite::delete();



## Security

If you discover any security related issues, please email rolandassale@gmail.com
instead of using the issue tracker.

## Credits

- [Guy-roland ASSALE](https://github.com/guysolamour/hootsuite)
- [All contributors](https://github.com/guysolamour/hootsuite/graphs/contributors)

This package is bootstrapped with the help of
[melihovv/laravel-package-generator](https://github.com/melihovv/laravel-package-generator).
