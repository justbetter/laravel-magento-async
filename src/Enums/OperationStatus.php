<?php

namespace JustBetter\MagentoAsync\Enums;

enum OperationStatus: int
{
    case Complete = 1;
    case RetriablyFailed = 2;
    case NotRetriablyFailed = 3;
    case Open = 4;
    case Rejected = 5;

    /** @return array<int, OperationStatus> */
    public static function failedStatuses(): array
    {
        return [
            OperationStatus::RetriablyFailed,
            OperationStatus::NotRetriablyFailed,
        ];
    }
}
