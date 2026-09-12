<?php

namespace App\Tests\Validator;

use App\Validator\EmailDomain;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\MissingOptionsException;

class EmailDomainTest extends TestCase
{
    public function testRequireParameters(): void
    {
        $this->expectException(MissingOptionsException::class);
        new EmailDomain();
    }

    public function testBadShapedBlockedParameters(): void
    {
        $this->expectException(ConstraintDefinitionException ::class);
        new EmailDomain('yahmo');
    }

    public function testOptionsIsSetCorrectly(): void
    {
        $emailDomain = new EmailDomain(['yahmo' ]);
        $this->assertCount(1, $emailDomain->blockedDomains);
    }
}
