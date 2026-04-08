<?php

declare(strict_types=1);

namespace App;

class CsvReader
{
    /**
     * @return array<int, array<string, string>>
     */
    public function read(string $path): array
    {
        if (!is_file($path)) {
            throw new \InvalidArgumentException(sprintf('CSV file not found: %s', $path));
        }

        $cacheFile = $this->buildCacheFilePath($path);
        $cachedRows = $this->readFromCache($cacheFile);
        if ($cachedRows !== null) {
            return $cachedRows;
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new \RuntimeException(sprintf('Cannot open CSV file: %s', $path));
        }

        $headers = fgetcsv($handle, 0, ',', '"', '\\');
        if ($headers === false) {
            fclose($handle);
            return [];
        }

        $headers = $this->normalizeHeaders($headers);

        $rows = [];
        while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            if ($row === [null] || $row === []) {
                continue;
            }

            if (count($row) !== count($headers)) {
                $this->warnInvalidCsvRow(
                    sprintf(
                        'CSV row column count mismatch (expected %d, got %d). Row ignored.',
                        count($headers),
                        count($row)
                    )
                );
                continue;
            }

            $combined = array_combine($headers, $row);
            if ($combined === false) {
                $this->warnInvalidCsvRow('CSV row could not be combined with headers. Row ignored.');
                continue;
            }

            /** @var array<string, string> $combined */
            $rows[] = $combined;
        }

        fclose($handle);
        $this->saveToCache($cacheFile, $rows);

        return $rows;
    }

    private function buildCacheFilePath(string $path): string
    {
        $mtime = filemtime($path);
        $cacheKey = md5($path . '|' . (string) $mtime);

        return rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . 'events_csv_cache_'
            . $cacheKey
            . '.json';
    }

    /**
     * @return array<int, array<string, string>>|null
     */
    private function readFromCache(string $cacheFile): ?array
    {
        if (!is_file($cacheFile)) {
            return null;
        }

        $contents = file_get_contents($cacheFile);
        if ($contents === false) {
            return null;
        }

        try {
            $decoded = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        if (!is_array($decoded)) {
            return null;
        }

        $rows = [];
        foreach ($decoded as $row) {
            if (!is_array($row)) {
                return null;
            }

            $normalizedRow = [];
            foreach ($row as $k => $v) {
                if (!is_string($k) || !is_string($v)) {
                    return null;
                }
                $normalizedRow[$k] = $v;
            }

            /** @var array<string, string> $normalizedRow */
            $rows[] = $normalizedRow;
        }

        return $rows;
    }

    /**
     * @param array<int, array<string, string>> $rows
     */
    private function saveToCache(string $cacheFile, array $rows): void
    {
        try {
            $encoded = json_encode($rows, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return;
        }

        file_put_contents($cacheFile, $encoded);
    }

    /**
     * @param array<int, string|null> $headers
     * @return array<int, string>
     */
    private function normalizeHeaders(array $headers): array
    {
        $result = [];
        foreach ($headers as $i => $header) {
            $value = trim((string) $header);
            if ($i === 0) {
                $value = preg_replace('/^\xEF\xBB\xBF/', '', $value) ?? $value;
            }
            $result[] = $value;
        }

        return $result;
    }

    private function warnInvalidCsvRow(string $message): void
    {
        error_log('[warn] ' . $message);
    }
}
