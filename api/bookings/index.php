<?php

declare(strict_types=1);
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
include_once '../../app/autoload.php';

//POST requests handling
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
}

//GET requests handling
$data = json_decode(file_get_contents('../../app/posts/bookings.json'), TRUE);
$bookings = $data;
if (isset($_GET['id'])) {
    $id = (int)htmlspecialchars($_GET['id'], ENT_QUOTES);
    $filtered = array_filter($bookings, function ($bookings) use ($id) {
        return $bookings['id'] === $id;
    });
    echo json_encode($filtered);
} else if (isset($_GET['guest_id'])) {
    $guestId = (int)htmlspecialchars($_GET['guest_id'], ENT_QUOTES);
    $filtered = array_filter($bookings, function ($bookings) use ($guestId) {
        return $bookings['guest_id'] === $guestId;
    });
    echo json_encode($filtered);
} else if (isset($_GET['start_date'])) {
    $arrival = htmlspecialchars($_GET['start_date'], ENT_QUOTES);
    $filtered = array_filter($bookings, function ($bookings) use ($arrival) {
        return $bookings['start_date'] === $arrival;
    });
    echo json_encode($filtered);
} else if (isset($_GET['end_date'])) {
    $departure = htmlspecialchars($_GET['end_date'], ENT_QUOTES);
    $filtered = array_filter($bookings, function ($bookings) use ($departure) {
        return $bookings['end_date'] == $departure;
    });
    echo json_encode($filtered);
} else if (isset($_GET['room_id'])) {
    $roomId = (int)htmlspecialchars($_GET['room_id'], ENT_QUOTES);
    $filtered = array_filter($bookings, function ($bookings) use ($roomId) {
        return $bookings['room_id'] === $roomId;
    });
    echo json_encode($filtered);
} else if (isset($_GET['transfer_code'])) {
    $code = htmlspecialchars($_GET['transfer_code'], ENT_QUOTES);
    $filtered = array_filter($bookings, function ($bookings) use ($code) {
        return $bookings['transfer_code'] == $code;
    });
    echo json_encode($filtered);
} else {
    $bookingInfo = file_get_contents('../../app/posts/bookings.json');
    echo $bookingInfo;
}
