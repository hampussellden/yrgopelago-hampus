<?php

declare(strict_types=1);
if (!empty($_POST)) {

    //Rooms
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
    header('content-type: application/JSON');
    $bookingInfo = file_get_contents('../../app/posts/bookings.json');
    echo $bookingInfo;
}
