<form
    method="get"
    action=""
    style="display: flex; flex-wrap: wrap; gap: 12px 16px; align-items: end; margin-bottom: 16px;"
>
    <div style="display: flex; flex-direction: column; gap: 4px;">
        <label for="city">Miasto:</label>
        <?php $selectedCityNormalized = mb_strtolower($eventFilters->city(), 'UTF-8'); ?>
        <select id="city" name="city">
            <option value="">Wszystkie miasta</option>
            <?php foreach ($availableCities as $city): ?>
                <?php $isSelected = $selectedCityNormalized === mb_strtolower($city, 'UTF-8'); ?>
                <option
                    value="<?= htmlspecialchars($city, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
                    <?= $isSelected ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($city, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div style="display: flex; flex-direction: column; gap: 4px;">
        <label for="category">Kategoria:</label>
        <select id="category" name="category">
            <option value="">Wszystkie</option>
            <option value="kids" <?= $eventFilters->category() === \App\Enum\EventCategory::KIDS ? 'selected' : '' ?>>dzieci</option>
            <option value="adults" <?= $eventFilters->category() === \App\Enum\EventCategory::ADULTS ? 'selected' : '' ?>>dorosli</option>
        </select>
    </div>
    <div style="display: flex; flex-direction: column; gap: 4px;">
        <label for="date_from">Data od:</label>
        <input
            type="date"
            id="date_from"
            name="date_from"
            value="<?= htmlspecialchars($eventFilters->dateFrom(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
        >
    </div>
    <div style="display: flex; flex-direction: column; gap: 4px;">
        <label for="date_to">Data do:</label>
        <input
            type="date"
            id="date_to"
            name="date_to"
            value="<?= htmlspecialchars($eventFilters->dateTo(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
        >
    </div>
    <div style="display: flex; gap: 8px;">
        <button type="submit">Filtruj</button>
        <a
            href="<?= htmlspecialchars($_SERVER['PHP_SELF'] ?? '/', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
            style="align-self: center;"
        >Wyczysc</a>
    </div>
</form>
