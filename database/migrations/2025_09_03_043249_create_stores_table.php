<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('platform', 20);
            $table->string('domain');
            $table->string('status', 20)->default('disconnected'); // disconnected|connected|error
            $table->timestamps();

            $table->unique(['platform', 'domain']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
