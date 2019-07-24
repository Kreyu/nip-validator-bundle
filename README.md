# NIP/TIN Validator Bundle
 
[![Latest Stable Version](https://poser.pugx.org/kreyu/nip-validator-bundle/version)](https://packagist.org/packages/kreyu/nip-validator-bundle)
[![Build Status](https://travis-ci.org/Kreyu/nip-validator-bundle.svg?branch=master)](https://travis-ci.org/Kreyu/nip-validator-bundle)

Validate the tax identification numbers with configurable validation constraint.

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

### Pattern validation

By default, the only accepted format is a string of characters without the prefix nor dashes.

#### Usage of dashes
  
If you wish to allow or require the usage of the dashes, use the `allowDashes` and `requireDashes` options:

```php
<?php

namespace App\Entity;

use Kreyu\Bundle\NipValidatorBundle\Validator\Constraints as KreyuAssert;

class Company
{
    /**
     * @KreyuAssert\Nip(
     *     allowDashes=true,
     *     requireDashes=true
     * )
     */
    private $nip;
}
```

Both options are set to `false` by default.  
Setting the `requireDashes` option to `true` ignores the value of the `allowDashes`, as the number without dashes is no longer valid.  
The allowed formats with dashes are following:
- `XX-XXX-XXX-XX`, e.g. `34-208-769-99`
- `XXX-XXX-XX-XX`, e.g. `342-087-69-99`

#### Usage of prefix

If you wish to allow or require the usage of the prefix, use the `allowPrefix` and `requirePrefix` options.  
Additionally, it is possible to modify the length of the prefix, using the `prefixLength` option. 

```php
<?php

namespace App\Entity;

use Kreyu\Bundle\NipValidatorBundle\Validator\Constraints as KreyuAssert;

class Company
{
    /**
     * @KreyuAssert\Nip(
     *     allowPrefix=true,
     *     requirePrefix=true,
     *     prefixLength=2     
     * )
     */
    private $nip;
}
```

Both `allowPrefix` and `requirePrefix` options are set to `false` by default, and `prefixLength` defaults to `2`.  
Prefixes are not followed with the space:
- `PPXXXXXXXXXX`, e.g. `PL3420876999`

#### Customization of the pattern violation message

It is possible to customize the pattern violation message using the `patternMessage` option:

```php
<?php

namespace App\Entity;

use Kreyu\Bundle\NipValidatorBundle\Validator\Constraints as KreyuAssert;

class Company
{
    /**
     * @KreyuAssert\Nip(
     *     patternMessage="This is not a valid NIP number."
     * )
     */
    private $nip;
}
```

#### Usage of a custom regular expression

If the default functionality does not meet your needs, consider using the `pattern` option:

```php
<?php

namespace App\Entity;

use Kreyu\Bundle\NipValidatorBundle\Validator\Constraints as KreyuAssert;

class Company
{
    /**
     * @KreyuAssert\Nip(
     *     pattern="/^(\d{2}.\d{3}.\d{3}.\d{2})$/"
     * )
     */
    private $nip;
}
```

By default this option is equals `null`, and setting it to any other value ignores the `allowDashes`, `requireDashes`, `allowPrefix` and `requirePrefix` options.

### Checksum validation 

By default, the checksum is being validated. If you wish to disable this feature, set the `checksum` option to `false`: 

```php
<?php

namespace App\Entity;

use Kreyu\Bundle\NipValidatorBundle\Validator\Constraints as KreyuAssert;

class Company
{
    /**
     * @KreyuAssert\Nip(
     *     checksum=false
     * )
     */
    private $nip;
}
```

#### Customization of the checksum violation message

It is possible to customize the checksum violation message using the `checksumMessage` option:

```php
<?php

namespace App\Entity;

use Kreyu\Bundle\NipValidatorBundle\Validator\Constraints as KreyuAssert;

class Company
{
    /**
     * @KreyuAssert\Nip(
     *     checksumMessage="This is not a valid NIP number."
     * )
     */
    private $nip;
}
```

## License

The MIT License (MIT). Please see [license file](LICENSE.md) for more information.
