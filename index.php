<?php
require 'app/autoload.php';
require 'views/header.php';
// Load a celendar
require '/Users/hampussellden/Documents/dev/Projekt/yrgopelago-hampus/vendor/autoload.php';

use benhall14\phpCalendar\Calendar as Calendar;

$calendar = new Calendar();
$calendar->useMondayStartingDate();
require 'app/posts/budgetEvents.php';
$calendar->addEvents($events);
$roomId = 1;
//Get the features for this room
$features = getRoomFeatures($roomId);
?>
<div class="calender-container">
    <div class="col-xs-12 col-sm-6 col-md-4">
        <?php echo $calendar->draw(date('2023-1-1'), 'grey'); ?>
        <hr />

    </div>
</div>

<form action="app/posts/booking.php" method="post">

    <input type="text" id="transferCode" name="transferCode">
    <label for="transferCode">Transfer Code</label>

    <input type="text" id="guestName" name="guestName">
    <label for="guestName">Your Name</label>

    <input type="date" id="arrival" name="arrival" min="2023-01-01" max="2023-01-31">
    <label for="arrival">Arrival</label>

    <input type="date" id="departure" name="departure" min="2023-01-01" max="2023-01-31">
    <label for="departure">Departure</label>

    <?php foreach ($features as $feature) : ?>
        <input type="checkbox" value="<?= $feature['id'] ?>" id="<?= $feature['name'] ?>" name="features[0]">
        <label for="<?= $feature['name'] ?>"><?= $feature['name'] . ' $' . $feature['cost'] ?></label>
    <?php endforeach ?>

    <input type="hidden" value="1" name="roomId">

    <input type="submit" value="submit">
</form>
