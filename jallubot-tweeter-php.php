<?php
require_once('vendor/autoload.php');

use Abraham\TwitterOAuth\TwitterOAuth;

// Ladataan Alkon sivu tietylle tuotteelle
$url = "https://www.alko.fi/INTERSHOP/web/WFS/Alko-OnlineShop-Site/fi_FI/-/EUR/ViewProduct-Include?SKU=000706";
$page = file_get_contents($url);

// Parsitaan sivun HTML DOMDocument-luokan avulla
$dom = new DOMDocument();
@$dom->loadHTML($page);

// Etsitään kaikki myymälät, jotka myyvät tuotetta ja niiden saatavuustiedot
$store_items = $dom->getElementsByTagName('li');

// Tallennetaan saatavuustiedot taulukkoon
$stock_info = array();
foreach ($store_items as $store) {
    $store_name = $store->getElementsByTagName('span')->item(0);
    $stock = $store->getElementsByTagName('span')->item(1);

    // Tarkistetaan, että elementit ovat olemassa ennen niiden arvon hakemista
    if ($store_name && $stock) {
        $store_name_value = $store_name->nodeValue;
        $stock_value = $stock->nodeValue;
        $stock_info[$store_name_value] = $stock_value;
    }
}
if (!empty($store_items)) {
    // Valitaan satunnainen myymälä ja saatavuustieto
    $store_name = array_rand($stock_info);
    $stock = $stock_info[$store_name];

    // Yhdistetään Twitter API:n käyttäjätunnukseen
    $consumer_key = 'buvoCKcCQdnywmdLjqUNV68Mt';
    $consumer_secret = 'VbESbbINeWc2hXmaJce36S7WP88tB4M6OwtTbKk2e0fmrMgxbe';
    $bearer_token = 'AAAAAAAAAAAAAAAAAAAAAPQUmgEAAAAAZk7r2H%2F8848LxeWBGSbmW5Q%2FKsU%3D72OzlHuAUcKYZ4EyVzv4wlr2uxjvWny7OrLRS3LECGekO5Gy2Y';
    $access_token = '1643559237828636672-rsEaINACdjNaPTpCmHSVzJlNCnli5j';
    $access_token_secret = 'xf4KCUlUYHZ22qfhdOcBKXKSh7JukEi7aqhYr0pDW5D1a';
    $connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);

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
        $connection->post('statuses/update', ['status' => $tweet_text]);
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
