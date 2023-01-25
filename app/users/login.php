<?php

declare(strict_types=1);

require '../autoload.php';
require '../../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

$redirectLocation = __DIR__ . '/admin.php';
//while testing use below location instead
// $redirectLocation = 'http://localhost:4000/admin.php';
$_SESSION['errors'] = [];
// In this file we login users.
if (isset($_POST['username'], $_POST['password'])) {
    $username = htmlspecialchars($_POST['username'], ENT_QUOTES);
    $password = htmlspecialchars($_POST['password'], ENT_QUOTES);

    if ($_ENV['USER_NAME'] !== $username) {
        $message = 'Form was not filled in correctly';
        array_push($_SESSION['errors'], $message);
        header('location: ' . $redirectLocation);
        exit;
    } else {
        if ($_ENV['API_KEY'] !== $password) {
            $message = 'Form was not filled in correctly';
            array_push($_SESSION['errors'], $message);
            header('location: ' . $redirectLocation);
            exit;
        } else {
            $_SESSION['user'] = [
                'name' => $username
            ];
            header('location: ' . $redirectLocation);
            exit;
        }
    }
}
