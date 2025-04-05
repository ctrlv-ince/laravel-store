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
        Schema::create('item_images', function (Blueprint $table) {
            $table->id('image_id');
            $table->unsignedBigInteger('item_id');
            $table->string('image_path', 255);
            $table->boolean('is_primary')->default(false);
            $table->dateTime('uploaded_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();
        });

        Schema::table('item_images', function (Blueprint $table) {
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
        Schema::table('item_images', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
        });
        
        Schema::dropIfExists('item_images');
    }
}; 