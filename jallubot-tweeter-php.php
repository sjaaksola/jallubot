<?php
require_once('vendor/autoload.php');
require_once('jb-config.php');

use Abraham\TwitterOAuth\TwitterOAuth;

$json_data = file_get_contents('stock_info.json');
$store_items = json_decode($json_data, true);
// var_dump($store_items);

if (!empty($store_items)) {
    // Valitaan satunnainen myymälä ja saatavuustieto
    
    $store_name = array_rand($store_items);
    // var_dump($store_name);
    $stock = $store_items[$store_name];

    // Yhdistetään Twitter API:n käyttäjätunnukseen
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);

    // Make a request to the "account/verify_credentials" endpoint
    $user = $connection->get('account/verify_credentials');

    if ($connection->getLastHttpCode() == 200) {
        // var_dump($user);
    } else {
        $error = $connection->getLastHttpCode();
        var_dump($error);
    }

    // Tweetin teksti esim. "Alko Varkaus ilmoittaa yhden tähden jallun saldoksi 16-20 pulloa."
    $tweet_text = $store_name . ' ilmoittaa yhden tähden jallun saldoksi ' . $stock . ' pulloa.';

    // Lähetetään tweetti
    try {
        // Lähetetään tweetti
        $message = $connection->post('tweets', ['text' => $tweet_text]);
        echo json_encode($message);
        echo "\n\r";
        echo 'Tweet lähetetty onnistuneesti! ("'.$tweet_text.'")'."\n\r";
    } catch (Exception $e) {
        echo 'Virhe tweetin lähettämisessä: ' . $e->getMessage();
    }

    // Tarkistetaan, onnistuiko tweetin lähetys vai ei
    if ($connection->getLastHttpCode() == 200) {
        // Onnistui
        echo 'Tweet lähetetty onnistuneesti! ("'.$tweet_text.'")';
    } else {
        // Ei onnistunut
        echo 'Virhe tweetin lähettämisessä: ' . $connection->getLastHttpCode();
    }
} else {
    // Tässä voit käsitellä tilannetta, jossa tuotteita ei löytynyt
    echo 'Tuotteita ei löytynyt.';
}
?>
