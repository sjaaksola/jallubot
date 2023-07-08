<?php

require_once('vendor/autoload.php');
require_once('jb-config.php');

use Abraham\TwitterOAuth\TwitterOAuth;

$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
$user = $connection->get('account/verify_credentials');


$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
$requestToken = $connection->oauth('oauth/request_token', array('oauth_callback' => 'oob', 'x_auth_access_type' => 'write'));
// $connection->setApiVersion('2');

echo json_encode($requestToken);

// die();
$tweet_text = 'Alko Pori Eteläväylä ilmoittaa yhden tähden jallun saldoksi 6-10 pulloa.';

if ($connection->getLastHttpCode() == 200) {
    $status = $connection->getLastHttpCode();
    echo "Yhdistetty (HTTP vastaus ".$status.")\n\r";
    // var_dump($user);
    $message = $connection->post('status/update', ['text' => $tweet_text]);
    echo json_encode($message);
    echo "\n\r";
} else {
    $error = $connection->getLastHttpCode();
    echo "Virhe: Ei yhdistetty (HTTP error ".$error.")\n\r";
    var_dump($error);
}

if ($connection->getLastHttpCode() == 200) {
    echo 'Tweet lähetetty onnistuneesti! ("'.$tweet_text.'")'."\n\r";
} else {
    echo 'Virhe tweetin lähettämisessä: HTTP error ' . $connection->getLastHttpCode();
}
