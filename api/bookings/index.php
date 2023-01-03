<?php

declare(strict_types=1);
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
include_once '../../app/autoload.php';
$_POST = $_GET;
if (!empty($_POST)) {
    //Rooms
    $_POST['room'] = strtolower(htmlspecialchars(trim($_POST['room']), ENT_QUOTES));
    switch ($_POST['room']) {
        case 'budget':
            $_POST['roomId'] = 1;
            break;
        case 'standard':
            $_POST['roomId'] = 2;
            break;
        case 'luxury':
            $_POST['roomId'] = 3;
            break;
    }

    //Features
    $_POST['features'] = array_map('intval', $_POST['features']);
    if ($_POST['roomId'] === 2) {
        foreach ($_POST['features'] as $feature) {
            $feature = $feature + 3;
        }
    } else if ($_POST['roomId'] === 3) {
        foreach ($_POST['features'] as $feature) {
            $feature = $feature + 6;
        }
    }
    require '../../app/posts/booking.php';
} else {
    $bookingInfo = file_get_contents('../../app/posts/bookings.json');
    echo $bookingInfo;
}
