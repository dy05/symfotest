<?php

namespace App\Tests\Validator;

use App\Repository\ConfigRepository;
use App\Validator\EmailDomain;
use App\Validator\EmailDomainValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class EmailDomainValidatorTest extends ConstraintValidatorTestCase
{
    private array $blockedDomains = [];

    /**
     * @return array{0: EmailDomainValidator, 1: ExecutionContextInterface}
     */
    public function getValidator(array $blockedDomains = [], bool $expectViolation = false): array
    {
        $configRepository = $this->getMockBuilder(ConfigRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configRepository
            ->expects($this->atLeastOnce())
            ->method('getAsArray')
            ->willReturn($blockedDomains);

        $validator = new EmailDomainValidator($configRepository);

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
        [$validator, $context] = $this->getValidator(['yahmo.com'], true);
        $validator->validateinContext('obbyto@yahmo.com', $constraint, $context);
    }

    protected function createValidator(): ConstraintValidatorInterface
    {
        $configRepository = $this->getMockBuilder(ConfigRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configRepository
            ->expects($this->never())
            ->method('getAsArray')
            ->willReturn($this->blockedDomains);

        return new EmailDomainValidator($configRepository);
    }

    private function useStrictRepository(array $blockedDomains = []): void
    {
        $configRepository = $this->getMockBuilder(ConfigRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configRepository->expects($this->atLeastOnce())
            ->method('getAsArray')
            ->with('blocked_domains')
            ->willReturn($blockedDomains);

        $this->validator = new EmailDomainValidator($configRepository);
    }

    public function testBlockedDomainRaisesViolation(): void
    {
        $constraint = new EmailDomain(['yahmo.com']);
        $this->useStrictRepository();
        $this->validator->validateInContext(
            'obbyto@yahmo.com',
            $constraint,
            $this->context
        );

        $this->buildViolation($constraint->message)
            ->setParameter('{{ value }}', 'obbyto@yahmo.com')
            ->assertRaised();
    }

    public function testAllowedDomainIsValid(): void
    {
        $this->useStrictRepository();
        $this->validator->validateInContext(
            'user@allowed.com', new EmailDomain(['yahmo.com']),
            $this->context
        );
        $this->assertNoViolation();
    }

    public function testBlockedDomainFromDatabase()
    {
        $constraint = new EmailDomain([]);
        $this->useStrictRepository(['yahmo.com']);
        $this->validator->validateInContext('obbyto@yahmo.com', $constraint, $this->context);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ value }}', 'obbyto@yahmo.com')
            ->assertRaised();
    }

    public function testAllowedDomainFromDatabase()
    {
        $constraint = new EmailDomain([]);
        $this->useStrictRepository();
        $this->validator->validateInContext('obbyto@yahmo.com', $constraint, $this->context);

        $this->assertNoViolation();
    }

    public function testEmptyValueSkipsValidationAndRepository(): void
    {
        $this->validator->validateInContext('', new EmailDomain(['yahmo.com']), $this->context);

        $this->assertNoViolation();
    }
}
