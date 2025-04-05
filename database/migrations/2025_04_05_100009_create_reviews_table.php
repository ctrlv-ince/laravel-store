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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id('review_id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('item_id');
            $table->text('comment');
            $table->integer('rating');
            $table->timestamp('create_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('update_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->foreign('account_id')
                  ->references('account_id')
                  ->on('accounts')
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
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropForeign(['item_id']);
        });
        
        Schema::dropIfExists('reviews');
    }
}; 