<?php

namespace App\Validator;

use App\Repository\ConfigRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class EmailDomainValidator extends ConstraintValidator
{
    private array $globalBlockedDomainsList;
    private ConfigRepository $configRepository;

    public function __construct(ConfigRepository $configRepository, string $globalBlockedDomains = '')
    {
        $this->configRepository = $configRepository;
        $this->globalBlockedDomainsList = explode(',', $globalBlockedDomains);
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var EmailDomain $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $blockedDomains = array_merge(
            $constraint->blockedDomains,
            $this->configRepository->getAsArray('blocked_domains'),
            $this->globalBlockedDomainsList
        );

        $domain = substr($value, strpos($value, '@') + 1);

        if (in_array($domain, $blockedDomains)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
