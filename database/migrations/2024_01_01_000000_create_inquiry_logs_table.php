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
        Schema::create('inquiry_logs', function (Blueprint $table) {
            $table->id();
            $table->string('method', 100)->index();
            $table->string('endpoint')->index();
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->integer('status_code')->nullable();
            $table->string('response_status', 50)->nullable(); // success, error, timeout, etc.
            $table->text('error_message')->nullable();
            $table->integer('response_time_ms')->nullable(); // Response time in milliseconds
            $table->string('ip_address', 45)->nullable(); // IPv6 support
            $table->string('user_agent')->nullable();
            $table->string('request_id', 100)->nullable()->index(); // For tracking related requests
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index(['method', 'created_at']);
            $table->index(['status_code', 'created_at']);
            $table->index(['response_status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiry_logs');
    }
};
