<?php

/*
 * This file is part of the Nip Validator Bundle package.
 *
 * (c) Sebastian Wróblewski <kontakt@swroblewski.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kreyu\Bundle\NipValidatorBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 * @author Sebastian Wróblewski <kontakt@swroblewski.pl>
 */
class Nip extends Constraint
{
    const INVALID_PATTERN_ERROR = 'b2e5a844-1127-43dd-be56-861a5e37fd8e';
    const INVALID_CHECKSUM_ERROR = 'ba94bb8a-88bc-4be8-9577-f71ec17cd7e7';

    protected static $errorNames = [
        self::INVALID_PATTERN_ERROR => 'INVALID_PATTERN_ERROR',
        self::INVALID_CHECKSUM_ERROR => 'INVALID_CHECKSUM_ERROR',
    ];

    public $pattern = null;
    public $patternMessage = 'This is not a valid NIP number.';
    public $checksum = true;
    public $checksumMessage = 'This is not a valid NIP number.';
    public $allowDashes = false;
    public $requireDashes = false;
    public $allowPrefix = false;
    public $requirePrefix = false;
    public $prefixLength = 2;
}

class_alias(Nip::class, 'Kreyu\Bundle\NipValidatorBundle\Validator\Constraints\Tin');
