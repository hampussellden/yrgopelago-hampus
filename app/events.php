<?php

//Reload the bookings.json file wiht data from database
$stmt = $database->query('SELECT * from bookings');
$data = $stmt->fetchAll();
file_put_contents('app/posts/bookings.json', json_encode($data));
