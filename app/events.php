<?php

require 'autoload.php';

//Reload the bookings.json file wiht data from database
$stmt = $database->query('SELECT * from bookings');
$data = $stmt->fetchAll();
file_put_contents('posts/bookings.json', json_encode($data));
header('location:http://localhost:4000');
// header('location:http://bosse.ai/yrgopelago-hampus');
