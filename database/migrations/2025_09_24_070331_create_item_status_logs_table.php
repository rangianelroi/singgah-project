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
        Schema::create('item_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('confiscated_items')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->enum('status', [
                'RECORDED',
                'VERIFIED_BY_SQUAD_LEADER',
                'PENDING_PICKUP',
                'VERIFIED_FOR_STORAGE',
                'IN_STORAGE',
                'PENDING_SHIPMENT_CONFIRMATION',
                'READY_TO_SHIP',
                'SHIPPED',
                'PICKED_UP',
                'DISPOSED'
            ]);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['item_id', 'created_at']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_status_logs');
    }
};