<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('magento_bulk_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('magento_connection');
            $table->string('store_code');
            $table->string('path');
            $table->string('bulk_uuid')->index();
            $table->json('request');
            $table->json('response');
            $table->timestamp('started_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('magento_bulk_requests');
    }
};
