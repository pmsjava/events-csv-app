<?php

declare(strict_types=1);

namespace Tests;

use App\Dto\CsvOrderRow;
use App\Dto\EventFilters;
use App\EventService;
use PHPUnit\Framework\TestCase;

final class EventServiceTest extends TestCase
{
    private EventService $service;

    protected function setUp(): void
    {
        $this->service = new EventService();
    }

    public function testTicketAggregationUsesOnlyConfirmedRecords(): void
    {
        $data = [
            ['event_id' => 'E1', 'event_date' => '2026-01-01', 'city' => 'Warsaw', 'category' => 'kids', 'status' => 'confirmed', 'ticket_qty' => '10', 'utm_campaign' => 'a'],
            ['event_id' => 'E1', 'event_date' => '2026-01-01', 'city' => 'Warsaw', 'category' => 'kids', 'status' => 'cancelled', 'ticket_qty' => '50', 'utm_campaign' => 'a'],
            ['event_id' => 'E1', 'event_date' => '2026-01-01', 'city' => 'Warsaw', 'category' => 'adults', 'status' => 'confirmed', 'ticket_qty' => '3', 'utm_campaign' => 'a'],
            ['event_id' => 'E1', 'event_date' => '2026-01-01', 'city' => 'Krakow', 'category' => 'kids', 'status' => 'confirmed', 'ticket_qty' => '4', 'utm_campaign' => 'a'],
            ['event_id' => 'E2', 'event_date' => '2026-01-01', 'city' => 'Warsaw', 'category' => 'kids', 'status' => 'confirmed', 'ticket_qty' => '5', 'utm_campaign' => 'a'],
            ['event_id' => 'E3', 'event_date' => '2026-01-01', 'city' => 'Krakow', 'category' => 'adults', 'status' => 'confirmed', 'ticket_qty' => '11', 'utm_campaign' => 'b'],
            ['event_id' => 'E3', 'event_date' => '2026-01-01', 'city' => 'Krakow', 'category' => 'adults', 'status' => 'cancelled', 'ticket_qty' => '99', 'utm_campaign' => 'b'],
            ['event_id' => 'E4', 'event_date' => '2026-01-02', 'city' => 'Warsaw', 'category' => 'kids', 'status' => 'confirmed', 'ticket_qty' => '7', 'utm_campaign' => 'a'],
            ['event_id' => 'E5', 'event_date' => '2026-01-02', 'city' => 'Warsaw', 'category' => 'kids', 'status' => 'cancelled', 'ticket_qty' => '40', 'utm_campaign' => 'a'],
            ['event_id' => 'E5', 'event_date' => '2026-01-02', 'city' => 'Warsaw', 'category' => 'kids', 'status' => 'confirmed', 'ticket_qty' => '40', 'utm_campaign' => 'a'],
            ['event_id' => 'E6', 'event_date' => '2026-01-02', 'city' => 'Warsaw', 'category' => 'kids', 'status' => 'cancelled', 'ticket_qty' => '40', 'utm_campaign' => 'a'],
        ];

        $events = $this->service->getEvents($this->rows($data), new EventFilters());

        self::assertSame(5, $events['count']);
        self::assertCount(6, $events['data']);

        $totalsByGroup = [];
        foreach ($events['data'] as $event) {
            $key = $event->eventId() . '|' . $event->category()->value;
            $totalsByGroup[$key] = $event->totalTickets();
        }

        self::assertSame(10, $totalsByGroup['E1|kids']);
        self::assertSame(3, $totalsByGroup['E1|adults']);
        self::assertSame(5, $totalsByGroup['E2|kids']);
        self::assertSame(11, $totalsByGroup['E3|adults']);
        self::assertSame(7, $totalsByGroup['E4|kids']);
        self::assertSame(40, $totalsByGroup['E5|kids']);
        self::assertArrayNotHasKey('E6|kids', $totalsByGroup);
    }

    public function testFilteringByCity(): void
    {
        $data = [
            ['event_id' => 'E1', 'event_date' => '2026-01-01', 'city' => 'Warsaw', 'category' => 'kids', 'status' => 'confirmed', 'ticket_qty' => '10', 'utm_campaign' => 'a'],
            ['event_id' => 'E2', 'event_date' => '2026-01-02', 'city' => 'Krakow', 'category' => 'adults', 'status' => 'confirmed', 'ticket_qty' => '20', 'utm_campaign' => 'b'],
        ];

        $events = $this->service->getEvents($this->rows($data), new EventFilters(city: 'Krakow'));

        self::assertSame(1, $events['count']);
        self::assertCount(1, $events['data']);
        self::assertSame('Krakow', $events['data'][0]->city());
        self::assertSame('E2', $events['data'][0]->eventId());
    }

    public function testFilteringByDateInDdMmYyyyFormat(): void
    {
        $data = [
            ['event_id' => 'E1', 'event_date' => '2026-01-01', 'city' => 'Warsaw', 'category' => 'kids', 'status' => 'confirmed', 'ticket_qty' => '10', 'utm_campaign' => 'a'],
            ['event_id' => 'E2', 'event_date' => '2026-01-10', 'city' => 'Krakow', 'category' => 'adults', 'status' => 'confirmed', 'ticket_qty' => '20', 'utm_campaign' => 'b'],
            ['event_id' => 'E3', 'event_date' => '2026-01-20', 'city' => 'Gdansk', 'category' => 'kids', 'status' => 'confirmed', 'ticket_qty' => '30', 'utm_campaign' => 'c'],
        ];

        $events = $this->service->getEvents(
            $this->rows($data),
            new EventFilters(dateFrom: '05/01/2026', dateTo: '15/01/2026')
        );

        self::assertSame(1, $events['count']);
        self::assertCount(1, $events['data']);
        self::assertSame('E2', $events['data'][0]->eventId());
    }

    public function testTopCampaignsRanking(): void
    {
        $data = [
            ['event_id' => 'E1', 'event_date' => '2026-01-01', 'city' => 'Warsaw', 'category' => 'kids', 'status' => 'confirmed', 'ticket_qty' => '10', 'utm_campaign' => 'alpha'],
            ['event_id' => 'E2', 'event_date' => '2026-01-02', 'city' => 'Krakow', 'category' => 'adults', 'status' => 'confirmed', 'ticket_qty' => '25', 'utm_campaign' => 'beta'],
            ['event_id' => 'E3', 'event_date' => '2026-01-03', 'city' => 'Gdansk', 'category' => 'adults', 'status' => 'cancelled', 'ticket_qty' => '100', 'utm_campaign' => 'gamma'],
            ['event_id' => 'E4', 'event_date' => '2026-01-04', 'city' => 'Gdansk', 'category' => 'kids', 'status' => 'confirmed', 'ticket_qty' => '5', 'utm_campaign' => 'alpha'],
        ];

        $ranking = $this->service->getTopCampaigns($this->rows($data));

        self::assertCount(2, $ranking);
        self::assertSame('beta', $ranking[0]['utm_campaign']);
        self::assertSame(25, $ranking[0]['total_tickets']);
        self::assertSame('alpha', $ranking[1]['utm_campaign']);
        self::assertSame(15, $ranking[1]['total_tickets']);
    }

    /**
     * @param array<int, array<string, string>> $rows
     * @return array<int, CsvOrderRow>
     */
    private function rows(array $rows): array
    {
        return array_map(
            static fn (array $row): CsvOrderRow => CsvOrderRow::fromArray($row),
            $rows
        );
    }
}
