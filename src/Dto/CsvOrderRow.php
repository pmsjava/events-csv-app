<?php

declare(strict_types=1);

namespace App\Dto;

final class CsvOrderRow
{
    public function __construct(
        private string $eventId,
        private string $eventDate,
        private string $city,
        private string $category,
        private string $status,
        private int $ticketQty,
        private string $utmCampaign
    ) {
    }

    /**
     * @param array<string, string> $row
     */
    public static function fromArray(array $row): self
    {
        return new self(
            eventId: (string) ($row['event_id'] ?? ''),
            eventDate: (string) ($row['event_date'] ?? ''),
            city: (string) ($row['city'] ?? ''),
            category: (string) ($row['category'] ?? ''),
            status: (string) ($row['status'] ?? ''),
            ticketQty: (int) ($row['ticket_qty'] ?? 0),
            utmCampaign: trim((string) ($row['utm_campaign'] ?? ''))
        );
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

    public function category(): string
    {
        return $this->category;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function ticketQty(): int
    {
        return $this->ticketQty;
    }

    public function utmCampaign(): string
    {
        return $this->utmCampaign;
    }
}
