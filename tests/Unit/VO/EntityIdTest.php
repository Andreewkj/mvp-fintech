<?php

namespace Tests\Unit\VO;

use App\Domain\VO\EntityId;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Tests\TestCase;

class EntityIdTest extends TestCase
{
    public function test_it_throws_exception_for_invalid_transfer_value()
    {
        $this->expectException(InvalidArgumentException::class);
        (new EntityId('wrong'))->getValue();
    }

    public function test_it_creates_a_valid_transfer_value()
    {
        $ulid = Str::ulid()->toString();
        $entityId = (new EntityId($ulid))->getValue();
        $this->assertEquals($ulid, $entityId);
    }
}
