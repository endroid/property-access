Property Access
===============

*By [endroid](http://endroid.nl/)*

[![Latest Stable Version](http://img.shields.io/packagist/v/endroid/property-access.svg)](https://packagist.org/packages/endroid/property-access)
[![Build Status](http://img.shields.io/travis/endroid/PropertyAccess.svg)](http://travis-ci.org/endroid/PropertyAccess)
[![Total Downloads](http://img.shields.io/packagist/dt/endroid/property-access.svg)](https://packagist.org/packages/endroid/property-access)
[![License](http://img.shields.io/packagist/l/endroid/property-access.svg)](https://packagist.org/packages/endroid/property-access)

Extends the Symfony property accessor with the ability to query on objects
having a specific property. An example of such a query would be
"band.member[address.street=Abbey Road].firstName". This functionality makes
it easier to retrieve properties without the need for looping through values.

## Installation

Use [Composer](https://getcomposer.org/) to install the library.

``` bash
$ composer require endroid/property-access
```

## Usage

```php
<?php

use Endroid\PropertyAccess\PropertyAccessor;

$accessor = new PropertyAccessor();

// Returns the first name of the first band member that lives on Abbey Road
$firstName = $accessor->getValue($band, "member[address.street=Abbey Road][0].firstName");
```

## Versioning

Version numbers follow the MAJOR.MINOR.PATCH scheme. Backwards compatible
changes will be kept to a minimum but be aware that these can occur. Lock
your dependencies for production and test your code when upgrading.

## License

This bundle is under the MIT license. For the full copyright and license
information please view the LICENSE file that was distributed with this source code.
