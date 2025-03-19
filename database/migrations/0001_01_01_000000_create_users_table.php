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
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user');
            $table->unsignedBigInteger('id_branch_user')->nullable();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('fullname_user');
            $table->enum('role_user', ['admin', 'owner', 'kurir']);
            $table->enum('gender_user', ['male', 'female']);
            $table->string('phone_user')->nullable();
            $table->text('address_user')->nullable();
            $table->enum('is_active_user', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // Jika id_branch_user merupakan foreign key, tambahkan constraint berikut
            // $table->foreign('id_branch_user')->references('id')->on('branch')->onDelete('cascade');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};