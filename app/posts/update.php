<?php

declare(strict_types=1);
require '../autoload.php';
require '../../vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client();

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable('../../../neversummer/');
$dotenv->load();

$location = '../../admin.php';

if (isset($_POST['roomId'], $_POST['roomCost'])) {
    $roomId = htmlspecialchars($_POST['roomId']);
    $roomCost = htmlspecialchars($_POST['roomCost']);


    $stmt = $database->prepare('UPDATE rooms SET cost_per_day = :roomCost WHERE id=:roomId');
    $stmt->bindParam(':roomCost', $roomCost, PDO::PARAM_INT);
    $stmt->bindParam(':roomId', $roomId, PDO::PARAM_INT);
    $stmt->execute();

    header('location: ' . $location);
    exit;
}

if (isset($_POST['stars'])) {
    $stars = htmlspecialchars($_POST['stars']);
    $client->post('https://www.yrgopelago.se/centralbank/islands.php', [
        'form_params' => [
            'islandName' => $_ENV['ISLAND_NAME'],
            'url' => $_ENV['URL'],
            'hotelName' => $_ENV['HOTEL_NAME'],
            'stars' => $stars,
            'user' => $_ENV['USER_NAME'],
            'guid' => $_ENV['API_KEY'],
            'submit-island' => 'hej'
        ]
    ]);

    header('location: ' . $location);
    exit;
}
if (isset($_POST['roomId'], $_POST['featureId'], $_POST['featureName'], $_POST['featureCost'])) {
    $roomId = htmlspecialchars($_POST['roomId']);
    $featureId = htmlspecialchars($_POST['featureId']);
    $featureName = htmlspecialchars($_POST['featureName']);
    $featureCost = htmlspecialchars($_POST['featureCost']);

    if ($roomId === 2) {
        $featureId = $featureId + 3;
    } else if ($roomId === 3) {
        $featureId = $featureId + 6;
    }

    $stmt = $database->prepare('UPDATE features SET name = :name , cost = :cost WHERE id=:featureId');

    $stmt->bindParam(':name', $featureName, PDO::PARAM_STR);
    $stmt->bindParam(':cost', $featureCost, PDO::PARAM_INT);
    $stmt->bindParam(':featureId', $featureId, PDO::PARAM_INT);
    $stmt->execute();

    header('location: ' . $location);
    exit;
}

if (isset($_POST['amountOfDays'], $_POST['discountDaysValue'])) {
    $amount = htmlspecialchars($_POST['amountOfDays']);
    $value = htmlspecialchars($_POST['discountDaysValue']);

    $stmt = $database->prepare('UPDATE discounts SET amount = :amount, value = :value WHERE name=staytime');

    $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);
    $stmt->bindParam(':value', $value, PDO::PARAM_INT);
    $stmt->execute();

    header('location: ' . $location);
    exit;
}
