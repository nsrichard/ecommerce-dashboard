<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->timestamp('last_synced_at')->nullable()->after('status');
            $table->string('last_sync_status', 20)->nullable()->after('last_synced_at');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['last_synced_at','last_sync_status']);
        });
    }
};
