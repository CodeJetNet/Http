# Http

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This is a Work In Progress.

This is an exercise in understanding the http-interop related standards and is an implementation of [PSR-7 HTTP message interfaces](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-7-http-message.md) and [HTTP-Factory Interfaces](https://github.com/php-fig/fig-standards/blob/master/proposed/http-factory/http-factory.md)

## Install

Via Composer

``` bash
$ composer require codejet/http
```

## Usage

### URI
``` php
$uri = new CodeJet\Uri('https://www.example.com');
echo $uri;
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email josh@findsomehelp.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/codejet/http.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/CodeJetNet/Http/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/CodeJetNet/Http.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/CodeJetNet/Http.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/codejet/http.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/codejet/http
[link-travis]: https://travis-ci.org/CodeJetNet/Http
[link-scrutinizer]: https://scrutinizer-ci.com/g/CodeJetNet/Http/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/CodeJetNet/Http
[link-downloads]: https://packagist.org/packages/codejet/http
