<?php

declare(strict_types=1);

namespace App;

use App\Dto\CsvOrderRow;

final class AvailableCitiesProvider
{
    /**
     * @param array<int, CsvOrderRow> $data
     * @return array<int, string>
     */
    public function getAvailableCities(array $data): array
    {
        $citiesByKey = [];

        foreach ($data as $row) {
            $city = trim($row->city());
            if ($city === '') {
                continue;
            }

            $normalizedKey = mb_strtolower($city, 'UTF-8');
            if (!isset($citiesByKey[$normalizedKey])) {
                $citiesByKey[$normalizedKey] = $city;
            }
        }

        $cities = array_values($citiesByKey);
        usort(
            $cities,
            static fn (string $a, string $b): int => strcasecmp($a, $b)
        );

        return $cities;
    }
}
