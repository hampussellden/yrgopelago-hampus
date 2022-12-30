<?php

declare(strict_types=1);
require 'app/events.php';

function getEvents(int $roomId)
{
    $data = file_get_contents(__DIR__ . '/bookings.json');
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
