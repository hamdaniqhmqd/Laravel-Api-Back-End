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
        Schema::create('transaction_laundries', function (Blueprint $table) {
            $table->id('id_transaction_laundry');
            $table->unsignedBigInteger('id_user_transaction_laundry'); // Foreign key ke kurir
            $table->unsignedBigInteger('id_branch_transaction_laundry'); // Foreign key ke branch
            $table->time('time_transaction_laundry');
            $table->string('name_client_transaction_laundry');
            $table->enum('status_transaction_laundry', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->text('notes_transaction_laundry')->nullable();
            $table->double('total_weight_transaction_laundry', 8, 2);
            $table->double('total_price_transaction_laundry', 10, 2);
            $table->double('cash_transaction_laundry', 10, 2);
            $table->enum('is_active_transaction_laundry', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // Foreign Key dengan ON CASCADE DELETE dan CASCADE UPDATE
            $table->foreign('id_user_transaction_laundry')
                ->references('id_user')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('id_branch_transaction_laundry')
                ->references('id_branch')
                ->on('branches')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction__laundries');
    }
};
