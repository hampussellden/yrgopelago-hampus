<?php

//Reload the features.json file with data from database
$stmt = $database->query('SELECT * from rooms');
$data = $stmt->fetchAll();
file_put_contents('app/posts/rooms.json', json_encode($data));
