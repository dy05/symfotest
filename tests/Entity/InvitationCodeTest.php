<?php

namespace App\Tests\Entity;

use App\Entity\InvitationCode;
use DateTime;
use DateTimeInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class InvitationCodeTest extends KernelTestCase
{
    public function testSomething(): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());
        // $routerService = static::getContainer()->get('router');
        // $myCustomService = static::getContainer()->get(CustomService::class);
    }

    public function getEntity(string $code, string $description, ?DateTimeInterface $date = null): ?InvitationCode
    {
        return new InvitationCode()
            ->setCode($code)
            ->setDescription($description)
            ->setExpiredAt($date ?? new DateTime());
    }

    public function assertHasErrors(mixed $expectedCount, mixed $entity): void
    {
        /** @var ValidatorInterface $validator */
        $validator = static::getContainer()->get('validator');
        $result = $validator->validate($entity);

        $this->assertCount($expectedCount, $result);
    }

    public function testValidEntity()
    {
        $code = $this->getEntity('12345', 'Description de test');
        $this->assertHasErrors(0, $code);
    }

    public function testInvalidEntity()
    {
        $code = $this->getEntity('123456', 'Description faux test');
        $this->assertHasErrors(1, $code);

        $code = $this->getEntity('1234o', 'Description faux test');
        $this->assertHasErrors(1, $code);
    }

    public function testInvalidBlankCodeEntity()
    {
        $code = $this->getEntity('', 'Description blank test');

        $this->assertHasErrors(2, $code);
    }
}
