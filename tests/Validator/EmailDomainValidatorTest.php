<?php

namespace App\Tests\Validator;

use App\Validator\EmailDomain;
use App\Validator\EmailDomainValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class EmailDomainValidatorTest extends ConstraintValidatorTestCase
{
    /**
     * @return array{0: EmailDomainValidator, 1: ExecutionContextInterface}
     */
    public function getValidator(bool $expectViolation = false): array
    {
        $validator = new EmailDomainValidator();

        $context = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();

        if ($expectViolation) {
            $violation = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMock();
            $violation->method('setParameter')->willReturn($violation);
            $violation->expects($this->once())->method('addViolation');

            $context->expects($this->once())
                ->method('buildViolation')
                ->willReturn($violation);
        } else {
            $context->expects($this->never())
                ->method('buildViolation');
        }

        return [$validator, $context];
    }

    public function testCashBadDomains(): void
    {
        $constraint = new EmailDomain(['yahmo.com']);
        [$validator, $context] = $this->getValidator(true);
        $validator->validateinContext('obbyto@yahmo.com', $constraint, $context);
    }

    protected function createValidator(): ConstraintValidatorInterface
    {
        return new EmailDomainValidator();
    }

    public function testBlockedDomainRaisesViolation(): void
    {
        $constraint = new EmailDomain(['yahmo.com']);

        $this->validator->validate('obbyto@yahmo.com', $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ value }}', 'obbyto@yahmo.com')
            ->assertRaised();
    }

    public function testAllowedDomainIsValid(): void
    {
        $this->validator->validate('user@allowed.com', new EmailDomain(['yahmo.com']));
        $this->assertNoViolation();
    }
}
