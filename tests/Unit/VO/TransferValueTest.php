<?php

namespace Tests\Unit\VO;

use App\Domain\VO\TransferValue;
use InvalidArgumentException;
use Tests\TestCase;

class TransferValueTest extends TestCase
{
    public function test_it_throws_exception_for_invalid_transfer_value()
    {
        $this->expectException(InvalidArgumentException::class);
        new TransferValue(0);

        new TransferValue(-100);
    }

    public function test_it_creates_a_valid_transfer_value()
    {
        $value = new TransferValue(150);
        $this->assertEquals(150, $value->getValue());
    }
}
