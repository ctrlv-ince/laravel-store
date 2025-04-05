<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orderinfos', function (Blueprint $table) {
            $table->id('orderinfo_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('item_id');
            $table->integer('quantity');
            $table->dateTime('created')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();
        });

        Schema::table('orderinfos', function (Blueprint $table) {
            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('orders')
                  ->onDelete('cascade');
            
            $table->foreign('item_id')
                  ->references('item_id')
                  ->on('items')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orderinfos', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['item_id']);
        });
        
        Schema::dropIfExists('orderinfos');
    }
}; 