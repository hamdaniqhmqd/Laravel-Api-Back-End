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
        Schema::create('list_transaction_rentals', function (Blueprint $table) {
            $table->id('id_list_transaction_rental')->index();
            $table->unsignedBigInteger('id_rental_transaction')->index();
            $table->unsignedBigInteger('id_item_rental')->index();
            $table->enum('status_list_transaction_rental', ['rented', 'returned', 'cancelled'])->default('rented');
            $table->enum('condition_list_transaction_rental', ['clean', 'dirty', 'damaged'])->default('clean');
            $table->text('note_list_transaction_rental')->nullable();
            $table->double('price_list_transaction_rental', 10, 2);
            $table->double('weight_list_transaction_rental', 8, 2);
            $table->enum('is_active_list_transaction_rental', ['active', 'inactive'])->default('active');
            $table->softDeletes();
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('id_rental_transaction', 'fk_id_transaction_rental')
                ->references('id_transaction_rental')
                ->on('transaction_rentals')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('id_item_rental', 'fk_id_item_rental')
                ->references('id_rental_item')
                ->on('rental_items')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list__transaction__rentals');
    }
};
