<?php

namespace Tests\Unit\VO;

use App\Domain\VO\Cnpj;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CnpjTest extends TestCase
{
    public function test_it_accepts_a_valid_cnpj(): void
    {
        $cnpj = new Cnpj('11222333000181'); // válido
        $this->assertEquals('11222333000181', $cnpj->getValue());
    }

    public function test_it_accepts_a_valid_masked_cnpj(): void
    {
        $cnpj = new Cnpj('11.222.333/0001-81'); // válido com máscara
        $this->assertEquals('11222333000181', $cnpj->getValue());
    }

    public function test_it_throws_exception_for_invalid_check_digits(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Cnpj('11222333000182'); // dígito verificador incorreto
    }

    public function test_it_throws_exception_for_invalid_length(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Cnpj('123456789'); // menos que 14 caracteres
    }
}
