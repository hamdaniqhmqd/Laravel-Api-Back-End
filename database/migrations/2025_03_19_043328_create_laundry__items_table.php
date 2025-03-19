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
        Schema::create('laundry__items', function (Blueprint $table) {
            $table->id('id_laundry_item');
            $table->unsignedBigInteger('id_branch_laundry_item')->nullable(); // Foreign key ke branch
            $table->string('name_laundry_item');
            $table->bigInteger('price_laundry_item');
            $table->time('time_laundry__item')->nullable();
            $table->text('description_laundry_item')->nullable();
            $table->enum('is_active_laundry_item', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // Foreign Key
            $table->foreign('id_branch_laundry_item')
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
        Schema::dropIfExists('laundry__items');
    }
};