<?php
require '/Users/hampussellden/Documents/dev/Projekt/yrgopelago-hampus/app/autoload.php';

//Count the totalcost that the form should equal to.
$features = $_POST['features'];
$roomId = $_POST['roomId'];
if (isset($_POST['arrival'], $_POST['departure'])) {
    $monthStart = 1672531200; //unix for Jan 2023 start
    $monthEnd = 1675209599; //uxin for Jan 2023 end
    $unixDay = 86400; //seconds in a day
    //Will give us the exact day of the arrival in the form of an INT
    $arrivalDay = ((strtotime($_POST['arrival']) - $monthStart) / $unixDay) + 1;
    $departureDay = ((strtotime($_POST['departure']) - $monthStart) / $unixDay) + 1;
    $totalNightsStayed = $departureDay - $arrivalDay;
    $discount = 0;
    if ($totalNightsStayed === 0) {
        $totalNightsStayed = 1; //Day visit will cost the same as 1 night
    } else if ($totalNightsStayed = 4) {
        $discount = $discount + 3;
    }
    // a discount for chosing 2 features
    if (count($features) === 2) {
        $discount = $discount + 2;
    }
    //calculate the cost for the room
    switch ($roomId) {
        case 1:
            $costPerNight = 2;
            break;
        case 2:
            $costPerNight = 4;
            break;
        case 3:
            $costPerNight = 8;
            break;
    }
    $featureCost = 0; //Will make a
    $totalCost = (($totalNightsStayed * $costPerNight) + $featureCost) - $discount;
}

//check if a transfercode is legit and not used
use GuzzleHttp\Client;

$client = new Client();
$transferCode = $_POST['transferCode'];
header('content-type: application/json');
try {
    $response = $client->post('https://www.yrgopelago.se/centralbank/transferCode', [
        'form_params' => [
            'transferCode' => $transferCode,
            'totalcost' => $totalCost
        ]
    ]);
    $response = json_decode($response->getBody()->getContents());
    die(var_dump($response));
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
            $guestIdResponse = $stmt->fetch();
            $guestId = $guestIdResponse['id'];
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
    //We can get the features as an array directly from the form with some clever HTML. Then we can loop through it and non checked features wont show up at all.
    // if the booking has features chosen, import those to the booking_to_feature table
    if (!empty($features)) {
        //Get the booking id of the booking we just created
        $stmt = $database->prepare('SELECT id FROM bookings where transfer_code=:transfer_code');
        $stmt->bindParam(':transfer_code', $validCode, PDO::PARAM_STR);
        $stmt->execute();
        $bookingIdResponse = $stmt->fetch();
        $bookingId = $bookingIdResponse['id'];
        //loop through the chosen features and add them to the pivot table booking_to_feature
        foreach ($features as $feature) {
            $stmt = $database->prepare('INSERT INTO booking_to_feature (booking_id, feature_id) VALUES (:booking_id, :feature_id);');
            $feature_id = (int)$feature;
            $stmt->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
            $stmt->bindParam(':feature_id', $feature_id, PDO::PARAM_INT);
            $stmt->execute();
        }
    }
    $bookingMade = true;
}
if ($bookingMade === true) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $islandName = $_ENV['ISLAND_NAME'];
    $hotelName = $_ENV['HOTEL_NAME'];
    $stars = $_ENV['STARS'];
    header('content-type: application/json');
    $bookingInfo = [
        'island' => $islandName,
        'hotel' => $hotelName,
        'arrival_date' => $arrival,
        'departure_date' => $departure,
        'total_cost' => $cost,
        'stars' => $stars,
        'features' => [$features],
        'additional_info' => ''
    ];
    echo json_encode($bookingInfo);
}
