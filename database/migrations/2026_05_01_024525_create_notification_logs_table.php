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
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pet_id')->nullable()->constrained('pets')->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->string('notification_type');
            $table->string('channel', 30);
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->dateTime('send_at')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->string('status', 30)->default('pending');
            $table->string('dedup_key')->nullable()->unique();
            $table->string('provider_message_id')->nullable();
            $table->text('error_message')->nullable();
            $table->json('meta')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['notification_type', 'status']);
            $table->index(['channel', 'status']);
            $table->index(['send_at', 'status']);
            $table->index(['sent_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
