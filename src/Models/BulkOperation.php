<?php

namespace JustBetter\MagentoAsync\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use JustBetter\MagentoAsync\Enums\OperationStatus;

/**
 * @property int $id
 * @property int $bulk_request_id
 * @property ?string $subject_type
 * @property int $operation_id
 * @property ?OperationStatus $status
 * @property ?array $response
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Model $subject
 * @property ?BulkRequest $request
 */
class BulkOperation extends Model
{
    protected $table = 'magento_bulk_request_operations';

    protected $guarded = [];

    protected $casts = [
        'status' => OperationStatus::class,
        'response' => 'array',
    ];

    /** @return MorphTo<Model, BulkOperation> */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /** @return BelongsTo<BulkRequest, BulkOperation> */
    public function request(): BelongsTo
    {
        return $this->belongsTo(BulkRequest::class, 'bulk_request_id');
    }
}
