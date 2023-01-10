<?php
require 'app/events.php';


$navbarItems = [
    'budget' => [
        'href' => 'index.php',
        'content' => 'Budget',
    ],
    'standard' => [
        'href' => 'standard.php',
        'content' => 'Standard',
    ],
    'luxury' => [
        'href' => 'luxury.php',
        'content' => 'Luxury',
    ]
];
?>
<nav class="navbar">
    <ul>
        <?php foreach ($navbarItems as $item) : ?>
            <li class="navbar-item">
                <a class="nav-link" href="<?= $item['href'] ?>">
                    <h2>
                        <?= $item['content'] ?>
                    </h2>
                </a>
            </li>
        <?php endforeach ?>
    </ul>
</nav>
