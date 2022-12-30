<?php
require '/Users/hampussellden/Documents/dev/Projekt/yrgopelago-hampus/app/autoload.php';
require '/Users/hampussellden/Documents/dev/Projekt/yrgopelago-hampus/vendor/autoload.php';
$errors = array_diff($errors, $errors);

use GuzzleHttp\Client;

$client = new Client();

if (isset($_POST['transferCode'], $_POST['guestName'], $_POST['arrival'], $_POST['departure'])) {
    $chosenFeatures = $_POST['features'];
    $roomId = $_POST['roomId'];
    $monthStart = 1672531200; //unix for Jan 2023 start
    $unixDay = 86400; //seconds in a day
    $arrivalDay = ((strtotime($_POST['arrival']) - $monthStart) / $unixDay) + 1;
    $departureDay = ((strtotime($_POST['departure']) - $monthStart) / $unixDay) + 1;
    $discount = 0;
    $transferCode = $_POST['transferCode'];
    $guestName = ucfirst(strtolower(htmlspecialchars(trim($_POST['guestName']), ENT_QUOTES)));
    $arrival = $_POST['arrival'];
    $departure = $_POST['departure'];


    //Count the totalcost that the form should equal to.
    //Will give us the exact day of the arrival in the form of an INT
    $totalDaysSpent = ($departureDay - $arrivalDay) + 1;
    if ($totalDaysSpent === 4) {
        $discount = $discount + 3;
    }
    // a discount for chosing 2 features
    if (count($chosenFeatures) === 2) {
        $discount = $discount + 2;
    }
    //calculate the cost for the room
    switch ($roomId) {
        case 1:
            $costPerDay = 2;
            break;
        case 2:
            $costPerDay = 4;
            break;
        case 3:
            $costPerDay = 8;
            break;
    }
    //Will make an array that takes the feature cost from the database
    $featureCost = 2;
    $totalCost = (($totalDaysSpent * $costPerDay) + $featureCost) - $discount;


    //create an array to use when when filling our database with booked dates and to compare against already booked dates
    $countDay = $arrivalDay;
    $chosenDays = [];
    do {
        $chosenDays[] = $countDay;
        $countDay++;
    } while ($countDay <= $departureDay);

    //Check dates so that the dates are available
    $stmt = $database->query('SELECT day_of_month FROM booked_days');
    $rawBookedDays = $stmt->fetchAll();
    foreach ($rawBookedDays as $day) {
        $bookedDays[] = $day['day_of_month'];
    }

    //array_intersect looks for matches in the 2 (or more) provided arrays.
    $matchingDates = array_intersect($chosenDays, $bookedDays);
    if (!empty($matchingDates)) {
        $errors[] = 'A date you have chosen is already booked by someone else';
        header('location: http://localhost:4000/');
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
            $errors[] = 'The transfercode submited was not valid';
            header('location: http://localhost:4000/');
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

        // if the booking has features chosen, import those to the booking_to_feature table
        if (!empty($chosenFeatures)) {
            //Get the booking id of the booking we just created
            $stmt = $database->prepare('SELECT id FROM bookings where transfer_code=:transfer_code');
            $stmt->bindParam(':transfer_code', $validCode, PDO::PARAM_STR);
            $stmt->execute();
            $bookingIdResponse = $stmt->fetch();
            $bookingId = $bookingIdResponse['id'];
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
            $stmt = $database->prepare('INSERT INTO booked_days (booking_id, day_of_month) VALUES (:booking_id, :day_of_month);');
            $stmt->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
            $stmt->bindParam(':day_of_month', $chosenDay, PDO::PARAM_INT);
            $stmt->execute();
        }
    }
    //Json response on succesful booking
    if ($bookingMade === true) {
        //load dotenv
        $dotenv = Dotenv\Dotenv::createImmutable("/Users/hampussellden/Documents/dev/Projekt/yrgopelago-hampus/");
        $dotenv->load();
        $islandName = $_ENV['ISLAND_NAME'];
        $hotelName = $_ENV['HOTEL_NAME'];
        $stars = $_ENV['STARS'];
        $bookingInfo = [
            'island' => $islandName,
            'hotel' => $hotelName,
            'arrival_date' => $arrival,
            'departure_date' => $departure,
            'total_cost' => $totalCost,
            'stars' => $stars,
            'features' => 'featuresarray', //Make an array with features and there costs here
            'additional_info' => '' //maybe include discounts here if there are any
        ];
        header('content-type: application/json');
        echo json_encode($bookingInfo);
    }
} else {
    $errors[] = 'Form was not filled in correctly';
    header('location: http://localhost:4000/');
    exit;
}
