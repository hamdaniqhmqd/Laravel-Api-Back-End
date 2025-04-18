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
        Schema::create('list_invoice_rentals', function (Blueprint $table) {
            $table->id('id_list_invoice_rental')->index();
            $table->unsignedBigInteger('id_rental_invoice')->index();
            $table->unsignedBigInteger('id_rental_transaction')->index();
            $table->enum('status_list_invoice_rental', ['unpaid', 'paid', 'cancelled'])->default('unpaid');
            $table->text('note_list_invoice_rental')->nullable();
            $table->double('price_list_invoice_rental', 12, 2);
            $table->double('weight_list_invoice_rental', 8, 2);
            $table->enum('is_active_list_invoice_rental', ['active', 'inactive'])->default('active');
            $table->softDeletes();
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('id_rental_invoice', 'fk_id_rental_invoice')
                ->references('id_invoice_rental')
                ->on('invoice_rentals')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('id_rental_transaction', 'fk_invoice_transaction_rental') // Nama constraint diperbaiki
                ->references('id_transaction_rental')
                ->on('transaction_rentals')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list__invoice__rentals');
    }
};