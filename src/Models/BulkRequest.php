<?php

namespace JustBetter\MagentoAsync\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $magento_connection
 * @property string $store_code
 * @property string $path
 * @property string $bulk_uuid
 * @property array $request
 * @property array $response
 * @property ?Carbon $started_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property Collection<int, BulkOperation> $operations
 */
class BulkRequest extends Model
{
    protected $table = 'magento_bulk_requests';

    protected $guarded = [];

    protected $casts = [
        'request' => 'array',
        'response' => 'array',
        'started_at' => 'datetime',
    ];

    /** @return HasMany<BulkOperation> */
    public function operations(): HasMany
    {
        return $this->hasMany(BulkOperation::class);
    }
}
