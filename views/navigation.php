<?php
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
$currentServer = $_SERVER['SCRIPT_NAME'];
function isActive(string $currentServer, array $item): string
{
    if ($currentServer == $item['href']) {
        return 'active';
    } else {
        return 'not-active';
    }
}
?>
<nav class="navbar">
    <ul>
        <?= $currentServer ?>
        <?php foreach ($navbarItems as $item) : ?>
            <li class="navbar-nav <?= isActive($currentServer, $item) ?>">
                <a class="nav-link" href="<?= $item['href'] ?>">
                    <h2>
                        <?= $item['content'] ?>
                    </h2>
                </a>
            </li>
        <?php endforeach ?>
    </ul>
</nav>
