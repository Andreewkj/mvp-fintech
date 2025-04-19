<?php

namespace Tests\Unit\VO;

use App\Domain\VO\Account;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    public function test_it_generates_account_when_none_is_provided(): void
    {
        $account = new Account();

        $this->assertNotNull($account->getValue());
        $this->assertEquals(15, strlen($account->getValue()));
        $this->assertStringStartsWith(Account::DIGITAL_AGENCY, $account->getValue());
    }

    public function test_it_returns_the_provided_account_when_valid(): void
    {
        $value = '0001123456789-0';
        $account = new Account($value);

        $this->assertEquals($value, $account->getValue());
    }

    public function test_it_throws_exception_when_account_is_invalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Account('invalid');
    }
}
