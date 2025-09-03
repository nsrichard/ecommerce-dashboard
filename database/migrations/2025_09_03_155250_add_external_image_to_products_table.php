<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('external_id')->after('id')->index();
            $table->text('image_url')->nullable()->after('currency');
            $table->unique(['store_id','external_id']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['products_store_id_external_id_unique']);
            $table->dropColumn(['external_id','image_url']);
        });
    }
};
