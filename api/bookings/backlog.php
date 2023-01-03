<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

$client = new Client();

try {
    $response = $client->post('http://localhost:4000/app/posts/booking.php');
} catch (ClientException $e) {
    echo $e->getMessage();
}

if ($response->getBody()) {
    $bookings = json_decode($response->getBody()->getContents());

    foreach ($bookings->bookings as $booking) {
        echo $booking . '<pr>';
    }
}
