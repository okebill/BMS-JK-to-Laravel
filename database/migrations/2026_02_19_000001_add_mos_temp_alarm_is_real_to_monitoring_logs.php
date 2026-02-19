<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitoring_logs', function (Blueprint $table) {
            // MOSFET temperature
            if (!Schema::hasColumn('monitoring_logs', 'mos_temp')) {
                $table->decimal('mos_temp', 5, 2)->nullable()->after('temperature2')
                      ->comment('MOSFET temperature °C');
            }

            // Alarm real flag
            if (!Schema::hasColumn('monitoring_logs', 'alarm_is_real')) {
                $table->boolean('alarm_is_real')->default(false)->after('alarm_text')
                      ->comment('True jika alarm benar-benar berbahaya');
            }
        });
    }

    public function down(): void
    {
        Schema::table('monitoring_logs', function (Blueprint $table) {
            $table->dropColumnIfExists('mos_temp');
            $table->dropColumnIfExists('alarm_is_real');
        });
    }
};
