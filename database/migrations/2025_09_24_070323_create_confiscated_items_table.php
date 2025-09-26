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
        Schema::create('confiscated_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('passenger_id')->constrained('passengers')->onDelete('restrict');
            $table->foreignId('flight_id')->constrained('flights')->onDelete('restrict');
            $table->foreignId('recorded_by_user_id')->constrained('users')->onDelete('restrict');
            
            $table->string('item_name');
            $table->string('item_image_path')->nullable();
            $table->enum('category', ['dangerous_goods', 'prohibited_items', 'security_items', 'other']);
            $table->integer('item_quantity')->default(1);
            $table->string('item_unit', 50)->default('unit');
            $table->text('notes')->nullable();
            $table->dateTime('confiscation_date');
            $table->string('storage_location')->nullable();
            $table->timestamps();

            $table->index(['passenger_id', 'flight_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('confiscated_items');
    }
};
