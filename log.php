<?php

require 'app/autoload.php';
require 'vendor/autoload.php';
require 'views/header.php';

$logbookContent = file_get_contents(__DIR__ . '/logbook.json');
$logbookContentJson = json_decode($logbookContent, true);
$total_cost = 0;
$features = [];
$starRatings = [];

?>

<h2 class="admin">Logbook</h2>

<main class="admin">

    <?php foreach ($logbookContentJson as $log) : ?>

        <?php
        $feat = [];
        //get additional info as string
        if ($log['additional_info'] !== null) {
            $additional_info = (gettype($log['additional_info']) === 'string') ? $log['additional_info'] : implode(' ', $log['additional_info']);
        } else
            $additional_info = 'No additional info';

        $total_cost += $log['total_cost'];
        array_push($starRatings, $log['stars']);

        foreach ($log['features'] as $feature) {
            array_push($feat, $feature['name']);
            array_push($features, $feature['name']);
        }
        ?>

        <div class="admin bookings form-container" style="align-items: flex-start;">
            <p>Island: <?= $log['island'] ?></p>
            <p>hotel: <?= $log['hotel'] ?></p>
            <p>arrival_date: <?= $log['arrival_date'] ?></p>
            <p>departure_date: <?= $log['departure_date'] ?></p>
            <p>stars: <?= $log['stars'] ?></p>
            <p>Features: </p>
            <ul>
                <?= implode('<li>', $feat) . '</li>' ?>
            </ul>

            <p>additional_info: <?= $additional_info ?></p>

        </div>

    <?php endforeach; ?>

</main>

<div class="admin form-container">

    <?php

    $avgStarRating = floor((array_sum($starRatings) / count($starRatings)));
    $val = array_count_values($features);
    arsort($val);
    $mostUsedFeature = array_slice(array_keys($val), 0, 1, true);
    ?>

    <h2>Facts</h2>
    <p>Total cost: <?= $total_cost ?>$</p>
    <p>AVG Star Rating: <?= $avgStarRating ?></p>
    <h3>Used Features</h3>
    <ul style="columns: 4;">
        <?= implode('<li>', $features) . '</li>' ?>
    </ul>
    <h3>Most Used Feature</h3>
    <?= $mostUsedFeature[0] ?>
</div>

<?php
require 'views/footer.php';
?>
