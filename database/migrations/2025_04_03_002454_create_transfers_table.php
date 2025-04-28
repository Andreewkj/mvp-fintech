<?php

use App\Domain\Enums\TransferStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->unsignedBigInteger('value');
            $table->enum('status', TransferStatusEnum::toArray())->default(TransferStatusEnum::STATUS_PENDING->value);
            $table->foreignUlid('payer_wallet_id')->references('id')->on('wallets');
            $table->foreignUlid('payee_wallet_id')->references('id')->on('wallets');
            $table->dateTime('authorized_at')->nullable();
            $table->dateTime('denied_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
