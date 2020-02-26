<?php

/*
 * This file is part of the Nip Validator Bundle package.
 *
 * (c) Sebastian Wróblewski <kontakt@swroblewski.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kreyu\Bundle\NipValidatorBundle\Tests\Validator\Constraints;

use Kreyu\Bundle\NipValidatorBundle\Validator\Constraints\Nip;
use Kreyu\Bundle\NipValidatorBundle\Validator\Constraints\NipValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @property-read NipValidator $validator
 *
 * @author Sebastian Wróblewski <kontakt@swroblewski.pl>
 */
class NipValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
    {
        return new NipValidator();
    }

    public function getCustomPatternValidChecksums()
    {
        return [
            ['15.757.258.57'],
            ['15.644.738.72'],
            ['56.605.269.52'],
            ['15.706.794.37'],
            ['56.758.226.68'],
        ];
    }

    /**
     * @dataProvider getCustomPatternValidChecksums
     */
    public function testCustomPatternWithValidChecksum($value)
    {
        $constraint = new Nip([
            'pattern' => '/^(\d{2}.\d{3}.\d{3}.\d{2})$/',
            'requireDashes' => true, // Should be ignored
            'requirePrefix' => true, // Should be ignored
        ]);

        $this->validator->validate($value, $constraint);

        $this->assertNoViolation();
    }

    public function getCustomPatternInvalidChecksums()
    {
        return [
            ['87.612.873.52'],
            ['18.273.263.19'],
            ['92.481.028.23'],
            ['88.760.965.43'],
            ['16.323.499.87'],
        ];
    }

    /**
     * @dataProvider getCustomPatternInvalidChecksums
     */
    public function testCustomPatternWithInvalidChecksum($value)
    {
        $constraint = new Nip([
            'pattern' => '/^(\d{2}.\d{3}.\d{3}.\d{2})$/',
        ]);

        $this->validator->validate($value, $constraint);

        $this->buildViolation($constraint->checksumMessage)
            ->setParameter('{{ value }}', '"'.$value.'"')
            ->setInvalidValue($value)
            ->setCode(Nip::INVALID_CHECKSUM_ERROR)
            ->assertRaised();
    }

    /**
     * @dataProvider getCustomPatternInvalidChecksums
     */
    public function testCustomPatternWithoutChecksum($value)
    {
        $constraint = new Nip([
            'pattern' => '/^(\d{2}.\d{3}.\d{3}.\d{2})$/',
            'checksum' => false,
        ]);

        $this->validator->validate($value, $constraint);

        $this->assertNoViolation();
    }

    public function getNoDashesNoPrefixValidChecksums()
    {
        return [
            ['3774988224'],
            ['3784078972'],
            ['7745293711'],
            ['1239664059'],
            ['1589583841'],
        ];
    }

    public function getNoDashesNoPrefixInvalidChecksums()
    {
        return [
            ['8761287352'],
            ['1827326319'],
            ['9248102823'],
            ['8876096543'],
            ['1632349987'],
        ];
    }

    /**
     * @dataProvider getNoDashesNoPrefixInvalidChecksums
     */
    public function testCustomChecksumMessage($value)
    {
        $constraint = new Nip([
            'checksumMessage' => 'My custom checksum message',
        ]);

        $this->validator->validate($value, $constraint);

        $this->buildViolation('My custom checksum message')
            ->setParameter('{{ value }}', '"'.$value.'"')
            ->setInvalidValue($value)
            ->setCode(Nip::INVALID_CHECKSUM_ERROR)
            ->assertRaised();
    }

    /**
     * @dataProvider getNoDashesNoPrefixInvalidChecksums
     */
    public function testCustomPatternMessage($value)
    {
        $constraint = new Nip([
            'requireDashes' => true,
            'patternMessage' => 'My custom pattern message',
        ]);

        $this->validator->validate($value, $constraint);

        $this->buildViolation('My custom pattern message')
            ->setParameter('{{ value }}', '"'.$value.'"')
            ->setParameter('{{ pattern }}', $this->validator->getPattern($constraint))
            ->setInvalidValue($value)
            ->setCode(Nip::INVALID_PATTERN_ERROR)
            ->assertRaised();
    }

    public function getWithAndWithoutDashesMixed()
    {
        return [
            ['3774988224'],
            ['37-840-789-72'],
            ['77-452-937-11'],
            ['123-966-40-59'],
            ['1589583841'],
        ];
    }

    /**
     * @dataProvider getWithAndWithoutDashesMixed
     */
    public function testAllowingDashesShouldAcceptBothWithAndWithout($value)
    {
        $constraint = new Nip([
            'allowDashes' => true,
            'requireDashes' => false,
        ]);

        $this->validator->validate($value, $constraint);

        $this->assertNoViolation();
    }

    public function getWithoutDashes()
    {
        return [
            ['3774988224'],
            ['3784078972'],
            ['7745293711'],
            ['1239664059'],
            ['1589583841'],
        ];
    }

    /**
     * @dataProvider getWithoutDashes
     */
    public function testRequiringDashesShouldNotAcceptWithout($value)
    {
        $constraint = new Nip([
            'requireDashes' => true,
            'allowDashes' => false, // Should be ignored
        ]);

        $this->validator->validate($value, $constraint);

        $this->buildViolation($constraint->patternMessage)
            ->setParameter('{{ value }}', '"'.$value.'"')
            ->setParameter('{{ pattern }}', $this->validator->getPattern($constraint))
            ->setInvalidValue($value)
            ->setCode(Nip::INVALID_PATTERN_ERROR)
            ->assertRaised();
    }

    public function getWithDashesBothPatterns()
    {
        return [
            ['37-749-882-24'],
            ['37-840-789-72'],
            ['77-452-937-11'],
            ['123-966-40-59'],
            ['158-958-38-41'],
        ];
    }

    /**
     * @dataProvider getWithDashesBothPatterns
     */
    public function testRequiringDashesShouldAcceptBothPatterns($value)
    {
        $constraint = new Nip([
            'requireDashes' => true,
            'allowDashes' => false, // Should be ignored
        ]);

        $this->validator->validate($value, $constraint);

        $this->assertNoViolation();
    }

    public function getWithAndWithoutPrefixMixed()
    {
        return [
            ['PL3774988224'],
            ['EN3784078972'],
            ['DE7745293711'],
            ['1239664059'],
            ['1589583841'],
        ];
    }

    /**
     * @dataProvider getWithAndWithoutPrefixMixed
     */
    public function testAllowingPrefixShouldAcceptBothWithAndWithout($value)
    {
        $constraint = new Nip([
            'allowPrefix' => true,
            'requirePrefix' => false,
        ]);

        $this->validator->validate($value, $constraint);

        $this->assertNoViolation();
    }

    public function getWithoutPrefix()
    {
        return [
            ['3774988224'],
            ['3784078972'],
            ['7745293711'],
            ['1239664059'],
            ['1589583841'],
        ];
    }

    /**
     * @dataProvider getWithoutPrefix
     */
    public function testRequiringPrefixShouldNotAcceptWithout($value)
    {
        $constraint = new Nip([
            'requirePrefix' => true,
            'allowPrefix' => false, // Should be ignored
        ]);

        $this->validator->validate($value, $constraint);

        $this->buildViolation($constraint->patternMessage)
            ->setParameter('{{ value }}', '"'.$value.'"')
            ->setParameter('{{ pattern }}', $this->validator->getPattern($constraint))
            ->setInvalidValue($value)
            ->setCode(Nip::INVALID_PATTERN_ERROR)
            ->assertRaised();
    }

    public function getWithCustomLengthPrefix()
    {
        return [
            ['APL3774988224'],
            ['BEN3784078972'],
            ['CDE7745293711'],
            ['DAA1239664059'],
            ['EBB1589583841'],
        ];
    }

    /**
     * @dataProvider getWithCustomLengthPrefix
     */
    public function testCustomPrefixLength($value)
    {
        $constraint = new Nip([
            'requirePrefix' => true,
            'allowPrefix' => false, // Should be ignored
            'prefixLength' => 3,
        ]);

        $this->validator->validate($value, $constraint);

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getWithCustomLengthPrefix
     */
    public function testInvalidCustomPrefixLength($value)
    {
        $constraint = new Nip([
            'requirePrefix' => true,
            'allowPrefix' => false, // Should be ignored
            'prefixLength' => 1,
        ]);

        $this->validator->validate($value, $constraint);

        $this->buildViolation($constraint->patternMessage)
            ->setParameter('{{ value }}', '"'.$value.'"')
            ->setParameter('{{ pattern }}', $this->validator->getPattern($constraint))
            ->setInvalidValue($value)
            ->setCode(Nip::INVALID_PATTERN_ERROR)
            ->assertRaised();
    }

    public function testNullValueIsIgnored()
    {
        $this->validator->validate(null, new Nip());
        $this->assertNoViolation();
    }

    public function testEmptyValueIsIgnored()
    {
        $this->validator->validate('', new Nip());
        $this->assertNoViolation();
    }

    public function testInvalidConstraintThrowsException()
    {
        $constraint = new class extends Constraint {};

        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(null, $constraint);
    }

    public function testInvalidValueTypeThrowsException()
    {
        $this->expectException(UnexpectedValueException::class);
        $this->validator->validate([], new Nip());
    }

    public function testObjectValueWithoutToStringMethodThrowsException()
    {
        $this->expectException(UnexpectedValueException::class);
        $this->validator->validate(new class {}, new Nip());
    }

    public function testObjectValueWithToStringMethodIsAccepted()
    {
        $value = new class {
            public function __toString()
            {
                return '1111111111';
            }
        };

        $this->validator->validate($value, new Nip());
        $this->assertNoViolation();
    }

    public function testConstraintNormalizer()
    {
        $this->validator->validate('111111111', new Nip([
            'normalizer' => function ($value) {
                return $value . '1';
            },
        ]));
        $this->assertNoViolation();
    }

    public function testConstraintNormalizerWithPhpFunction()
    {
        $this->validator->validate('   1111111111 ', new Nip([
            'normalizer' => 'trim',
        ]));
        $this->assertNoViolation();
    }
}
