<?php

require 'app/autoload.php';
require 'vendor/autoload.php';
require 'views/header.php';

$logbookContent = file_get_contents(__DIR__ . '/logbook.json');
$logbookContentJson = json_decode($logbookContent, true);
$total_cost = 0;
$features = [];
$starRatings = [];

function printFeaturesAsUL($target, $withColums = false)
{
    if (isset($target[0]['name'])) {
        echo ($withColums === true) ? '<ul column-count="4">' : '<ul>';
        foreach ($target as $key) {
            echo '<li>' . $key['name'] . '</li>';
        }
        echo '</ul>';
    }
}

?>

<h2 class="admin">Logbook</h2>

<main class="admin">

    <?php foreach ($logbookContentJson as $log) : ?>

        <?php
        //get additional info as string
        if ($log['additional_info'] !== null) {
            $additional_info = (gettype($log['additional_info']) === 'string') ? $log['additional_info'] : implode(' ', $log['additional_info']);
        } else
            $additional_info = 'No additional info';

        $total_cost += $log['total_cost'];
        array_push($starRatings, $log['stars']);

        foreach ($log['features'] as $feature) {
            array_push($features, $feature);
        }
        ?>

        <div class="admin bookings form-container" style="align-items: flex-start;">
            <p>Island: <?= $log['island'] ?></p>
            <p>hotel: <?= $log['hotel'] ?></p>
            <p>arrival_date: <?= $log['arrival_date'] ?></p>
            <p>departure_date: <?= $log['departure_date'] ?></p>
            <p>stars: <?= $log['stars'] ?></p>
            <p>Features: </p>
            <?php printFeaturesAsUL($log['features']); ?>

            <p>additional_info: <?= $additional_info ?></p>

        </div>

    <?php endforeach; ?>

</main>

<div class="admin form-container">

    <?php

    $featureNames = [];
    foreach ($features as $feature) {
        array_push($featureNames, $feature['name']);
    }

    $avgStarRating = floor((array_sum($starRatings) / count($starRatings)));
    $val = array_count_values($featureNames);
    arsort($val);
    $mostUsedFeature = array_slice(array_keys($val), 0, 1, true);
    ?>

    <h2>Facts</h2>
    <p><b>Total cost:</b> <?= $total_cost ?>$</p>
    <p><b>AVG Star Rating:</b> <?= $avgStarRating ?></p>
    <h3>Used Features</h3>
    <?php printFeaturesAsUL($features, true) ?>
    <h3>Most Used Feature</h3>
    <?= $mostUsedFeature[0] ?>
</div>

<?php
require 'views/footer.php';
?>
