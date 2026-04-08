<?php

declare(strict_types=1);

namespace Tests;

use App\CsvReader;
use PHPUnit\Framework\TestCase;

final class CsvReaderTest extends TestCase
{
    public function testThrowsExceptionWhenFileDoesNotExist(): void
    {
        $reader = new CsvReader();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('CSV file not found:');

        $reader->read('/this/path/does/not/exist.csv');
    }

    public function testReturnsEmptyArrayWhenCsvHasNoRows(): void
    {
        $path = $this->createTempCsvFile("event_id,event_date,city,category,status,ticket_qty,utm_campaign\n");
        $reader = new CsvReader();

        self::assertSame([], $reader->read($path));
    }

    public function testReadsRowsAndSkipsEmptyLine(): void
    {
        $csv = <<<'CSV'
event_id,event_date,city,category,status,ticket_qty,utm_campaign
E1,2026-01-01,Warsaw,kids,confirmed,10,alpha

E2,2026-01-02,Krakow,adults,cancelled,2,beta
CSV;
        $path = $this->createTempCsvFile($csv . "\n");
        $reader = new CsvReader();

        $rows = $reader->read($path);

        self::assertCount(2, $rows);
        self::assertSame('E1', $rows[0]['event_id']);
        self::assertSame('Warsaw', $rows[0]['city']);
        self::assertSame('E2', $rows[1]['event_id']);
        self::assertSame('beta', $rows[1]['utm_campaign']);
    }

    private function createTempCsvFile(string $content): string
    {
        $path = tempnam(sys_get_temp_dir(), 'events_csv_reader_test_');
        self::assertNotFalse($path);

        file_put_contents($path, $content);

        return $path;
    }
}
