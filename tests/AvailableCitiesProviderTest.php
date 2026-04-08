<?php

declare(strict_types=1);

namespace Tests;

use App\AvailableCitiesProvider;
use App\Dto\CsvOrderRow;
use PHPUnit\Framework\TestCase;

final class AvailableCitiesProviderTest extends TestCase
{
    public function testGetAvailableCitiesReturnsUniqueTrimmedAndSortedValues(): void
    {
        $provider = new AvailableCitiesProvider();

        $data = [
            ['event_id' => 'E1', 'event_date' => '2026-01-01', 'city' => '  Warsaw ', 'category' => 'kids', 'status' => 'confirmed', 'ticket_qty' => '1', 'utm_campaign' => 'a'],
            ['event_id' => 'E2', 'event_date' => '2026-01-02', 'city' => 'krakow', 'category' => 'adults', 'status' => 'confirmed', 'ticket_qty' => '1', 'utm_campaign' => 'b'],
            ['event_id' => 'E3', 'event_date' => '2026-01-03', 'city' => 'Krakow', 'category' => 'kids', 'status' => 'confirmed', 'ticket_qty' => '1', 'utm_campaign' => 'c'],
            ['event_id' => 'E4', 'event_date' => '2026-01-04', 'city' => '', 'category' => 'kids', 'status' => 'confirmed', 'ticket_qty' => '1', 'utm_campaign' => 'd'],
            ['event_id' => 'E5', 'event_date' => '2026-01-05', 'city' => 'Gdansk', 'category' => 'kids', 'status' => 'confirmed', 'ticket_qty' => '1', 'utm_campaign' => 'e'],
            ['event_id' => 'E6', 'event_date' => '2026-01-06', 'city' => 'Łódź', 'category' => 'kids', 'status' => 'confirmed', 'ticket_qty' => '1', 'utm_campaign' => 'f'],
            ['event_id' => 'E7', 'event_date' => '2026-01-07', 'city' => 'łódź', 'category' => 'kids', 'status' => 'confirmed', 'ticket_qty' => '1', 'utm_campaign' => 'g'],
        ];

        $cities = $provider->getAvailableCities($this->rows($data));

        self::assertSame(['Gdansk', 'krakow', 'Warsaw', 'Łódź'], $cities);
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
