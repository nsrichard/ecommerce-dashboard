<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->string('external_id')->index();
            $table->string('number');
            $table->string('status');
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->decimal('total', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->timestamp('external_created_at');
            $table->timestamps();

            $table->unique(['store_id','external_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
