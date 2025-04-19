<?php

namespace Tests\Unit\VO;

use App\Domain\VO\Cpf;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CpfTest extends TestCase
{
    public function test_it_accepts_a_valid_cpf(): void
    {
        $cpf = new Cpf('52998224725'); // válido
        $this->assertEquals('52998224725', $cpf->getValue());
    }

    public function test_it_accepts_a_valid_masked_cpf(): void
    {
        $cpf = new Cpf('529.982.247-25'); // válido com máscara
        $this->assertEquals('52998224725', $cpf->getValue());
    }

    public function test_it_throws_exception_for_invalid_check_digits(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Cpf('52998224726'); // dígito verificador inválido
    }

    public function test_it_throws_exception_for_invalid_length(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Cpf('123456'); // CPF muito curto
    }
}
