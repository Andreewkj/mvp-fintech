<?php

declare(strict_types=1);

namespace App\Domain\VO;

class Account
{
    const DIGITAL_AGENCY = '0001';

    public function __construct(private ?string $account = null)
    {
        $this->validate($account);
    }

    private function validate(?string $account): void
    {
        if (empty($account)) {
            $this->generate();
        }

        if (strlen($this->account) !== 15) {
            throw new \InvalidArgumentException('Invalid account length');
        }

    }

    public function generate(): void
    {
        $account = (string) rand(100000000, 999999999);

        $this->account = self::DIGITAL_AGENCY . $account . '-'. '0';
    }

    public function getValue(): ?string
    {
        return $this->account;
    }
}
