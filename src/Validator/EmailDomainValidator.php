<?php

namespace App\Validator;

use Exception;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class EmailDomainValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var EmailDomain $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        foreach ($constraint->blockedDomains as $blockedDomain) {
            if (str_contains($value, $blockedDomain)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $value)
                    ->addViolation();
            }
        }
    }
}
