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
        Schema::create('rental_items', function (Blueprint $table) {
            $table->id('id_rental_item')->index();
            $table->unsignedBigInteger('id_branch_rental_item'); // Foreign key ke branch
            $table->string('number_rental_item')->unique();
            $table->string('name_rental_item');
            $table->bigInteger('price_rental_item');
            $table->enum('status_rental_item', ['available', 'rented', 'maintenance'])->default('available');
            $table->enum('condition_rental_item', ['clean', 'dirty', 'damaged'])->default('clean');
            $table->text('description_rental_item')->nullable();
            $table->enum('is_active_rental_item', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // Foreign Key dengan ON CASCADE DELETE dan CASCADE UPDATE
            $table->foreign('id_branch_rental_item')
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
        Schema::dropIfExists('rental__items');
    }
};