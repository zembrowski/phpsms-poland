<?php

// Place examples.php in the composer root directory or adjust the path to autoload.php
require_once 'vendor/autoload.php';

$login = 'login';
$password = 'password';
$recipient = '501234567';
$text = 'It works! Thanks :)';

// Simple
/*
try {
    $sms = new zembrowski\SMS\Orange();
    $sms->login($login, $password);
    $sms->send($recipient, $text);
} catch (Exception $e) {
    echo '[ERROR] ' . $e->getMessage();
}
*/

// Advanced
try {

    $sms = new zembrowski\SMS\Orange();
    $login = $sms->login($login, $password);

    if ($login['check']) {

        if ($login['free'] <= 0 && $login['free'] !== false) {

            echo 'You don\'t have any free SMS left this month.';

        } else {

            $send = $sms->send($recipient, $text);

            if ($send['status_code'] == 200) {

                if ($send['check'] && $login['free'] > $send['free'] && is_int($send['free'])) {

                    echo 'SMS was successfully sent. This month ' . $send['free'] . ' free SMS left.';

                } elseif ($login['free'] == $send['free']) {

                    echo 'Count of free SMS has not changed. Was ' . $login['free'] . ' and is ' . $send['free'] . ' . SMS was most probably not sent.';

                } else {

                    echo 'SMS was submitted, but it could not be determined whether it was sucessfully sent/delivered to recipient.';

                }

            } else {

                echo 'It could be not determined whether the SMS was sent, as no successful status code was returned.';

            }

        }

    }

} catch (Exception $e) {

    echo '[ERROR] ' . $e->getMessage();

}
