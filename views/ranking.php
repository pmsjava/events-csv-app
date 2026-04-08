<section style="margin-top: 18px; padding: 12px 14px; border: 1px solid #d6d6d6; border-radius: 8px; background-color: #f3f3f3;">
    <h2 style="margin-top: 0;">Top 10 kampanii UTM</h2>

    <ol>
        <?php foreach ($topCampaigns as $campaign): ?>
            <li>
                <?= htmlspecialchars((string) $campaign['utm_campaign'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                - <?= htmlspecialchars((string) $campaign['total_tickets'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> biletów
            </li>
        <?php endforeach; ?>
        <?php if ($topCampaigns === []): ?>
            <li>Nie znaleziono kampanii.</li>
        <?php endif; ?>
    </ol>
</section>
