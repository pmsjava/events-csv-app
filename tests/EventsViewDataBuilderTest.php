<?php

declare(strict_types=1);

namespace Tests;

use App\Dto\Event;
use App\Enum\EventCategory;
use App\EventsViewDataBuilder;
use PHPUnit\Framework\TestCase;

final class EventsViewDataBuilderTest extends TestCase
{
    public function testBuildFormatsRows(): void
    {
        $builder = new EventsViewDataBuilder();

        $result = $builder->build([
            new Event('E1', '2026-01-01', 'Warsaw', EventCategory::KIDS, 10),
            new Event('E1', '2026-01-01', 'Warsaw', EventCategory::ADULTS, 5),
            new Event('E2', 'not-a-date', 'Krakow', EventCategory::ADULTS, 8),
        ]);

        self::assertCount(3, $result['data']);

        self::assertSame('01/01/2026', $result['data'][0]['event_date']);
        self::assertSame('Dzieci', $result['data'][0]['category_label']);
        self::assertSame('10', $result['data'][0]['total_tickets']);

        self::assertSame('Dorosli', $result['data'][1]['category_label']);
        self::assertSame('not-a-date', $result['data'][2]['event_date']);
    }

    public function testBuildComputesDateAndCityRowspans(): void
    {
        $builder = new EventsViewDataBuilder();

        $result = $builder->build([
            new Event('E1', '2026-01-01', 'Warsaw', EventCategory::KIDS, 10),
            new Event('E2', '2026-01-01', 'Warsaw', EventCategory::ADULTS, 20),
            new Event('E3', '2026-01-01', 'Krakow', EventCategory::KIDS, 30),
            new Event('E4', '2026-01-02', 'Krakow', EventCategory::ADULTS, 40),
        ]);

        self::assertTrue($result['data'][0]['show_date']);
        self::assertSame(3, $result['data'][0]['date_rowspan']);
        self::assertFalse($result['data'][1]['show_date']);
        self::assertSame(0, $result['data'][1]['date_rowspan']);
        self::assertFalse($result['data'][2]['show_date']);
        self::assertSame(0, $result['data'][2]['date_rowspan']);
        self::assertTrue($result['data'][3]['show_date']);
        self::assertSame(1, $result['data'][3]['date_rowspan']);

        self::assertTrue($result['data'][0]['show_city']);
        self::assertSame(2, $result['data'][0]['city_rowspan']);
        self::assertFalse($result['data'][1]['show_city']);
        self::assertSame(0, $result['data'][1]['city_rowspan']);
        self::assertTrue($result['data'][2]['show_city']);
        self::assertSame(1, $result['data'][2]['city_rowspan']);
        self::assertTrue($result['data'][3]['show_city']);
        self::assertSame(1, $result['data'][3]['city_rowspan']);
    }

    public function testBuildReturnsEmptyStateForNoEvents(): void
    {
        $builder = new EventsViewDataBuilder();

        $result = $builder->build([]);

        self::assertSame([], $result['data']);
    }
}
