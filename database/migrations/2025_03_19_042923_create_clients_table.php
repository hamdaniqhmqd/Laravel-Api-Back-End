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
        Schema::create('clients', function (Blueprint $table) {
            $table->id('id_client')->index();
            $table->unsignedBigInteger('id_branch_client')->nullable(); // Relasi ke branch
            $table->string('name_client');
            $table->text('address_client')->nullable();
            $table->string('phone_client')->nullable();
            $table->enum('is_active_client', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->foreign('id_branch_client')
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
        Schema::dropIfExists('clients');
    }
};