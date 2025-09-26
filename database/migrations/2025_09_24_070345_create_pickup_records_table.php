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
        Schema::create('pickup_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('confiscated_items')->onDelete('cascade');
            $table->string('pickup_by_name');
            $table->string('pickup_by_identity_number');
            $table->string('photo_of_recipient_path')->nullable();
            $table->string('photo_of_identity_path')->nullable();
            $table->string('relationship_to_passenger');
            $table->foreignId('verified_by_user_id')->constrained('users')->onDelete('restrict');
            $table->dateTime('pickup_timestamp');
            $table->timestamps();

            $table->index(['item_id', 'verified_by_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickup_records');
    }
};
