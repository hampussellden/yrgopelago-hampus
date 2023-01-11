<?php

/*
Here's something to start your career as a hotel manager.

One function to connect to the database you want (it will return a PDO object which you then can use.)
    For instance: $db = connect('hotel.db');
                  $db->prepare("SELECT * FROM bookings");

one function to create a guid,
and one function to control if a guid is valid.
*/

function connect(string $dbName): object
{
    $dbPath = __DIR__ . '/' . $dbName;
    $db = "sqlite:$dbPath";

    // Open the database file and catch the exception if it fails.
    try {
        $db = new PDO($db);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Failed to connect to the database";
        throw $e;
    }
    return $db;
}

function guidv4(string $data = null): string
{
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);

    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function isValidUuid(string $uuid): bool
{
    if (!is_string($uuid) || (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) !== 1)) {
        return false;
    }
    return true;
}
function getRoomFeatures(int $roomId, PDO $database): array
{
    $stmt = $database->prepare('SELECT * FROM features where room_id=:room_id');
    $stmt->bindParam(':room_id', $roomId, PDO::PARAM_INT);
    $stmt->execute();
    $features = $stmt->fetchAll();
    return $features;
}
function getEvents(int $roomId)
{
    $data = file_get_contents('app/posts/bookings.json');
    $data = json_decode($data, true);
    $bookings = array_filter($data, function ($var) use ($roomId) {
        return $var['room_id'] === $roomId;
    });
    $events = [];
    foreach ($bookings as $booking) {
        $events[] = [
            'start' => $booking['start_date'],
            'end' => $booking['end_date'],
            'summary' => '',
            'mask' => true
        ];
    }
    return $events;
}
function getRoomCost(int $roomId, PDO $database): string
{
    $stmt = $database->prepare('SELECT cost_per_day FROM rooms where id=:room_id');
    $stmt->bindParam(':room_id', $roomId, PDO::PARAM_INT);
    $stmt->execute();
    $cost = $stmt->fetch();
    return $cost['cost_per_day'];
}
function getChosenFeatures(int $featureId, PDO $database): array
{
    $stmt = $database->prepare('SELECT name, cost FROM features where id=:id');
    $stmt->bindParam(':id', $featureId, PDO::PARAM_INT);
    $stmt->execute();
    $features = $stmt->fetchAll();
    return $features[0];
}

function getFeatureCost(array $chosenFeatures, PDO $database): int
{
    foreach ($chosenFeatures as $feature) {
        $features[] = getChosenFeatures($feature, $database);
    }
    $featureCost = 0;
    foreach ($features as $key => $feature) {
        $featureCost = $featureCost + $feature['cost'];
    }
    return $featureCost;
}
function getDiscounts(PDO $database): array
{
    $stmt = $database->query('SELECT * FROM discounts');
    $discounts = $stmt->fetchAll();
    return $discounts;
}
function getStaytimeString(int|float $value): string
{
    if (is_float($value)) {
        $value = 100 * $value;
        return $value . '%';
    } else {
        return '$' . $value;
    }
}

function getTotalDaysSpent(string $arrival, string $departure): int
{
    $monthStart = 1672531200; //unix for Jan 2023 start
    $unixDay = 86400; //seconds in a day
    //Will give us the exact day of the arrival in the form of an INT
    $arrivalDay = ((strtotime($arrival) - $monthStart) / $unixDay) + 1;
    $departureDay = ((strtotime($departure) - $monthStart) / $unixDay) + 1;
    $totalDaysSpent = ($departureDay - $arrivalDay) + 1;
    return $totalDaysSpent;
}
function getCountDayArray(string $arrival, string $departure): array
{
    $monthStart = 1672531200; //unix for Jan 2023 start
    $unixDay = 86400; //seconds in a day
    $startDay = ((strtotime($arrival) - $monthStart) / $unixDay) + 1;
    $endDay = ((strtotime($departure) - $monthStart) / $unixDay) + 1;
    $chosenDays = [];
    do {
        $chosenDays[] = $startDay;
        $startDay++;
    } while ($startDay <= $endDay);
    return $chosenDays;
}
function setRedirectLocation(int $roomId): string
{
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
    return $redirectLocation;
}
function redirectUser(string $message, string $location)
{
    array_push($_SESSION['errors'], $message);
    header('location: ' . $location);
    exit;
}

function addToBookings(int $guestId, string $arrival, string $departure, int $roomId, string $validCode, PDO $database)
{
    $stmt = $database->prepare('INSERT INTO bookings (guest_id, start_date, end_date, room_id, transfer_code)
    VALUES (:guest_id, :start_date, :end_date, :room_id, :transfer_code);
    ');
    $stmt->bindParam(':guest_id', $guestId, PDO::PARAM_INT);
    $stmt->bindParam(':start_date', $arrival, PDO::PARAM_STR);
    $stmt->bindParam(':end_date', $departure, PDO::PARAM_STR);
    $stmt->bindParam(':room_id', $roomId, PDO::PARAM_INT);
    $stmt->bindParam(':transfer_code', $validCode, PDO::PARAM_STR);
    $stmt->execute();
}
