<?php

// Place file in composer root directory or adjust the path to autoload.php
require_once 'vendor/autoload.php';

$login = 'login';
$password = 'password';

try {

    $sms = new zembrowski\SMS\Orange();
    $login = $sms->login($login, $password);

    if ($login['remaining']['found'] === true && $login['remaining']['count'] <= 0) {

        echo 'You don\'t have any SMS left this month.';

    } else {

        $array = array(
            '501234567' => 'Un',
            '+48221234567' => 'Deux',
            '+4912345678901' => 'Trois'
        );

        foreach ($array as $recipient => $text) {

            // Last parameter in send() has to be set to (boolean) true
            // Every request has to be sent with a valid token
            $send = $sms->send($recipient, $text, true);

        }

    }

} catch (Exception $e) {

    echo '[ERROR] ' . $e->getMessage();

}
