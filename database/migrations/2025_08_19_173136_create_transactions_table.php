<?php

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
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('description')->nullable();
            $table->date('date');
            $table->boolean('is_recurring');
            $table->integer('recurring_interval')->nullable();
            $table->enum('recurring_unit', ['day', 'week', 'month', 'year'])->nullable();
            $table->string('location')->nullable();
            $table->float('amount');
            $table->enum('type', ['income', 'expense']);
            $table->foreignUuid('account_id')->constrained('accounts');
            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('budget_id')->constrained('budgets');
            $table->timestamps();
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
