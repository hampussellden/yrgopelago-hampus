<?php

declare(strict_types=1);

// Start the session engines.
session_start();

// Set the default timezone to Coordinated Universal Time.
date_default_timezone_set('UTC');

// Set the default character encoding to UTF-8.
mb_internal_encoding('UTF-8');

// Include the helper functions.
require __DIR__ . '/functions.php';

// Fetch the global configuration array.
$config = require __DIR__ . '/config.php';

// Setup the database connection.
$database = new PDO($config['database_path']);

// Load a celendar
// require('/vendor/benhall14/php-calendar/src/phpCalendar/Calendar.php');
require '/Users/hampussellden/Documents/dev/Projekt/yrgopelago-hampus/vendor/autoload.php';

use benhall14\phpCalendar\Calendar as Calendar;

$calendar = new Calendar();
$calendar->useMondayStartingDate();


# if needed, add event
$events = array();
$events[] = array(
    'start' => '2023-01-12',   # start date in either Y-m-d or Y-m-d H:i if you want to add a time.
    'end' => '2023-01-14',   # end date in either Y-m-d or Y-m-d H:i if you want to add a time.
    'summary' => 'Test booking',  # event name text
    'mask' => true,           # should the date be masked - boolean default true
    'classes' => ['myclass', 'abc']   # (optional) additional classes in either string or array format to be included on the event days
);
$calendar->addEvents($events);
