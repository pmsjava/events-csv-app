<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enum\EventCategory;

final class Event
{
    public function __construct(
        private string $eventId,
        private string $eventDate,
        private string $city,
        private EventCategory $category,
        private int $totalTickets
    ) {
    }

    public function eventId(): string
    {
        return $this->eventId;
    }

    public function eventDate(): string
    {
        return $this->eventDate;
    }

    public function city(): string
    {
        return $this->city;
    }

    public function category(): EventCategory
    {
        return $this->category;
    }

    public function totalTickets(): int
    {
        return $this->totalTickets;
    }
}
