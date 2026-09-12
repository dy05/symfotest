<?php

namespace App\Validator;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\MissingOptionsException;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class EmailDomain extends Constraint
{
    public string $message = 'The domain name is not allowed.';

    // All configurable options must be passed to the constructor.
    #[HasNamedArguments]
    public function __construct(
        public mixed $blockedDomains = null,
        public string $mode = 'strict',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct([], $groups, $payload);

        if (is_null($blockedDomains)) {
            throw new MissingOptionsException(
                'The "blockedDomains" option must be set.',
                ['blockedDomains']
            );
        }

        if (!is_array($blockedDomains)) {
            throw new ConstraintDefinitionException(
                'The "blockedDomains" option must be an array.'
            );
        }
    }
}
