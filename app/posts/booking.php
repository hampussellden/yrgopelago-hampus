<?php

declare(strict_types=1);
require '../../app/autoload.php';
require '../../vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client();
$_SESSION['errors'] = [];
$roomId = (int)$_POST['roomId'];
switch ($roomId) {
    case 1:
        $redirectLocation = '../../index.php';
        break;
    case 2:
        $redirectLocation = '../../standard.php';
        break;
    case 3:
        $redirectLocation = '../../luxury.php';
        break;
}

if (!empty($_POST['transferCode']) && !empty($_POST['name']) && !empty($_POST['arrival']) && !empty($_POST['departure'])) {
    //variables to be used
    $transferCode = $_POST['transferCode'];
    $guestName = ucfirst(strtolower(htmlspecialchars(trim($_POST['name']), ENT_QUOTES)));
    $arrival = $_POST['arrival'];
    $departure = $_POST['departure'];

    $monthStart = 1672531200; //unix for Jan 2023 start
    $unixDay = 86400; //seconds in a day
    $discount = 0;
    //Will give us the exact day of the arrival in the form of an INT
    $arrivalDay = ((strtotime($_POST['arrival']) - $monthStart) / $unixDay) + 1;
    $departureDay = ((strtotime($_POST['departure']) - $monthStart) / $unixDay) + 1;
    $totalDaysSpent = ($departureDay - $arrivalDay) + 1;
    if ($totalDaysSpent < 1) {
        $message = 'Invalid date input';
        array_push($_SESSION['errors'], $message);
        header('location: ' . $redirectLocation);
        exit;
    }
    //Get info about the cosen features
    if (!empty($_POST['features'])) {
        $chosenFeatures = $_POST['features'];
        $chosenFeatures = array_map('intval', $chosenFeatures);
        $featureCost = getFeatureCost($chosenFeatures, $database);
    } else {
        $chosenFeatures = array();
        $featureCost = 0;
    }
    //Count the totalcost that the form should equal to.
    if ($totalDaysSpent >= 4) {
        $discount = $discount + 3;
    }
    // a discount for chosing 2 features
    if (count($chosenFeatures) === 2) {
        $discount = $discount + 2;
    }
    //calculate the cost for the room
    $costPerDay = getRoomCost($roomId, $database);
    // calculate totalCost
    $totalCost = (($totalDaysSpent * $costPerDay) + $featureCost) - $discount;
    //create an array to use when when filling our database with booked dates and to compare against already booked dates
    $countDay = $arrivalDay;
    $chosenDays = [];
    do {
        $chosenDays[] = $countDay;
        $countDay++;
    } while ($countDay <= $departureDay);

    //Check dates so that the dates are available
    $stmt = $database->prepare('SELECT day_of_month FROM booked_days where room_id=:room_id');
    $stmt->bindParam(':room_id', $roomId, PDO::PARAM_INT);
    $rawBookedDays = $stmt->fetchAll();
    foreach ($rawBookedDays as $day) {
        $bookedDays[] = $day['day_of_month'];
    }
    if (empty($bookedDays)) {
        $bookedDays = array();
    }

    //array_intersect looks for matches in the 2 (or more) provided arrays.
    $matchingDates = array_intersect($chosenDays, $bookedDays);
    if (!empty($matchingDates)) {
        $message = 'A date you have chosen is already booked by someone else';
        array_push($_SESSION['errors'], $message);
        header('location: ' . $redirectLocation);
        exit;
    }
    //check if a transfercode is legit and not used
    try {
        $response = $client->post('https://www.yrgopelago.se/centralbank/transferCode', [
            'form_params' => [
                'transferCode' => $transferCode,
                'totalcost' => $totalCost
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
        } else {
            $message = 'The transfercode submited was not valid';
            array_push($_SESSION['errors'], $message);
            header('location: ' . $redirectLocation);
            exit;
        }
    } catch (Exception $e) {
        echo 'something went wrong with the transfer code check';
    }
    if ($codeCheck === true) {
        // make a look up if guest name already exists in guests table, if not, create a new one and use that
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

        //Get the booking id of the booking we just created
        $stmt = $database->prepare('SELECT id FROM bookings where transfer_code=:transfer_code');
        $stmt->bindParam(':transfer_code', $validCode, PDO::PARAM_STR);
        $stmt->execute();
        $response = $stmt->fetch();
        $bookingId = $response['id'];
        // if the booking has features chosen, import those to the booking_to_feature table
        if (!empty($chosenFeatures)) {
            //loop through the chosen features and add them to the pivot table booking_to_feature
            foreach ($chosenFeatures as $feature) {
                $stmt = $database->prepare('INSERT INTO booking_to_feature (booking_id, feature_id) VALUES (:booking_id, :feature_id);');
                $feature_id = (int)$feature;
                $stmt->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
                $stmt->bindParam(':feature_id', $feature_id, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
        $bookingMade = true;
    }
    //fill database with the booked days using the array $chosenDays
    if ($bookingMade === true) {
        foreach ($chosenDays as $chosenDay) {
            $stmt = $database->prepare('INSERT INTO booked_days (booking_id, room_id, day_of_month) VALUES (:booking_id, :room_id, :day_of_month);');
            $stmt->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
            $stmt->bindParam(':room_id', $roomId, PDO::PARAM_INT);
            $stmt->bindParam(':day_of_month', $chosenDay, PDO::PARAM_INT);
            $stmt->execute();
        }
    }
    //Json response on succesful booking
    if ($bookingMade === true) {
        //load dotenv
        $dotenv = Dotenv\Dotenv::createImmutable("/Users/hampussellden/Documents/dev/Projekt/neversummer/");
        $dotenv->load();
        $islandName = $_ENV['ISLAND_NAME'];
        $hotelName = $_ENV['HOTEL_NAME'];
        $stars = $_ENV['STARS'];
        foreach ($chosenFeatures as $feature) {
            $postFeatures[] = getChosenFeatures($feature, $database);
        }
        $bookingInfo = [
            'island' => $islandName,
            'hotel' => $hotelName,
            'arrival_date' => $arrival,
            'departure_date' => $departure,
            'total_cost' => $totalCost,
            'stars' => $stars,
            'features' => [
                $postFeatures
            ],
            'additional_info' => 'You saved $' . $discount . ' with discounts'
        ];
        header('content-type: application/json');
        echo json_encode($bookingInfo);
    }
} else {
    $message = 'Form was not filled in correctly';
    array_push($_SESSION['errors'], $message);
    header('location: ' . $redirectLocation);
    exit;
}
