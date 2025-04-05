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
        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('account_id');
            $table->unsignedBigInteger('user_id');
            $table->string('username', 50)->unique()->nullable();
            $table->string('password', 255)->nullable();
            $table->enum('role', ['admin', 'user']);
            $table->string('profile_img', 255);
            $table->enum('account_status', ['active', 'inactive']);
            $table->timestamps();
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        
        Schema::dropIfExists('accounts');
    }
}; 