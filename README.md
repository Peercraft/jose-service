# PHP JOSE Service

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Spomky-Labs/jose-service/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Spomky-Labs/jose-service/?branch=master)
[![Coverage Status](https://coveralls.io/repos/Spomky-Labs/jose-service/badge.svg?branch=master&service=github)](https://coveralls.io/github/Spomky-Labs/jose-service?branch=master)

[![Build Status](https://travis-ci.org/Spomky-Labs/jose.svg?branch=master)](https://travis-ci.org/Spomky-Labs/jose)
[![HHVM Status](http://hhvm.h4cc.de/badge/Spomky-Labs/jose-service.png)](http://hhvm.h4cc.de/package/Spomky-Labs/jose-service)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/33c9c0b7-cc73-475e-8e83-e9526c539369/big.png)](https://insight.sensiolabs.com/projects/33c9c0b7-cc73-475e-8e83-e9526c539369)

[![Latest Stable Version](https://poser.pugx.org/Spomky-Labs/jose-service/v/stable.png)](https://packagist.org/packages/Spomky-Labs/jose-service) [![Total Downloads](https://poser.pugx.org/Spomky-Labs/jose-service/downloads.png)](https://packagist.org/packages/Spomky-Labs/jose-service) [![Latest Unstable Version](https://poser.pugx.org/Spomky-Labs/jose-service/v/unstable.png)](https://packagist.org/packages/Spomky-Labs/jose-service) [![License](https://poser.pugx.org/Spomky-Labs/jose-service/license.png)](https://packagist.org/packages/Spomky-Labs/jose-service)

[![StyleCI](https://styleci.io/repos/30558405/shield)](https://styleci.io/repos/30558405)

This project uses [Spomky-Labs/jose](https://github.com/Spomky-Labs/jose) to ease encryption/decryption and signature/verification of JWS and JWE.

## The Release Process
The release process [is described here](doc/Release.md).

## Prerequisites

This library needs at least:
* ![PHP 5.6+](https://img.shields.io/badge/PHP-5.6%2B-ff69b4.svg).

Please consider the following optional requirements:
* AES-GCM based algorithms (AxxxGCM and AxxxGCMKW): [PHP Crypto](https://github.com/bukka/php-crypto) Extension (not yet available on `PHP 7` and `HHVM`).

# Continuous Integration

It has been successfully tested using `PHP 5.6` with all algorithms.

Some tests on `PHP 7` and `HHVM` were skipped because [PHP Crypto](https://github.com/bukka/php-crypto) is not yet supported.
At the moment, you will not be able to use GCM algorithms on these platforms.

We also track bugs and code quality using [Scrutinizer-CI](https://scrutinizer-ci.com/g/Spomky-Labs/jose-service) and [Sensio Insight](https://insight.sensiolabs.com/projects/33c9c0b7-cc73-475e-8e83-e9526c539369).

Coding Standards are verified by [StyleCI](https://styleci.io/repos/30558405).

Code coverage is analyzed by [Coveralls.io](https://coveralls.io/github/Spomky-Labs/jose-service). 

## Installation

The preferred way to install this library is to rely on Composer:

```sh
composer require spomky-labs/jose-service "dev-master"
```

## How to use

Your classes are ready to use? Have a look at [How to use](doc/Use.md) to create or load your first JWT objects.

## Contributing

Requests for new features, bug fixed and all other ideas to make this library useful are welcome. [Please follow these best practices](doc/Contributing.md).

## Licence

This software is release under [MIT licence](LICENSE).
