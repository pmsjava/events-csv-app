<?php

declare(strict_types=1);

namespace App;

use App\Dto\Event;

final class EventsViewDataBuilder
{
    /**
     * @param array<int, Event> $events
     * @return array{
     *     data:array<int, array{
     *         event_date:string,
     *         city:string,
     *         category_label:string,
     *         total_tickets:string,
     *         show_date:bool,
     *         date_rowspan:int,
     *         show_city:bool,
     *         city_rowspan:int
     *     }>
     * }
     */
    public function build(array $events): array
    {
        $preparedEvents = [];
        foreach ($events as $event) {
            $category = $event->category();
            $preparedEvents[] = [
                'event_date' => $this->formatEventDate($event->eventDate()),
                'city' => $event->city(),
                'category_label' => $category->value === 'kids'
                    ? 'Dzieci'
                    : 'Dorosli',
                'total_tickets' => (string) $event->totalTickets(),
            ];
        }

        $result = [
            'data' => $preparedEvents,
        ];

        return $this->applyRowspans($events, $result);
    }

    /**
     * @param array<int, Event> $events
     * @param array{
     *     data:array<int, array{
     *         event_date:string,
     *         city:string,
     *         category_label:string,
     *         total_tickets:string
     *     }>
     * } $result
     * @return array{
     *     data:array<int, array{
     *         event_date:string,
     *         city:string,
     *         category_label:string,
     *         total_tickets:string,
     *         show_date:bool,
     *         date_rowspan:int,
     *         show_city:bool,
     *         city_rowspan:int
     *     }>
     * }
     */
    private function applyRowspans(array $events, array $result): array
    {
        $dateRowspans = [];
        $cityRowspans = [];
        $this->computeRowspans($events, $dateRowspans, $cityRowspans);

        foreach ($result['data'] as $i => $row) {
            $result['data'][$i] = [
                ...$row,
                'show_date' => isset($dateRowspans[$i]),
                'date_rowspan' => $dateRowspans[$i] ?? 0,
                'show_city' => isset($cityRowspans[$i]),
                'city_rowspan' => $cityRowspans[$i] ?? 0,
            ];
        }

        return $result;
    }

    /**
     * @param array<int, Event> $events
     * @param array<int, int> $dateRowspans
     * @param array<int, int> $cityRowspans
     */
    private function computeRowspans(array $events, array &$dateRowspans, array &$cityRowspans): void
    {
        $eventsCount = count($events);
        $index = 0;

        while ($index < $eventsCount) {
            $date = $events[$index]->eventDate();
            $dateSpan = 1;

            while (
                ($index + $dateSpan) < $eventsCount
                && $events[$index + $dateSpan]->eventDate() === $date
            ) {
                $dateSpan++;
            }

            $dateRowspans[$index] = $dateSpan;

            $cityIndex = $index;
            $dateEnd = $index + $dateSpan;
            while ($cityIndex < $dateEnd) {
                $city = $events[$cityIndex]->city();
                $citySpan = 1;

                while (
                    ($cityIndex + $citySpan) < $dateEnd
                    && $events[$cityIndex + $citySpan]->city() === $city
                ) {
                    $citySpan++;
                }

                $cityRowspans[$cityIndex] = $citySpan;
                $cityIndex += $citySpan;
            }

            $index = $dateEnd;
        }
    }

    private function formatEventDate(string $eventDate): string
    {
        $date = \DateTimeImmutable::createFromFormat('Y-m-d', $eventDate);
        if ($date === false) {
            return $eventDate;
        }

        return $date->format('d/m/Y');
    }
}
