<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('magento_bulk_request_operations', function (Blueprint $table): void {
            $table->index('status');
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('magento_bulk_request_operations', function (Blueprint $table): void {
            $table->dropIndex('status');
            $table->dropIndex(['status', 'created_at']);
        });
    }
};
