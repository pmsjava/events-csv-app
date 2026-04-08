<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enum\EventCategory;

final class EventFilters
{
    public function __construct(
        private string $city = '',
        private ?EventCategory $category = null,
        private string $dateFrom = '',
        private string $dateTo = ''
    ) {
    }

    public function city(): string
    {
        return $this->city;
    }

    public function category(): ?EventCategory
    {
        return $this->category;
    }

    public function dateFrom(): string
    {
        return $this->dateFrom;
    }

    public function dateTo(): string
    {
        return $this->dateTo;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'city' => $this->city,
            'category' => $this->category?->value ?? '',
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
        ];
    }
}
