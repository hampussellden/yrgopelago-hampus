<?php

require 'app/autoload.php';
require 'vendor/autoload.php';
require 'views/header.php';

$logbookContent = file_get_contents(__DIR__ . '/logbook.json');
$logbookContentJson = json_decode($logbookContent, true);
?>

<main class="admin">

    <?php foreach ($logbookContentJson as $log) : ?>

        <div class="admin bookings form-container">
            <p>Island: <?= $log['island'] ?></p>
            <p>hotel: <?= $log['hotel'] ?></p>
            <p>arrival_date: <?= $log['arrival_date'] ?></p>
            <p>departure_date: <?= $log['departure_date'] ?></p>
            <p>stars: <?= $log['stars'] ?>

        </div>

    <?php endforeach; ?>
</main>


<?php
require 'views/footer.php';
?>
