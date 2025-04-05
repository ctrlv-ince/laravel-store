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
        Schema::create('item_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('item_id');
            $table->timestamps();

            $table->primary(['group_id', 'item_id']);
        });

        Schema::table('item_groups', function (Blueprint $table) {
            $table->foreign('group_id')
                  ->references('group_id')
                  ->on('groups')
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
        Schema::table('item_groups', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropForeign(['item_id']);
        });
        
        Schema::dropIfExists('item_groups');
    }
}; 