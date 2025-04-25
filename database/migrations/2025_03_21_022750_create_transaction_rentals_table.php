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
        Schema::create('transaction_rentals', function (Blueprint $table) {
            $table->id('id_transaction_rental')->index();
            $table->unsignedBigInteger('id_kurir_transaction_rental')->index();
            $table->unsignedBigInteger('id_branch_transaction_rental')->index();
            $table->unsignedBigInteger('id_client_transaction_rental')->index();
            $table->text('recipient_name_transaction_rental')->nullable();
            $table->enum('type_rental_transaction', ['bath towel', 'hand towel', 'gorden', 'keset']);
            $table->enum('status_transaction_rental', ['waiting for approval', 'approved', 'out', 'in', 'cancelled'])->default('waiting for approval');
            $table->integer('price_weight_transaction_rental');
            $table->double('total_weight_transaction_rental', 8, 2);
            $table->integer('total_pcs_transaction_rental');
            $table->double('promo_transaction_rental', 10, 2)->default(0);
            $table->double('additional_cost_transaction_rental', 10, 2)->default(0);
            $table->double('total_price_transaction_rental', 10, 2);
            $table->text('notes_transaction_rental')->nullable();
            $table->enum('is_active_transaction_rental', ['active', 'inactive'])->default('active');
            // $table->timestamp('first_date_transaction_rental')->nullable();
            // $table->timestamp('last_date_transaction_rental')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('id_kurir_transaction_rental')
                ->references('id_user')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('id_branch_transaction_rental')
                ->references('id_branch')
                ->on('branches')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('id_client_transaction_rental')
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
        Schema::dropIfExists('transaction__rentals');
    }
};
