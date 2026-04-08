<h2>Wydarzenia (<?= $eventsCount ?>)</h2>
<?php include __DIR__ . '/filters.php'; ?>

<table border="1" cellpadding="6" cellspacing="0" style="margin-top:20px">
    <thead>
    <tr>
        <th>Data wydarzenia</th>
        <th>Miasto</th>
        <th>Kategoria</th>
        <th>Liczba biletow</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($eventsViewData['data'] as $event): ?>
        <tr>
            <?php if ($event['show_date']): ?>
                <td
                    rowspan="<?= $event['date_rowspan'] ?>"
                    style="text-align: center; vertical-align: middle;"
                >
                    <?= htmlspecialchars((string) $event['event_date'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                </td>
            <?php endif; ?>
            <?php if ($event['show_city']): ?>
                <td rowspan="<?= $event['city_rowspan'] ?>"><?= htmlspecialchars((string) $event['city'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
            <?php endif; ?>
            <td><?= htmlspecialchars((string) $event['category_label'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string) $event['total_tickets'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if ($eventsViewData['data'] === []): ?>
        <tr>
            <td colspan="4">Brak wydarzen.</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
