<?php

require_once 'vendor/autoload.php';

$login = 'login';
$password = 'password';
$number = '501234567';
$text = 'It works! Thanks :)';

// Simple
/*
try {
    $sms = new zembrowski\SMS\Orange();
    $sms->login($login, $password);
    $sms->send($number, $text);
} catch (Exception $e) {
    echo '[ERROR] ' . $e->getMessage();
}
*/

// Advanced
try {
    $sms = new zembrowski\SMS\Orange();
    $login = $sms->login($login, $password);
    if ($login['check']) {
        if ($login['free'] > 0) {
        $send = $sms->send($number, $text);
            if ($send['check']) {
                echo "SMS was successfully sent.";
                if (is_int($send['free'])) echo ' This month ' . $send['free'] . ' free SMS left.';
                else if (empty($send['free'])) echo ' This month ' . $sms->get_free() . ' free SMS left.';
                else 'Unknown how many free SMS left this month.';
            } else {
                echo "SMS was not sent.";
            }
        } else {
            echo 'You don\'t have any free SMS left.';
        }
    }
} catch (Exception $e) {
    echo '[ERROR] ' . $e->getMessage();
}
