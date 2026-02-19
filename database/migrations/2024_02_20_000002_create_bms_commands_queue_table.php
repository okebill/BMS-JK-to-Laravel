<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bms_commands_queue', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->default('ESP32-001');
            $table->string('command_type')->comment('bms_write_register, bms_write_multiple_registers');
            $table->integer('register_address')->comment('Modbus register address (hex)');
            $table->json('command_data')->comment('Value(s) to write');
            $table->enum('status', ['pending', 'sent', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            $table->timestamps();
            $table->index(['device_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bms_commands_queue');
    }
};
