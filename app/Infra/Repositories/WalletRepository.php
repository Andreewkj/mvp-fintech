<?php

declare(strict_types=1);

namespace App\Infra\Repositories;

use App\Domain\Contracts\Repositories\WalletRepositoryInterface;
use App\Domain\Contracts\TransactionManagerInterface;
use App\Domain\Entities\Wallet;
use App\Domain\Exceptions\WalletException;
use App\Infra\Mappers\WalletMapper;
use App\Models\WalletModel;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Exception;

readonly class WalletRepository implements WalletRepositoryInterface
{
    public function __construct(
        private WalletModel                 $model,
        private TransactionManagerInterface $transactionManager
    )
    {}

    /**
     * @param Wallet $wallet
     * @return Wallet
     */
    public function create(Wallet $wallet) : Wallet
    {
        $model = WalletMapper::toModel($wallet);
        $model->save();

        return WalletMapper::toEntity($model);
    }

    /**
     * @param Wallet $wallet
     * @return void
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
                $walletModel = $this->model->query()->where('id', $wallet->getWalletId())
                    ->lockForUpdate()
                    ->first();

                if (!$walletModel) {
                    throw new WalletException('Wallet not found');
                }

                $walletModel->balance = $wallet->getBalance();
                $walletModel->save();

                $this->transactionManager->commit();
                return;

            } catch (QueryException $exception) {
                if ($this->isDeadlock($exception)) {
                    $this->transactionManager->rollback();
                    continue;
                }

                $this->transactionManager->rollback();
                throw $exception;
            }
        }

        Log::critical("Update balance for wallet: {$wallet->getWalletId()}, balance: {$wallet->getBalance()} failed after {$maxAttempts} attempts");
        throw new Exception('Transaction failed after multiple attempts due to deadlock');
    }

    /**
     * @param QueryException $exception
     * @return bool
     */
    private function isDeadlock(QueryException $exception): bool
    {
        return $exception->getCode() === '1213';
    }

    /**
     * @param string $getPayeeWalletId
     * @return Wallet|null
     */
    public function findById(string $getPayeeWalletId) : ?Wallet
    {
        $model = $this->model->query()->where('id', $getPayeeWalletId)->first();
        return $model ? WalletMapper::toEntity($model) : null;
    }

    /**
     * @param string $userId
     * @return Wallet|null
     */
    public function findWalletByUserId(string $userId) : ?Wallet
    {
        $model = $this->model->query()->where('user_id', $userId)->first();
        return $model ? WalletMapper::toEntity($model) : null;

    }

    /**
     * @param string $userId
     * @return bool
     */
    public function userWalletExist(string $userId) : bool
    {
        return $this->model->query()->where('user_id', $userId)->exists();
    }

    /**
     * @param string $walletId
     * @return Wallet|null
     */
    public function findUserByWalletById(string $walletId) : ?Wallet
    {
        $model = $this->model->query()->where('id', $walletId)->first();
        return $model ? WalletMapper::toEntity($model) : null;
    }
}
