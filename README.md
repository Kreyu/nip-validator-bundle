# NIP/TIN Validator Bundle
 
[![Latest Stable Version](https://poser.pugx.org/kreyu/nip-validator-bundle/version)](https://packagist.org/packages/kreyu/nip-validator-bundle)
[![Build Status](https://travis-ci.org/Kreyu/nip-validator-bundle.svg?branch=master)](https://travis-ci.org/Kreyu/nip-validator-bundle)

Validate the tax identification numbers with configurable validation constraint.

## Requirements

- Symfony validator >=6.1
- PHP >=8.1

For PHP 7.0 & Symfony 3.4, look at v1.x version.

## Installation

To download the bundle, require it using the Composer:

```
$ composer require kreyu/nip-validator-bundle
```

## Usage

Like with all Symfony validation constraints, you can apply it by using annotations:

```php
<?php

namespace App\Entity;

use Kreyu\Bundle\NipValidatorBundle\Validator\Constraints as Assert;

class Company
{
    /**
     * @Assert\Nip()
     */
    private $nip;
}
```

or by using YAML:

```yaml
App\Entity\Company:
    properties:
        nip:
            - Kreyu\Bundle\NipValidatorBundle\Validator\Constraints\Nip: ~
```

or by using XML:

```xml
<?xml version="1.0" encoding="UTF-8" ?>
<constraint-mapping xmlns="http://symfony.com/schema/dic/constraint-mapping"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://symfony.com/schema/dic/constraint-mapping https://symfony.com/schema/dic/constraint-mapping/constraint-mapping-1.0.xsd">

    <class name="App\Entity\Company">
        <property name="nip">
            <constraint name="Kreyu\Bundle\NipValidatorBundle\Validator\Constraints\Nip"/>
        </property>
    </class>
</constraint-mapping>
```

or by using PHP:

```php
<?php

namespace App\Entity;

use Kreyu\Bundle\NipValidatorBundle\Validator\Constraints\Nip;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class Company
{
    private $nip;
    
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('nip', new Nip());
    }
}
```

### Pattern validation

By default, the only accepted format is a string of characters without the prefix nor dashes.

#### Usage of dashes
  
If you wish to allow or require the usage of the dashes, use the `allowDashes` and `requireDashes` options:

```php
<?php

namespace App\Entity;

use Kreyu\Bundle\NipValidatorBundle\Validator\Constraints as Assert;

class Company
{
    /**
     * @Assert\Nip(
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

use Kreyu\Bundle\NipValidatorBundle\Validator\Constraints as Assert;

class Company
{
    /**
     * @Assert\Nip(
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

use Kreyu\Bundle\NipValidatorBundle\Validator\Constraints as Assert;

class Company
{
    /**
     * @Assert\Nip(
     *     patternMessage="This is not a valid NIP number."
     * )
     */
    private $nip;
}
```

You can use the following parameters in this message:

| Parameter | Description |
| --- | --- |
| `{{ value }}` | The current (invalid value) |
| `{{ pattern }}` | The regular expression pattern used in the validation |

#### Usage of a custom regular expression

If the default functionality does not meet your needs, consider using the `pattern` option:

```php
<?php

namespace App\Entity;

use Kreyu\Bundle\NipValidatorBundle\Validator\Constraints as Assert;

class Company
{
    /**
     * @Assert\Nip(
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

use Kreyu\Bundle\NipValidatorBundle\Validator\Constraints as Assert;

class Company
{
    /**
     * @Assert\Nip(
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

use Kreyu\Bundle\NipValidatorBundle\Validator\Constraints as Assert;

class Company
{
    /**
     * @Assert\Nip(
     *     checksumMessage="This is not a valid NIP number."
     * )
     */
    private $nip;
}
```

You can use the following parameters in this message:

| Parameter | Description |
| --- | --- |
| `{{ value }}` | The current (invalid value) |

#### Usage of a callable normalizer

It is possible to define the PHP callable to apply on the value before the validation, using the `normalizer` option:

```php
<?php

namespace App\Entity;

use Kreyu\Bundle\NipValidatorBundle\Validator\Constraints as Assert;

class Company
{
    /**
     * @Assert\Nip(
     *     normalizer="trim"
     * )
     */
    private $nip;
}
```

## License

The MIT License (MIT). Please see [license file](LICENSE.md) for more information.
