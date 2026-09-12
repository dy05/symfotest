<?php

namespace App\Tests\Validator;

use App\Validator\EmailDomain;
use App\Validator\EmailDomainValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class EmailDomainValidatorTest extends  TestCase
{
    public function testCashBadDomains(): void
    {
        $constraint = new EmailDomain(['yahmo.com']);
        $context = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();
        $violation = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMock();

        $violation->method('setParameter')->willReturn($violation);
        $violation->expects($this->once())->method('addViolation');

        $context->expects($this->once())
            ->method('buildViolation')
            ->willReturn($violation);

        $validator = new EmailDomainValidator();
        $validator->validateInContext('obbyto@yahmo.com', $constraint, $context);
    }
}
