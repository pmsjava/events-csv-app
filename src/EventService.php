<?php

declare(strict_types=1);

namespace App;

use App\Dto\CsvOrderRow;
use App\Dto\Event;
use App\Dto\EventFilters;
use App\Enum\EventCategory;

class EventService
{
    /**
     * @param array<int, CsvOrderRow> $data
     * @return array{count:int, data:array<int, Event>}
     */
    public function getEvents(array $data, EventFilters $filters): array
    {
        $grouped = [];

        foreach ($data as $row) {
            if (!$this->matchesFilters($row, $filters)) {
                continue;
            }

            if ($row->status() !== 'confirmed') {
                continue;
            }

            $eventId = $row->eventId();
            if ($eventId === '') {
                continue;
            }

            $category = EventCategory::tryFrom($row->category());
            if ($category === null) {
                continue;
            }

            $groupKey = $eventId . '|' . $category->value;
            $eventDate = $row->eventDate();
            $city = $row->city();

            $tickets = $row->ticketQty();

            if (!isset($grouped[$groupKey])) {
                $grouped[$groupKey] = [
                    'event_id' => $eventId,
                    'event_date' => $eventDate,
                    'city' => $city,
                    'category' => $category,
                    'total_tickets' => 0,
                ];
            }

            if (
                $grouped[$groupKey]['event_date'] !== $eventDate
                || $grouped[$groupKey]['city'] !== $city
            ) {
                $this->warnInvalidCsvRow($eventId, $category->value, $eventDate, $city);
                continue;
            }

            $grouped[$groupKey]['total_tickets'] += $tickets;
        }

        usort(
            $grouped,
            static fn (array $a, array $b): int => strcmp((string) $a['event_date'], (string) $b['event_date'])
        );

        $events = array_values(array_map(
            static fn (array $group): Event => new Event(
                eventId: (string) $group['event_id'],
                eventDate: (string) $group['event_date'],
                city: (string) $group['city'],
                category: $group['category'],
                totalTickets: (int) $group['total_tickets']
            ),
            $grouped
        ));

        $uniqueEventIds = [];
        foreach ($events as $event) {
            $uniqueEventIds[$event->eventId()] = true;
        }

        return [
            'count' => count($uniqueEventIds),
            'data' => $events,
        ];
    }

    /**
     * @param array<int, CsvOrderRow> $data
     * @return array<int, array<string, int|string>>
     */
    public function getTopCampaigns(array $data): array
    {
        $campaignTotals = [];

        foreach ($data as $row) {
            if ($row->status() !== 'confirmed') {
                continue;
            }

            $campaign = $row->utmCampaign();
            if ($campaign === '') {
                continue;
            }

            $campaignTotals[$campaign] = ($campaignTotals[$campaign] ?? 0) + $row->ticketQty();
        }

        arsort($campaignTotals);

        $result = [];
        foreach (array_slice($campaignTotals, 0, 10, true) as $campaign => $totalTickets) {
            $result[] = [
                'utm_campaign' => $campaign,
                'total_tickets' => $totalTickets,
            ];
        }

        return $result;
    }

    /**
     * @param CsvOrderRow $row
     * @param EventFilters $filters
     * @return bool
     */
    private function matchesFilters(CsvOrderRow $row, EventFilters $filters): bool
    {
        if ($filters->city() !== '' && strcasecmp($row->city(), $filters->city()) !== 0) {
            return false;
        }

        $categoryFilter = $filters->category();
        $rowCategory = EventCategory::tryFrom($row->category());
        if ($categoryFilter !== null && $rowCategory !== $categoryFilter) {
            return false;
        }

        $dateFromTs = $this->parseDateToTimestamp($filters->dateFrom());
        $dateToTs = $this->parseDateToTimestamp($filters->dateTo());
        $rowDateTs = $this->parseDateToTimestamp($row->eventDate());

        if ($dateFromTs !== null && ($rowDateTs === null || $rowDateTs < $dateFromTs)) {
            return false;
        }

        if ($dateToTs !== null && ($rowDateTs === null || $rowDateTs > $dateToTs)) {
            return false;
        }

        return true;
    }

    private function parseDateToTimestamp(string $value): ?int
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }

        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $trimmed);
        if ($date !== false && $date->format('Y-m-d') === $trimmed) {
            return $date->getTimestamp();
        }

        $date = \DateTimeImmutable::createFromFormat('!d/m/Y', $trimmed);
        if ($date !== false && $date->format('d/m/Y') === $trimmed) {
            return $date->getTimestamp();
        }

        return null;
    }

    private function warnInvalidCsvRow(
        string $eventId,
        string $category,
        string $eventDate,
        string $city
    ): void {
        $message = sprintf(
            '[warn] invalid CSV row ignored for event_id=%s, category=%s, event_date=%s, city=%s',
            $eventId,
            $category,
            $eventDate,
            $city
        );

        if (defined('STDERR')) {
            fwrite(STDERR, $message . PHP_EOL);
            return;
        }

        error_log($message);
    }
}
