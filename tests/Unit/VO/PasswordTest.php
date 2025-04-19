<?php

namespace Tests\Unit\VO;

use App\Domain\VO\Password;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PasswordTest extends TestCase
{
    public function test_it_accepts_a_valid_password(): void
    {
        $password = new Password('secret123');
        $this->assertEquals('secret123', $password->getValue());
    }

    public function test_it_throws_exception_for_short_password(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Password('123');
    }
}
