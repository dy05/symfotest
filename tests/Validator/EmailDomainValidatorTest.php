<?php

namespace App\Tests\Validator;

use App\Validator\EmailDomain;
use App\Validator\EmailDomainValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

class EmailDomainValidatorTest extends  TestCase
{
    public function testCashBadDomains(): void
    {
        $constraint = new EmailDomain(['yahmo.com']);
        $context = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();
        $context->expects($this->once())
            ->method('buildViolation');
        $validator = new EmailDomainValidator();
        $validator->validateInContext('obbyto@yahmo.com', $constraint, $context);
    }
}
