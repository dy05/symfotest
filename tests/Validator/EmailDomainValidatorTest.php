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
        $validator = new EmailDomainValidator($this->getConfigRepository($blockedDomains));

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

    public function getConfigRepository(array $blockedDomains = []): ConfigRepository
    {
        $configRepository = $this->getMockBuilder(ConfigRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configRepository->method('getAsArray')
            ->willReturn($blockedDomains);

        return $configRepository;
    }

    protected function createValidator(): ConstraintValidatorInterface
    {
        return new EmailDomainValidator($this->getConfigRepository($this->blockedDomains));
    }

    /**
     * Rebuilds $this->validator and re-initializes it with a fresh context,
     * using new mock blockedDomains for this specific test.
     */
    private function useBlockedDomains(array $blockedDomains): void
    {
        $this->blockedDomains = $blockedDomains;
        $this->validator = $this->createValidator();
        $this->validator->initialize($this->context);
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

    public function testBlockedDomainFromDatabase()
    {
        $constraint = new EmailDomain([]);
        $this->useBlockedDomains(['yahmo.com']);
        $this->validator->validate('obbyto@yahmo.com', $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ value }}', 'obbyto@yahmo.com')
            ->assertRaised();
    }

    public function testAllowedDomainFromDatabase()
    {
        $constraint = new EmailDomain([]);
        $this->useBlockedDomains([]);
        $this->validator->validate('obbyto@yahmo.com', $constraint);

        $this->assertNoViolation();
    }
}
