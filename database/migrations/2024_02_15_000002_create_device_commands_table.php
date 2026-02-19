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
        Schema::create('device_commands', function (Blueprint $table) {
            $table->id();
            
            // Device Info
            $table->string('device_id')->nullable();
            $table->string('command_type')->comment('inverter_config, bms_config, etc');
            
            // Command Data (JSON untuk fleksibilitas)
            $table->json('command_data');
            
            // Status
            $table->enum('status', ['pending', 'sent', 'executed', 'failed'])->default('pending');
            
            // Execution Info
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->text('response')->nullable();
            
            // User who created the command
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['device_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_commands');
    }
};
