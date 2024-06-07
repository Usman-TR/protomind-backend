<?php

use App\Enums\ProtocolTaskStatusEnum;
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
        Schema::table('protocol_tasks', function (Blueprint $table) {
            $table->string('status')->default(ProtocolTaskStatusEnum::PROCESS);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('protocol_tasks', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
