<?php

declare(strict_types=1);

namespace Tests;

use App\Dto\EventFilters;
use PHPUnit\Framework\TestCase;

final class FiltersViewIntegrationTest extends TestCase
{
    public function testFiltersViewRendersCitySelectAndKeepsSelectedValue(): void
    {
        $eventFilters = new EventFilters(city: 'łódź');
        $availableCities = ['Gdansk', 'Łódź', 'Warsaw'];
        $_SERVER['PHP_SELF'] = '/public/index.php';

        ob_start();
        include __DIR__ . '/../views/filters.php';
        $html = (string) ob_get_clean();

        self::assertStringContainsString('<select id="city" name="city">', $html);
        self::assertMatchesRegularExpression(
            '/<option\s+value="Łódź"\s+selected\s*>/u',
            $html
        );
        self::assertStringContainsString('href="/public/index.php"', $html);
    }
}
