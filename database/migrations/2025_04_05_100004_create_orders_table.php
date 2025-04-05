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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->unsignedBigInteger('account_id');
            $table->dateTime('date_ordered');
            $table->integer('total_amount');
            $table->enum('status', ['pending', 'shipped', 'for_confirm', 'completed', 'cancelled']);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('account_id')
                  ->references('account_id')
                  ->on('accounts')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
        });
        
        Schema::dropIfExists('orders');
    }
}; 