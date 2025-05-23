<?php

declare(strict_types=1);

namespace App\Infra\Repositories;

use App\Domain\Contracts\Repositories\WalletRepositoryInterface;
use App\Domain\Contracts\TransactionManagerInterface;
use App\Domain\Entities\Wallet;
use App\Domain\Exceptions\WalletException;
use App\Infra\Mappers\WalletMapper;
use App\Models\WalletModel;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

readonly class WalletRepository implements WalletRepositoryInterface
{
    public function __construct(
        private WalletModel                 $model,
        private TransactionManagerInterface $transactionManager
    )
    {}

    /**
     * @throws WalletException
     * @throws Exception
     */
    public function updateBalance(Wallet $wallet): void
    {
        $maxAttempts = 5;
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            $attempt++;

            $this->transactionManager->beginTransaction();

            try {
                $walletModel = $this->model->query()->where('id', $wallet->getId())
                    ->lockForUpdate()
                    ->first();

                if (!$walletModel) {
                    throw new WalletException('Wallet not found');
                }

                $walletModel->balance = $wallet->getBalance();
                $walletModel->save();

                $this->transactionManager->commit();
                return;

            } catch (QueryException $e) {
                if ($this->isDeadlock($e)) {
                    $this->transactionManager->rollback();
                    continue;
                }

                $this->transactionManager->rollback();
                throw $e;
            }
        }

        Log::critical("Update balance for wallet: {$wallet->getId()}, balance: {$wallet->getBalance()} failed after {$maxAttempts} attempts");
        throw new Exception('Transaction failed after multiple attempts due to deadlock');
    }

    private function isDeadlock(QueryException $e): bool
    {
        return $e->getCode() === '1213';
    }

    public function findById(string $getPayeeWalletId) : ?Wallet
    {
        $model = $this->model->query()->where('id', $getPayeeWalletId)->first();
        return $model ? WalletMapper::toEntity($model) : null;
    }

    public function create(array $data) : Wallet
    {
        $model = $this->model->create($data);
        $model->refresh();

        return WalletMapper::toEntity($model);
    }

    public function findWalletByUserId(string $userId) : ?Wallet
    {
        $model = $this->model->query()->where('user_id', $userId)->first();
        return $model ? WalletMapper::toEntity($model) : null;

    }

    public function userWalletExist(string $userId) : bool
    {
        return $this->model->query()->where('user_id', $userId)->exists();
    }

    public function findUserByWalletById(string $walletId) : ?Wallet
    {
        $model = $this->model->query()->where('id', $walletId)->first();
        return $model ? WalletMapper::toEntity($model) : null;
    }
}
