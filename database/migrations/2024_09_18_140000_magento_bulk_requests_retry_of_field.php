<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('magento_bulk_requests', function (Blueprint $table): void {
            $table->unsignedBigInteger('retry_of')->after('id')->nullable();

            $table->foreign('retry_of')->references('id')->on('magento_bulk_requests')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropColumns('magento_bulk_requests', ['retry_of']);
    }
};
