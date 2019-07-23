# NIP/TIN Validator Bundle
 
[![Latest Stable Version](https://poser.pugx.org/kreyu/nip-validator-bundle/version)](https://packagist.org/packages/kreyu/nip-validator-bundle)
[![Build Status](https://travis-ci.org/Kreyu/nip-validator-bundle.svg?branch=master)](https://travis-ci.org/Kreyu/nip-validator-bundle)

Validate the NIP/TIN numbers with configurable validation constraint.

## Requirements

- Symfony 3.4 or higher (4.x supported)
- PHP 5.6 or higher

## Installation

To download the bundle, require it using the Composer:

```
$ composer require kreyu/nip-validator-bundle
```

Then, enable the bundle in the `config/bundles.php` (or in `app/AppKernel.php`)

```php
// config/bundles.php
return [
    // ...
    Kreyu\Bundle\NipValidatorBundle\KreyuNipValidatorBundle::class => ['all' => true],
];
```

## Usage

To use the validation constraint, import the following namespace with the alias or your liking: 

```
Kreyu\Bundle\NipValidatorBundle\Validator\Constraints as KreyuAssert;
``` 

and then use it like any other Symfony validation constraint like so:

```php
<?php

namespace App\Entity;

use Kreyu\Bundle\NipValidatorBundle\Validator\Constraints as KreyuAssert;

class Company
{
    /**
     * @KreyuAssert\Nip()
     */
    private $nip;
}
```

Configuration reference:

```php
/**
 * @KreyuAssert\Nip({
 *     pattern = null,
 *     patternMessage = "This is not a valid NIP number.",
 *     checksum = true,
 *     checksumMessage = "This is not a valid NIP number.",
 *     allowDashes = false,
 *     requireDashes = false,
 *     allowPrefix = true,
 *     requirePrefix = false,
 *     prefixLength = 2
 * })
 */
```

- `pattern` - used to completely override the regex pattern used in the validation. Example value: `/^(\d{2}.\d{3}.\d{3}.\d{2})$/`
- `patternMessage` - message included in the violation if the NIP pattern is invalid.
- `checksum` - determines if the checksum should be validated.
- `checksumMessage` - message included in the violation if the NIP pattern is invalid.
- `allowDashes` - determines if dashes are accepted, but not required, e.g. `000-000-00-00`
- `requireDashes` - determines if dashes are required, ignores the `allowDashes` option.
- `allowPrefix` - determines if prefix is accepted, but not required, e.g. `PL 0000000000`
- `requirePrefix` - determines if prefix is required, ignores the `allowPrefix` option.
- `prefixLength` - determines a valid length of the prefix.

## License

The MIT License (MIT). Please see [license file](LICENSE.md) for more information.
