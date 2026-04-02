<?php

declare(strict_types=1);

namespace JustBetter\MagentoAsync\Enums;

enum ItemStatus: string
{
    case Accepted = 'accepted';
    case Rejected = 'rejected';
}
