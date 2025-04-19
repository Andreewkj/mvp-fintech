<?php

namespace Tests\Unit\VO;

use App\Domain\VO\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function test_it_accepts_a_valid_email(): void
    {
        $email = new Email('user@example.com');
        $this->assertEquals('user@example.com', $email->getValue());
    }

    public function test_it_converts_email_to_lowercase(): void
    {
        $email = new Email('USER@EXAMPLE.COM');
        $this->assertEquals('user@example.com', $email->getValue());
    }

    public function test_it_throws_exception_for_invalid_email(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('invalid-email@');
    }
}
