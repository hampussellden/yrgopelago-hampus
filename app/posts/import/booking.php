<?php
require '/Users/hampussellden/Documents/dev/Projekt/yrgopelago-hampus/app/autoload.php';

if (isset($_POST['transferCode'], $_POST['guestName'], $_POST['arrival'], $_POST['departure'])) {
    $transferCode = htmlspecialchars(trim($_POST['transferCode']), ENT_QUOTES);
    $guest = htmlspecialchars(trim($_POST['guestName']), ENT_QUOTES);
    $arrival = $_POST['arrival'];
    $departure = $_POST['departure'];
    $room_id = $_POST['roomId'];
    // make a look up if guest name already exists in guests table, if not, create a new one and use that
    //
    //
    $guestId = 1; //only an example for now.

    //Prepare statement to add the new booking to SQLite database
    $stmt = $database->prepare('INSERT INTO bookings (guest_id, start_date, end_date, room_id, transfer_code)
    VALUES (:guest_id, :start_date, :end_date, :room_id, :transfer_code);
    ');
    $stmt->bindParam(':guest_id', $guestId, PDO::PARAM_INT);
    $stmt->bindParam(':start_date', $arrival, PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $departure, PDO::PARAM_STR);
    $stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);
    $stmt->bindParam(':transfer_code', $transferCode, PDO::PARAM_STR);
    $stmt->execute();

    //Possible improvements? Make the $_POST items an array, then filter away the null values, instead of checking every one of them. That might help if we want to have different amounts of features for different rooms
    // if the booking has features chosen, import those to the booking_to_feature table
    $features = [];
    if (!empty($_POST['featureOne'])) {
        $features[] = $_POST['featureOne'];
    }
    if (!empty($_POST['featureTwo'])) {
        $features[] = $_POST['featureTwo'];
    }
    if (!empty($_POST['featureThree'])) {
        $features[] = $_POST['featureThree'];
    }
    //Get the booking id of the booking we just created
    $stmt = $database->prepare('SELECT id FROM bookings where transfer_code=:transfer_code');
    $stmt->bindParam(':transfer_code', $transferCode, PDO::PARAM_STR);
    $stmt->execute();
    $booking_id = $stmt->fetch();
    //loop through the chosen features and add them to the pivot table booking_to_feature
    foreach ($features as $feature) {
        $stmt = $database->prepare('INSERT INTO booking_to_feature (booking_id, feature_id) VALUES (:booking_id, :feature_id);');
        $feature_id = (int)$feature;
        $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
        $stmt->bindParam(':feature_id', $feature_id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
header('location: http://localhost:4000/app/events.php');
