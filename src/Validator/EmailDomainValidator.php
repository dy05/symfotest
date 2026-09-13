<?php

namespace App\Validator;

use App\Repository\ConfigRepository;
use Exception;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class EmailDomainValidator extends ConstraintValidator
{
    public function __construct(protected ConfigRepository $configRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var EmailDomain $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $blockedDomains = array_merge(
            $constraint->blockedDomains,
            $this->configRepository->getAsArray('blockedDomain')
        );

        $domain = substr($value, strpos($value, '@') + 1);

        if (in_array($domain, $blockedDomains)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
