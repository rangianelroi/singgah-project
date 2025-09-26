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
        Schema::create('disposal_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('confiscated_items')->onDelete('cascade');
            $table->enum('disposal_method', ['destroyed', 'handed_to_police', 'other']);
            $table->foreignId('authorized_by_user_id')->constrained('users')->onDelete('restrict');
            $table->string('report_document_url')->nullable();
            $table->date('disposal_date');
            $table->text('witnesses')->nullable();
            $table->timestamps();

            $table->index(['item_id', 'disposal_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposal_records');
    }
};
