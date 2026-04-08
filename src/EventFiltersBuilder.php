<?php

declare(strict_types=1);

namespace App;

use App\Dto\EventFilters;
use App\Enum\EventCategory;

class EventFiltersBuilder
{
    /**
     * @param array<string, mixed> $query
     */
    public function buildFromQuery(array $query): EventFilters
    {
        $categoryRaw = isset($query['category']) ? trim((string) $query['category']) : '';
        $category = EventCategory::tryFrom($categoryRaw);

        return new EventFilters(
            city: isset($query['city']) ? trim((string) $query['city']) : '',
            category: $category,
            dateFrom: isset($query['date_from']) ? trim((string) $query['date_from']) : '',
            dateTo: isset($query['date_to']) ? trim((string) $query['date_to']) : '',
        );
    }
}
