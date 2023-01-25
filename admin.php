<?php
require 'app/autoload.php';
require 'views/header.php';
require 'vendor/autoload.php';
require 'app/features.php';
require 'app/events.php';
require 'app/rooms.php';
$bookings = json_decode(file_get_contents('app/posts/bookings.json'), true);
?>
<?php if (isset($_SESSION['user'])) : ?>
    <h2 class="admin">Welcome <?= $_SESSION['user']['name'] ?></h2>
    <main class="admin">
        <form class="admin" action="app/posts/update.php" method="post">
            <h3>Update a rooms cost per day</h3>
            <div>
                <input type="number" name="roomId" placeholder="room id" id="roomcostID">
                <label for="roomcostID">Room nr</label>
            </div>
            <div>
                <input type="number" name="roomCost" placeholder="cost per day" id="roomcostCOST">
                <label for="roomcostCOST">Cost</label>
            </div>
            <input class="submit-button" type="submit"></input>
        </form>

        <form class="admin" action="app/posts/update.php" method="post">
            <h3>Update hotels star rating</h3>
            <div>
                <input type="number" name="stars" placeholder="stars" id="starsamount">
                <label for="starsamount">Star amount</label>
            </div>
            <input class="submit-button" type="submit"></input>
        </form>

        <form class="admin" action="app/posts/update.php" method="post">
            <h3>Update Features</h3>
            <div>
                <input type="number" name="roomId" placeholder="room id" id="featuresroomID">
                <label for="featuresroomID">Room nr</label>
            </div>
            <div>
                <input type="number" name="featureId" placeholder="feature id" id="featureID">
                <label for="featureID">Feature ID</label>
            </div>
            <div>
                <input type="text" name="featureName" placeholder="feature name" id="featureName">
                <label for="featureName">Name</label>
            </div>
            <div>
                <input type="number" name="featureCost" placeholder="cost" id="featureCost">
                <label for="featureCost">Cost</label>
            </div>
            <input class="submit-button" type="submit"></input>
        </form>

        <form class="admin" action="app/posts/update.php" method="post">
            <h3>Update staytime-discount</h3>
            <div>
                <input type="number" name="amountOfDays" placeholder="days for discount" id="discountAmountOfDays">
                <label for="discountAmountOfDays">How many days</label>
            </div>
            <div>
                <input type="number" name="discountDaysValue" placeholder="discount Amount" id="discountDaysValue" step="0.01">
                <label for="discountDaysValue">Discount amount</label>
            </div>
            <input class="submit-button" type="submit"></input>
        </form>
    </main>
    <form class="admin bookings" action="app/posts/update.php" method="post">
        <h3>Current bookings management</h3>
        <p>Room ID | Arrival date -> Departure date | Guest ID</p>
        <?php foreach ($bookings as $booking) : ?>
            <div class="booking">
                <p><?= '| ' . $booking['room_id'] . ' | ' . $booking['start_date'] . ' -> ' . $booking['end_date'] . ' | ' . $booking['guest_id'] . ' | ' ?></p>
                <input type="checkbox" name="bookings[]" value="<?= $booking['id'] ?>" id="id<?= $booking['id'] ?>">
                <label for="id<?= $booking['id'] ?>">Remove</label>
            </div>
        <?php endforeach ?>
        <input class="submit-button" type="submit"></input>
    </form>

    <form class="admin bookings" action="log.php" method="post">
        <h3>Check The Log</h3>
        <input class="submit-button" type="submit"></input>
    </form>

<?php else : ?>
    <h2 class="admin">Admin Login</h2>
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
    <form class="admin" action="app/users/login.php" method="post">
        <div>
            <input name="username" type="text" id="username" placeholder="username">
            <label for="username">Username</label>
        </div>
        <div>
            <input name="password" type="password" id="password" placeholder="password">
            <label for="password">Password</label>
        </div>
        <input class="submit-button" type="submit"></input>
    </form>
<?php endif ?>
<?php require 'views/footer.php';
