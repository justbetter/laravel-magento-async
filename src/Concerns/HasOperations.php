<?php

namespace JustBetter\MagentoAsync\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use JustBetter\MagentoAsync\Models\BulkOperation;

/**
 * @mixin Model
 *
 * @codeCoverageIgnore
 */
trait HasOperations
{
    public function bulkOperations(): MorphMany
    {
        return $this->morphMany(BulkOperation::class, 'subject');
    }
}
