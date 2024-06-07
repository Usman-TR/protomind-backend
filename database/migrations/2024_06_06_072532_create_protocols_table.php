<?php

use App\Enums\ProtocolStatusEnum;
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
        Schema::create('protocols', function (Blueprint $table) {
            $table->id();
            $table->string('theme');
            $table->string('agenda');
            $table->date('event_date');
            $table->foreignId('secretary_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('director_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default(ProtocolStatusEnum::PROCESS);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('protocols');
    }
};
