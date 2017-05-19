<?php

// Place file in composer root directory or adjust the path to autoload.php
require_once 'vendor/autoload.php';

$login = 'login';
$password = 'password';
$recipient = '501234567';
$text = 'It works! Thanks :)';

try {

    $sms = new zembrowski\SMS\Orange();
    $login = $sms->login($login, $password);

    if ($login['remaining']['found'] === true && $login['remaining']['count'] <= 0) {

        echo 'You don\'t have any SMS left this month.';

    } else {

        $send = $sms->send($recipient, $text);

        if ($send['status_code'] == 200) {

            if ($send['remaining']['found'] && $login['remaining']['count'] > $send['remaining']['count']) {

                echo 'SMS was successfully sent. You have ' . $send['remaining']['count'] . ' SMS left this month.';

            } elseif ($login['remaining']['count'] === $send['remaining']['count']) {

                echo 'Count of SMS has not changed. Was ' . $login['remaining']['count'] . ' and now is ' . $send['remaining']['count'] . ' . SMS was most probably not sent.';

            } else {

                echo 'SMS was submitted, but it could not be determined whether it was sucessfully sent/delivered to recipient.';

            }

        } else {

            echo 'It could not be determined whether the SMS was sent, as no successful status code was returned.';

        }

    }

} catch (Exception $e) {

    echo '[ERROR] ' . $e->getMessage();

}
