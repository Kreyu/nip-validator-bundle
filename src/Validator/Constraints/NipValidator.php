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
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * @author Sebastian Wróblewski <kontakt@swroblewski.pl>
 */
class NipValidator extends ConstraintValidator
{
    /**
     * {@inheritDoc}
     *
     * @var Nip $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Nip) {
            throw new UnexpectedTypeException($constraint, Nip::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_scalar($value) && !(\is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedValueException($value, 'string');
        }

        $value = (string) $value;

        if ('' === $value) {
            return;
        }

        if (null !== $constraint->normalizer) {
            $value = ($constraint->normalizer)($value);
        }

        if (!preg_match($pattern = $this->getPattern($constraint), $value)) {
            $this->context->buildViolation($constraint->patternMessage)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setParameter('{{ pattern }}', $pattern)
                ->setInvalidValue($value)
                ->setCode(Nip::INVALID_PATTERN_ERROR)
                ->addViolation();

            return;
        }

        if ($constraint->checksum && !$this->validateChecksum($value, $constraint)) {
            $this->context->buildViolation($constraint->checksumMessage)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setInvalidValue($value)
                ->setCode(Nip::INVALID_CHECKSUM_ERROR)
                ->addViolation();

            return;
        }
    }

    /**
     * Validate the value againts the checksum.
     *
     * @param  string           $value
     * @param  Constraint|Nip   $constraint
     * @return string
     */
    public function validateChecksum($value, Constraint $constraint)
    {
        preg_match_all('!\d+!', $value, $matches);

        $chars = str_split(implode('', $matches[0]));

        $sum = array_sum(array_map(function ($weight, $digit) {
            return $weight * $digit;
        }, [6, 5, 7, 2, 3, 4, 5, 6, 7], array_slice($chars, 0, 9)));

        return $sum % 11 == $chars[9];
    }

    /**
     * Build the regular expression pattern to validate the value against.
     *
     * @param  Constraint|Nip $constraint
     * @return string
     */
    public function getPattern(Constraint $constraint)
    {
        if (null !== $constraint->pattern) {
            return $constraint->pattern;
        }

        $pattern = '/^';

        // Pattern: '/^([A-z]{2})?';
        if ($constraint->prefixLength > 0 && ($constraint->requirePrefix || $constraint->allowPrefix)) {
            $pattern .= '([A-z]{' . $constraint->prefixLength . '})';

            if (!$constraint->requirePrefix) {
                $pattern .= '?';
            }
        }

        // Pattern: '/^([A-z]{2})?((\d{2}-\d{3}-\d{3}-\d{2})|(\d{3}-\d{3}-\d{2}-\d{2})|(\d{10}))';
        if ($constraint->requireDashes || $constraint->allowDashes) {
            $pattern .= '((\d{2}-\d{3}-\d{3}-\d{2})|(\d{3}-\d{3}-\d{2}-\d{2})';

            if ($constraint->allowDashes) {
                $pattern .= '|(\d{10})';
            }

            $pattern .= ')';
        } else {
            $pattern .= '(\d{10})';
        }

        // Pattern: '/^([A-z]{2})?((\d{2}-\d{3}-\d{3}-\d{2})|(\d{3}-\d{3}-\d{2}-\d{2})|(\d{10}))$/';
        $pattern .= '$/';

        return $pattern;
    }
}
