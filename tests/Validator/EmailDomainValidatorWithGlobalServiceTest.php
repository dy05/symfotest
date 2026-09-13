<?php

namespace App\Tests\Validator;

use App\Validator\EmailDomain;
use App\Validator\EmailDomainValidator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class EmailDomainValidatorWithGlobalServiceTest extends KernelTestCase
{
    /**
     * @return array{0: EmailDomainValidator, 1: ExecutionContextInterface}
     */
    public function getValidator(bool $expectViolation = false): array
    {
        $validator = static::getContainer()->get(EmailDomainValidator::class);
        $context = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();

        if ($expectViolation) {
            $violation = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMock();
            $violation->method('setParameter')->willReturn($violation);
            $violation->expects($this->atLeastOnce())->method('addViolation');

            $context->expects($this->atLeastOnce())
                ->method('buildViolation')
                ->willReturn($violation);
        } else {
            $context->expects($this->never())
                ->method('buildViolation');
        }

        return [$validator, $context];
    }

    public function testGlobalBlockedDomainRaisesViolation(): void
    {
        $constraint = new EmailDomain([]);
        [$validator, $context] = $this->getValidator(true);
        $validator->validateInContext(
            'obbyto@obbydev.com',
            $constraint,
            $context
        );

        $validator->validateInContext(
            'obbyto@test.com',
            $constraint,
            $context
        );
    }

    public function testGlobalBlockedDomainAttemptSuccess(): void
    {
        $constraint = new EmailDomain([]);
        [$validator, $context] = $this->getValidator();
        $validator->validateInContext(
            'obbyto@yahmo.com',
            $constraint,
            $context
        );
    }
}
