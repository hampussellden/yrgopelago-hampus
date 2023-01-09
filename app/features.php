<?php

//Reload the features.json file with data from database
$stmt = $database->query('SELECT * from features');
$data = $stmt->fetchAll();
file_put_contents('app/posts/features.json', json_encode($data));
