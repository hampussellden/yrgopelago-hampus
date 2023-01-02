<?php
require 'app/autoload.php';
include 'vendor/autoload.php';
include 'views/header.php';
require 'views/navigation.php';
include 'app/events.php';

use benhall14\phpCalendar\Calendar as Calendar;

$roomId = 2;

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
    <section class="calender-container">
        <div class="col-xs-12 col-sm-6 col-md-4">
            <?php echo $calendar->draw(date('2023-1-1'), 'grey'); ?>
            <hr />
        </div>
    </section>
    <section class="images">

    </section>
    <section class="form-container">
        <div class="form-header">
            <h3>Book this room</h3>
            <h3>$<?= getRoomCost($roomId, $database) ?> per day</h3>
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
                    <input class="css-checkbox" type="checkbox" value="<?= $feature['id'] ?>" id="<?= $feature['name'] ?>" name="features[]">
                    <label for="<?= $feature['name'] ?>"><?= $feature['name'] . ' $' . $feature['cost'] ?></label>
                </div>
            <?php endforeach ?>

            <input type="hidden" value="<?= $roomId ?>" name="roomId">

            <input class="submit-button" type="submit" value="submit">
        </form>
    </section>
</main>
<div class="offers">
    <h4>Holiday offers!</h4>
    <h5>Choose 2 features and save $2</h5>
    <h5>Book 4 or more days and save $3</h5>
</div>
<?php
require 'views/footer.php';
