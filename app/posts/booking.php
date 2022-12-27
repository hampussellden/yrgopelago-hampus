<?php
require '/Users/hampussellden/Documents/dev/Projekt/yrgopelago-hampus/app/autoload.php';

//check if a transfercode is legit and not used
use GuzzleHttp\Client;

$client = new Client();
$transferCode = $_POST['transferCode'];
header('content-type: application/json');
try {
    $response = $client->post('https://www.yrgopelago.se/centralbank/transferCode', [
        'form_params' => [
            'transferCode' => $transferCode,
            'totalcost' => 10
        ]
    ]);
    $response = json_decode($response->getBody()->getContents());
    //Deposit into my account
    if (property_exists($response, 'transferCode')) {
        $validCode = $response->transferCode;
        $response = $client->post('https://www.yrgopelago.se/centralbank/deposit', [
            'form_params' => [
                'user' => 'Hampus',
                'transferCode' => $validCode
            ]
        ]);
        $codeCheck = true;
    }
} catch (Exception $e) {
    echo 'something went wrong with the transfer code check';
}

if (isset($_POST['transferCode'], $_POST['guestName'], $_POST['arrival'], $_POST['departure']) && $codeCheck === true) {

    $guestName = ucfirst(strtolower(htmlspecialchars(trim($_POST['guestName']), ENT_QUOTES)));
    $arrival = $_POST['arrival'];
    $departure = $_POST['departure'];
    $roomId = $_POST['roomId'];

    // make a look up if guest name already exists in guests table, if not, create a new one and use that
    try {
        $stmt = $database->prepare('SELECT id from guests where name=:guest_name');
        $stmt->bindParam(':guest_name', $guestName, PDO::PARAM_STR);
        $stmt->execute();
        $guestIdArr = $stmt->fetch(); //Will return as an array if a name is found, as a boolean if not
        if (is_bool($guestIdArr)) {
            //insert the new guests info into guests table
            $stmt = $database->prepare('INSERT INTO guests (name) VALUES (:guest_name)');
            $stmt->bindParam(':guest_name', $guestName, PDO::PARAM_STR);
            $stmt->execute();
            //pull the newly added guests id into a variable to be used later
            $stmt = $database->prepare('SELECT id FROM guests where name=:guest_name');
            $stmt->bindParam(':guest_name', $guestName, PDO::PARAM_STR);
            $stmt->execute();
            $guestId = $stmt->fetch();
            die(var_dump($guestId));
        } else {

            $guestId = $guestIdArr['id'];
        }
    } catch (Exception $e) {
        echo 'something went wrong with the guest id check';
    }
    //Prepare statement to add the new booking to SQLite database
    $stmt = $database->prepare('INSERT INTO bookings (guest_id, start_date, end_date, room_id, transfer_code)
    VALUES (:guest_id, :start_date, :end_date, :room_id, :transfer_code);
    ');
    $stmt->bindParam(':guest_id', $guestId, PDO::PARAM_INT);
    $stmt->bindParam(':start_date', $arrival, PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $departure, PDO::PARAM_STR);
    $stmt->bindParam(':room_id', $roomId, PDO::PARAM_INT);
    $stmt->bindParam(':transfer_code', $validCode, PDO::PARAM_STR);
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
    if (!empty($features)) {
        //Get the booking id of the booking we just created
        $stmt = $database->prepare('SELECT id FROM bookings where transfer_code=:transfer_code');
        $stmt->bindParam(':transfer_code', $validCode, PDO::PARAM_STR);
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
}
