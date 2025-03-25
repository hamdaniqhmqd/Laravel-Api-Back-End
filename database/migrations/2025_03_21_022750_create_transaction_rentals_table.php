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
            $table->text('recipient_name_transaction_rental');
            $table->enum('status_transaction_rental', ['waiting for approval', 'approved', 'out', 'in', 'cancelled'])->default('waiting for approval');
            $table->double('total_weight_transaction_rental', 8, 2);
            $table->bigInteger('total_pcs_transaction_rental');
            $table->bigInteger('promo_transaction_rental')->default(0);
            $table->bigInteger('additional_cost_transaction_rental')->default(0);
            $table->bigInteger('total_price_transaction_rental');
            $table->text('notes_transaction_laundry')->nullable();
            // $table->time('time_transaction_rental');
            $table->enum('is_active_transaction_rental', ['active', 'inactive'])->default('active');
            $table->date('first_date_transaction_rental');
            $table->date('last_date_transaction_rental');
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
