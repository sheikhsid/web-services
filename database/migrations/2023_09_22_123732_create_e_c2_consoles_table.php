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
        Schema::create('e_c2_consoles', function (Blueprint $table) {
            $table->id();
            $table->string('user');
            $table->string('token_id')->unique();
            $table->string('key');
            $table->string('secret');
            $table->string('template');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_c2_consoles');
    }
};
