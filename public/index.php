<?php

declare(strict_types=1);

use App\CsvReader;
use App\Dto\CsvOrderRow;
use App\AvailableCitiesProvider;
use App\EventsViewDataBuilder;
use App\EventService;
use App\EventFiltersBuilder;

require_once __DIR__ . '/../vendor/autoload.php';

$csvReader = new CsvReader();
$availableCitiesProvider = new AvailableCitiesProvider();
$eventFiltersBuilder = new EventFiltersBuilder();
$eventService = new EventService();

$data = $csvReader->read(__DIR__ . '/../data/orders.csv');
$orderRows = array_map(
    static fn (array $row): CsvOrderRow => CsvOrderRow::fromArray($row),
    $data
);

$eventFilters = $eventFiltersBuilder->buildFromQuery($_GET);
$events = $eventService->getEvents($orderRows, $eventFilters);

$availableCities = $availableCitiesProvider->getAvailableCities($orderRows);
$eventsCount = $events['count'];
$eventsViewData = (new EventsViewDataBuilder())->build($events['data']);
$topCampaigns = $eventService->getTopCampaigns($orderRows);

include __DIR__ . '/../views/layout.php';
