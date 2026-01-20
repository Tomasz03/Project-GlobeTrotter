<?php
require_once 'db_config.php';
require_once 'classes/Baza.php';
require_once 'classes/User.php';
require_once 'classes/Tour.php';

$db = new Baza(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
$user = new User($db);
$tour_manager = new Tour($db);

$filter_country = filter_input(INPUT_GET, 'country', FILTER_SANITIZE_STRING);
$tours = $tour_manager->getTours($filter_country);
$all_countries = $tour_manager->getUniqueCountries();

include 'includes/header.php';
?>

<div class="content-box">
    <h2>Egzotyczne Podróże i Weekendowe Wypady - Nasza Oferta</h2>
    
    <form action="index.php" method="GET" class="filter-form">
        <label for="country">Filtruj wg kraju:</label>
        <select name="country" id="country">
            <option value="all">Wszystkie Kierunki</option>
            <?php foreach ($all_countries as $country): ?>
                <option value="<?= htmlspecialchars($country) ?>" <?= ($filter_country == $country) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($country) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filtruj</button>
        
    </form>
    
    <?php if (empty($tours)): ?>
        <p>Brak dostępnych wycieczek spełniających kryteria.</p>
    <?php else: ?>
        <div class="tour-grid">
        <?php foreach ($tours as $tour): ?>
            <div class="tour-card">
                <img src="<?= htmlspecialchars($tour['image_url'] ?: 'images/placeholder.jpg') ?>" alt="<?= htmlspecialchars($tour['title']) ?>">
                <div class="card-body">
                    <h3><?= htmlspecialchars($tour['title']) ?></h3>
                    <p><span style="font-style: italic; color: #0779e4;"> <?= htmlspecialchars($tour['country']) ?></span></p>
                    <div class="card-details">
                        <span>Transport: <?= htmlspecialchars($tour['transport_type']) ?></span>
                        <span>Wylot/Wyjazd: <?= htmlspecialchars($tour['start_date']) ?></span>
                        <span>Trwa: <?= htmlspecialchars($tour['duration_days']) ?> dni</span>
                    </div>
                </div>
                <div class="card-footer">
                    <span class="card-price"><?= number_format($tour['price'], 2, ',', ' ') ?> PLN</span>
                    <a href="tour_details.php?id=<?= $tour['id'] ?>"><button>Zobacz i Rezerwuj</button></a>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?> 