<?php
require 'app/autoload.php';
require 'views/header.php';
?>
<div class="calender-container">
    <div class="col-xs-12 col-sm-6 col-md-4">

        <?php echo $calendar->draw(date('2023-1-1'), 'grey'); ?>

        <hr />

    </div>
</div>
