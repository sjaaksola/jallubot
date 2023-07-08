<?php

$url = "https://www.alko.fi/INTERSHOP/web/WFS/Alko-OnlineShop-Site/fi_FI/-/EUR/ViewProduct-Include?SKU=000706&AppendStoreList=true&AjaxRequestMarker=true&AjaxRequestMarker=true";
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
var_dump($stock_info);

if (!empty($stock_info)) {
    file_put_contents('stock_info.json', json_encode($stock_info));
    echo "Saldot ladattu onnistuneesti stock_info.json tiedostoon.\n\r";
} else {
    echo "Virhe ladattessa saldoja.\n\r";
    var_dump($page);
}
