<?php

declare(strict_types=1);

namespace Tests;

use App\Enum\EventCategory;
use App\EventFiltersBuilder;
use PHPUnit\Framework\TestCase;

final class EventFiltersBuilderTest extends TestCase
{
    public function testBuildFromQueryTrimsAndMapsValidCategory(): void
    {
        $builder = new EventFiltersBuilder();

        $filters = $builder->buildFromQuery([
            'city' => '  Krakow ',
            'category' => ' kids ',
            'date_from' => ' 01/02/2026 ',
            'date_to' => ' 05/02/2026 ',
        ]);

        self::assertSame('Krakow', $filters->city());
        self::assertSame(EventCategory::KIDS, $filters->category());
        self::assertSame('01/02/2026', $filters->dateFrom());
        self::assertSame('05/02/2026', $filters->dateTo());
    }

    public function testBuildFromQueryUsesDefaultsForMissingValues(): void
    {
        $builder = new EventFiltersBuilder();

        $filters = $builder->buildFromQuery([]);

        self::assertSame('', $filters->city());
        self::assertNull($filters->category());
        self::assertSame('', $filters->dateFrom());
        self::assertSame('', $filters->dateTo());
    }

    public function testBuildFromQueryReturnsNullForInvalidCategory(): void
    {
        $builder = new EventFiltersBuilder();

        $filters = $builder->buildFromQuery([
            'category' => 'vip',
        ]);

        self::assertNull($filters->category());
    }
}
