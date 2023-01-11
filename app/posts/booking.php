<?php

declare(strict_types=1);
require '../../app/autoload.php';
require '../../vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client();
$_SESSION['errors'] = [];
$roomId = (int)$_POST['roomId'];
$redirectLocation = setRedirectLocation($roomId);

if (!empty($_POST['transferCode']) && !empty($_POST['name']) && !empty($_POST['arrival']) && !empty($_POST['departure'])) {
    //variables to be used
    $transferCode = $_POST['transferCode'];
    $guestName = ucfirst(strtolower(htmlspecialchars(trim($_POST['name']), ENT_QUOTES)));
    $arrival = $_POST['arrival'];
    $departure = $_POST['departure'];
    //Get discounts from database
    $stmt = $database->query('SELECT * FROM discounts');
    $discounts = $stmt->fetchAll();
    $staytimeDiscount = $discounts[0];
    $featureBonus = $discounts[1];
    $discount = 0;
    $percentageDiscount = 0;
    $discountMultiplier = 1;
    //getTotalDaysSpent
    $totalDaysSpent = getTotalDaysSpent($arrival, $departure);
    if ($totalDaysSpent < 1) {
        $message = 'Invalid date input';
        redirectUser($message, $redirectLocation);
    }
    //Get info about the chosen features
    if (!empty($_POST['features'])) {
        $chosenFeatures = $_POST['features'];
        $chosenFeatures = array_map('intval', $chosenFeatures);
        $featureCost = getFeatureCost($chosenFeatures, $database);
    } else {
        $chosenFeatures = array();
        $featureCost = 0;
    }
    //Count the totalcost that the form should equal to.
    //calculate the cost for the room
    $costPerDay = getRoomCost($roomId, $database);
    //discount for long stays
    if ($totalDaysSpent >= $staytimeDiscount['amount']) {
        if (is_float($staytimeDiscount['value'])) {
            $discountMultiplier = 1 - $staytimeDiscount['value'];
            $percentageDiscount = (($totalDaysSpent * $costPerDay) + $featureCost) * $staytimeDiscount['value'];
            //$percentageDiscount is just for show in the booking response JSON
        } else {
            $discount = $discount + $staytimeDiscount['value'];
        }
    }
    // a discount for chosing an amount of features
    if (count($chosenFeatures) >= $featureBonus['amount']) {
        $discount = $discount + $featureBonus['value'];
    }
    // calculate totalCost
    $totalCost = ((($totalDaysSpent * $costPerDay) + $featureCost) * $discountMultiplier) - $discount;
    //create an array to use when when filling our database with booked dates and to compare against already booked dates
    $chosenDays = getCountDayArray($arrival, $departure);

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
        redirectUser($message, $redirectLocation);
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
            redirectUser($message, $redirectLocation);
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
        addToBookings($guestId, $arrival, $departure, $roomId, $validCode, $database);

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
        $islandName = 'Glacier Island';
        $hotelName = 'Neversummer hotel';
        $stars = 5;
        $postFeatures = [];
        foreach ($chosenFeatures as $feature) {
            $postFeatures[] = getChosenFeatures($feature, $database);
        }

        $bookingInfo = [
            'island' => $islandName,
            'hotel' => $hotelName,
            'arrival_date' => $arrival,
            'departure_date' => $departure,
            'total_cost' => round($totalCost, 2),
            'stars' => $stars,
            'features' => [
                $postFeatures
            ],
            'additional_info' => 'You saved $' . $percentageDiscount + $discount . ' with discounts'
        ];
        header('content-type: application/json');
        echo json_encode($bookingInfo);
    }
} else {
    $message = 'Form was not filled in correctly';
    redirectUser($message, $redirectLocation);
}
