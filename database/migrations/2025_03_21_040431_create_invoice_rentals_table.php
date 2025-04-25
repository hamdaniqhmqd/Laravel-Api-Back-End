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
        Schema::create('invoice_rentals', function (Blueprint $table) {
            $table->id('id_invoice_rental')->index();
            $table->unsignedBigInteger('id_branch_invoice')->index();
            $table->unsignedBigInteger('id_client_invoice')->index();
            $table->string('number_invoice')->unique();
            $table->text('notes_invoice_rental')->nullable();
            $table->datetime('time_invoice_rental');
            $table->double('total_weight_invoice_rental', 8, 2);
            $table->double('price_invoice_rental', 12, 2);
            $table->double('promo_invoice_rental', 12, 2)->default(0);
            $table->double('additional_cost_invoice_rental', 12, 2)->default(0);
            $table->double('total_price_invoice_rental', 12, 2);
            $table->enum('is_active_invoice_rental', ['active', 'inactive'])->default('active');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('id_branch_invoice', 'fk_invoice_branch') // Nama constraint diperbaiki
                ->references('id_branch')
                ->on('branches')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('id_client_invoice', 'fk_invoice_client') // Nama constraint diperbaiki
                ->references('id_client')
                ->on('clients')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice__rentals');
    }
};
