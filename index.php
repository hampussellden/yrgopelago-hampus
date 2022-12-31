<?php
require_once 'app/autoload.php';
require 'views/header.php';
require 'vendor/autoload.php';
require 'app/events.php';

use benhall14\phpCalendar\Calendar as Calendar;

$roomId = 1;

$events = getEvents($roomId);
$calendar = new Calendar();
$calendar->useMondayStartingDate();
$calendar->addEvents($events);
//Get the features for this room
$features = getRoomFeatures($roomId, $database);
?>
<?php if (!empty($_SESSION)) : ?>
    <?php foreach ($_SESSION['errors'] as $error) : ?>
        <div class="error">
            <p>
                <?php echo $error; ?>
            </p>
        </div>
    <?php endforeach; ?>
    <?php session_unset(); ?>
<?php endif ?>
<main>
    <div class="calender-container">
        <div class="col-xs-12 col-sm-6 col-md-4">
            <?php echo $calendar->draw(date('2023-1-1'), 'grey'); ?>
            <hr />
        </div>
    </div>
    <aside>
        <?= $currentServer ?>
    </aside>
    <div class="form-container">
        <div class="form-header">
            <h4>Book this room</h4>
            <h4>$<?= getRoomCost($roomId, $database) ?> per day</h4>
        </div>
        <form action="app/posts/booking.php" method="post">
            <div>
                <input type="text" id="transferCode" name="transferCode">
                <label for="transferCode">Transfer Code</label>
            </div>
            <div>
                <input type="text" id="guestName" name="guestName">
                <label for="guestName">Your Name</label>
            </div>
            <div>
                <input type="date" id="arrival" name="arrival" min="2023-01-01" max="2023-01-31">
                <label for="arrival">Arrival</label>
            </div>
            <div>
                <input type="date" id="departure" name="departure" min="2023-01-01" max="2023-01-31">
                <label for="departure">Departure</label>
            </div>
            <?php foreach ($features as $feature) : ?>
                <div>
                    <input class="css-checkbox" type="checkbox" value="<?= $feature['id'] ?>" id="<?= $feature['name'] ?>" name="features[0]">
                    <label for="<?= $feature['name'] ?>"><?= $feature['name'] . ' $' . $feature['cost'] ?></label>
                </div>
            <?php endforeach ?>

            <input type="hidden" value="<?= $roomId ?>" name="roomId">

            <input class="submit-button" type="submit" value="submit">
        </form>
    </div>
</main>
