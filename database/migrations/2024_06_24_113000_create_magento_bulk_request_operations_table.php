<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use JustBetter\MagentoAsync\Models\BulkRequest;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('magento_bulk_request_operations', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(BulkRequest::class)->constrained('magento_bulk_requests')->cascadeOnDelete();
            $table->nullableMorphs('subject');
            $table->integer('operation_id');
            $table->integer('status')->nullable();
            $table->json('response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('magento_bulk_request_operations');
    }
};
