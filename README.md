# PHP JOSE Library

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Spomky-Labs/jose-service/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Spomky-Labs/jose-service/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Spomky-Labs/jose-service/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Spomky-Labs/jose-service/?branch=master)
[![Build Status](https://travis-ci.org/Spomky-Labs/jose.svg?branch=master)](https://travis-ci.org/Spomky-Labs/jose)
[![HHVM Status](http://hhvm.h4cc.de/badge/Spomky-Labs/jose-service.png)](http://hhvm.h4cc.de/package/Spomky-Labs/jose-service)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/33c9c0b7-cc73-475e-8e83-e9526c539369/big.png)](https://insight.sensiolabs.com/projects/33c9c0b7-cc73-475e-8e83-e9526c539369)

[![Latest Stable Version](https://poser.pugx.org/Spomky-Labs/jose-service/v/stable.png)](https://packagist.org/packages/Spomky-Labs/jose-service) [![Total Downloads](https://poser.pugx.org/Spomky-Labs/jose-service/downloads.png)](https://packagist.org/packages/Spomky-Labs/jose-service) [![Latest Unstable Version](https://poser.pugx.org/Spomky-Labs/jose-service/v/unstable.png)](https://packagist.org/packages/Spomky-Labs/jose-service) [![License](https://poser.pugx.org/Spomky-Labs/jose-service/license.png)](https://packagist.org/packages/Spomky-Labs/jose-service)

## The Release Process ##

We manage the releases of the library through features and time-based models.

- A new patch version comes out every month when you made backwards-compatible bug fixes.
- A new minor version comes every six months when we added functionality in a backwards-compatible manner.
- A new major version comes every year when we make incompatible API changes.

The meaning of "patch" "minor" and "major" comes from the Semantic [Versioning strategy](http://semver.org/).

This release process applies for all versions.

### Backwards Compatibility

We allow developers to upgrade with confidence from one minor version to the next one.

Whenever keeping backward compatibility is not possible, the feature, the enhancement or the bug fix will be scheduled for the next major version.

## Prerequisites ##

This library needs at least

* `PHP 5.4`.

It has been successfully tested using `PHP 5.4` to `PHP 5.6`.
Tests with `HHVM` fail because of `phpseclib/phpseclib` which is not compatible.

## Installation ##

The preferred way to install this library is to rely on Composer:

    composer require spomky-labs/jose-service

## How to use ##

Your classes are ready to use? Have a look at [How to use](doc/Use.md) to create or load your first JWT objects.

## Contributing

Requests for new features, bug fixed and all other ideas to make this library usefull are welcome. [Please follow these best practices](doc/Contributing.md).

## Licence

This software is release under [MIT licence](LICENSE).
