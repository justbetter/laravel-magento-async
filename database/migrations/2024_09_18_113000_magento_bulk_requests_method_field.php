<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('magento_bulk_requests', function (Blueprint $table): void {
            $table->string('method')->after('store_code');
        });
    }

    public function down(): void
    {
        Schema::dropColumns('magento_bulk_requests', ['method']);
    }
};
