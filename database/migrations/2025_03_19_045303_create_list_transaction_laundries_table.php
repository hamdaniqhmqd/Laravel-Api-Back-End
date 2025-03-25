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
        Schema::create('list_transaction_laundries', function (Blueprint $table) {
            $table->id('id_list_transaction_laundry')->index();
            $table->unsignedBigInteger('id_transaction_laundry')->index();
            $table->unsignedBigInteger('id_item_laundry')->index();
            $table->bigInteger('price_list_transaction_laundry');
            $table->double('weight_list_transaction_laundry', 8, 2);
            $table->integer('pcs_list_transaction_rental');
            $table->enum('status_list_transaction_laundry', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->text('note_list_transaction_laundry')->nullable();
            $table->enum('is_active_list_transaction_laundry', ['active', 'inactive'])->default('active');
            $table->softDeletes();
            $table->timestamps();

            // Foreign Key
            $table->foreign('id_transaction_laundry', 'id_laundry_transaction')
                ->references('id_transaction_laundry')
                ->on('transaction_laundries')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('id_item_laundry', 'id_laundry_transaction_item')
                ->references('id_laundry_item')
                ->on('laundry_items')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list__transaction__laundries');
    }
};
