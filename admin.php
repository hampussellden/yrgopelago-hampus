<?php
require 'views/header.php';
require 'app/autoload.php';
require 'vendor/autoload.php';
require 'app/features.php';
require 'app/rooms.php';
?>

<h2>4dm1n L0g1n</h2>
<section class="form-container">

    <form action="admin.php" method="post">
        <div>
            <input type="text" id="username" placeholder="Username">
            <label for="username">Username</label>
        </div>
        <div>
            <input type="password" id="password" placeholder="password">
            <label for="password">Username</label>
        </div>
        <button class="submit-button" type="submit">Login</button>
    </form>
</section>

<?
require 'views/footer.php';
