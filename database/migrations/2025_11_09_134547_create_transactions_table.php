<?php

use App\Enums\RecurringTransactionUnit;
use App\Enums\TransactionType;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('label');
            $table->string('description')->nullable();
            $table->date('date');
            $table->boolean('is_recurring');
            $table->integer('recurring_interval')->nullable();
            $table->enum('recurring_unit', RecurringTransactionUnit::cases())->nullable();
            $table->string('location')->nullable();
            $table->float('amount');
            $table->enum('type', TransactionType::cases());
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('budget_id')->nullable()->constrained('budgets')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
