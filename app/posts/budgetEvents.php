<?php

declare(strict_types=1);

$data = file_get_contents('/Users/hampussellden/Documents/dev/Projekt/yrgopelago-hampus/app/posts/JSON/bookings.json');
$data = json_decode($data, true);
$roomFilter = 1; //What room we sort for in this file
$bookings = array_filter($data, function ($var) use ($roomFilter) {
    return $var['room_id'] === $roomFilter;
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
