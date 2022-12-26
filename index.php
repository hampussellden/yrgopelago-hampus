<?php
require 'app/autoload.php';
require 'views/header.php';
require 'app/posts/export/budgetEvents.php';
?>
<div class="calender-container">
    <div class="col-xs-12 col-sm-6 col-md-4">
        <?php $calendar->addEvents($events); ?>
        <?php echo $calendar->draw(date('2023-1-1'), 'grey'); ?>

        <hr />

    </div>
</div>

<form action="app/posts/import/booking.php" method="post">

    <input type="text" id="transferCode" name="transferCode">
    <label for="transferCode">Transfer Code</label>

    <input type="text" id="guestName" name="guestName">
    <label for="guestName">Your Name</label>

    <input type="date" id="arrival" name="arrival" min="2023-01-01" max="2023-01-31">
    <label for="arrival">Arrival</label>

    <input type="date" id="departure" name="departure" min="2023-01-01" max="2023-01-31">
    <label for="departure">Departure</label>

    <input type="checkbox" value="1" id="featureOne" name="featureOne">
    <label for="featureOne">Feature #1 $4</label>

    <input type="checkbox" value="2" id="featureTwo" name="featureTwo">
    <label for="featureTwo">Feature #2 $8</label>

    <input type="checkbox" value="3" id="featureThree" name="featureThree">
    <label for="featureThree">Feature #3 $12</label>

    <input type="hidden" value="1" name="roomId">

    <input type="submit" value="submit">
</form>
