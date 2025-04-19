<?php

namespace Tests\Unit\VO;

use App\Domain\VO\Phone;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PhoneTest extends TestCase
{
    public function test_it_accepts_a_valid_10_digit_phone(): void
    {
        $phone = new Phone('3133221122');
        $this->assertEquals('3133221122', $phone->getValue());
    }

    public function test_it_accepts_a_valid_11_digit_phone(): void
    {
        $phone = new Phone('31998765432');
        $this->assertEquals('31998765432', $phone->getValue());
    }

    public function test_it_strips_non_numeric_characters(): void
    {
        $phone = new Phone('(31) 99876-5432');
        $this->assertEquals('31998765432', $phone->getValue());
    }

    public function test_it_throws_exception_for_short_phone(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Phone('99876');
    }

    public function test_it_throws_exception_for_long_phone(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Phone('319998877665'); // muito longo
    }
}
