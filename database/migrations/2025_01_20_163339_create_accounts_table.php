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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('account_type', ['asset', 'income','liability', 'equity', 'revenue', 'expense','receivable', 'other']);
            $table->unsignedBigInteger('parent_account_id')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->enum('normal_balance', ['debit', 'credit']);
            $table->boolean('flag')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->foreign('parent_account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
