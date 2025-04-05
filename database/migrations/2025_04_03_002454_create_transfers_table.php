<?php

use App\Enums\TransferStatusEnum;
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
            $table->unsignedBigInteger('amount');
            $table->enum('status', TransferStatusEnum::toArray())->default(TransferStatusEnum::STATUS_ACTIVE->value);
            $table->foreignUlid('payer_id')->references('id')->on('users');
            $table->foreignUlid('payee_id')->references('id')->on('users');
            $table->dateTime('refunded_at')->nullable();
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
