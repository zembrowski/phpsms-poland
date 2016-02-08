# phpsms-poland

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

Send SMS via Polish mobile carriers using Object-Orientated PHP.


## Install

Via Composer

``` bash
$ composer require zembrowski/phpsms-poland
```


## Usage

``` php
$login = 'login';
$password = 'password';
$number = '501234567';
$text = 'It works :)';
try {
  $sms = new zembrowski\SMS\Orange();
  $sms->login($login, $password);
  $sms->send($number, $text);
} catch (Exception $e) {
  echo '[ERROR] ' . $e->getMessage();
}
```


## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Credits

- [Krzysztof Tomasz Zembrowski](https://github.com/zembrowski)
- [All Contributors][link-contributors]


## License

The MIT License (MIT) applies. Please see [License File](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/zembrowski/phpsms-poland.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/zembrowski/phpsms-poland/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/zembrowski/phpsms-poland.svg?style=flat-square
[ico-coveralls]: https://img.shields.io/coveralls/zembrowski/phpsms-poland/phpsms-poland.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/zembrowski/phpsms-poland.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/zembrowski/phpsms-poland.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/zembrowski/phpsms-poland
[link-travis]: https://travis-ci.org/zembrowski/phpsms-poland
[link-coveralls]: https://coveralls.io/r/zembrowski/phpsms-poland
[link-code-quality]: https://scrutinizer-ci.com/g/zembrowski/phpsms-poland
[link-downloads]: https://packagist.org/packages/zembrowski/phpsms-poland
[link-author]: https://github.com/zembrowski
[link-contributors]: ../../contributors
