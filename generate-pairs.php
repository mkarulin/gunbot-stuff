<?php

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2018 Meelis Karulin <mkarulin@icloud.com>
 *
 *  All rights reserved
 *
 *  This is a free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

// Arguments
$volume = $argv['1'] ? (int)$argv['1'] : 500;
$strategy = $argv['2'] ? $argv['2'] : 'tssl';
$length = $argv['3'] ? $argv['3'] : 1;

$url = 'https://www.binance.com/api/v1/ticker/24hr';
$data = json_decode(CallAPI('GET', $url), 1);

echo "Generating pairs for Binance with minimum 24h volume of " . $volume;
$pairs = [];

foreach ($data as $coin) {
    if ($coin['quoteVolume'] > $volume) {
        $market = substr($coin['symbol'], -3);
        if ($market === 'BTC' && !strstr($coin['symbol'], 'BNB')) {
            $pairs[] = substr($coin['symbol'], 0, -3);
        }
    }
}

$count = 0;
$array = [];
sort($pairs, SORT_ASC);
$count = count($pairs);
$index = 0;
$arrayCount = 1;
foreach ($pairs as $pair) {
    if ($index <= intval($count / $length)) {
        $array['pairs' . $arrayCount]['binance']['BTC-' . $pair] = ['strategy' => $strategy, 'override' => []];
        $index++;
    } else {
        $arrayCount++;
        $index = 0;
    }
}
$fp = fopen('pairs.json', 'w');
fwrite($fp, json_encode($array, JSON_PRETTY_PRINT));
fclose($fp);

// Method: POST, PUT, GET etc
// Data: array("param" => "value") ==> index.php?param=value
function CallAPI($method, $url, $data = false)
{
    $curl = curl_init();

    switch ($method) {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Optional Authentication:
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "username:password");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}
