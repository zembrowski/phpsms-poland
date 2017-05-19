<?php

// Place file in composer root directory or adjust the path to autoload.php
require_once 'vendor/autoload.php';

$login = 'login';
$password = 'password';
$recipient = '501234567';
$text = 'It works! Thanks :)';

try {
    $sms = new zembrowski\SMS\Orange();
    $sms->login($login, $password);
    $sms->send($recipient, $text);
} catch (Exception $e) {
    echo '[ERROR] ' . $e->getMessage();
}
