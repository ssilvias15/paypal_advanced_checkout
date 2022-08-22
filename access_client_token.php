<?php

$curl = curl_init();

//API CREDENTIALS
$clientId = "xxxxxxxxxxxxxxx";
$secret = "xxxxxxxxxxxxxx";

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.sandbox.paypal.com/v1/oauth2/token",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "grant_type=client_credentials",
    CURLOPT_USERPWD => $clientId . ":" . $secret,
    CURLOPT_HEADER => false,
    CURLOPT_HTTPHEADER => array(
        "Accept: application/json",
        "Accept-Language: en_US",

    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "<pre>cURL Error #:" . $err . "</pre>";
} else {
    $response = json_decode($response);
    $access_token = $response->access_token;
}
$curl2 = curl_init();

curl_setopt_array($curl2, array(
    CURLOPT_URL => "https://api.sandbox.paypal.com/v1/identity/generate-token",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_HEADER => false,
    CURLOPT_HTTPHEADER => array(
        "Accept: application/json",
        "Accept-Language: en_US",
        "Authorization: Bearer " . $access_token,
    ),
));

$response = curl_exec($curl2);
$err = curl_error($curl2);



if ($err) {
    echo "<pre>cURL Error #:" . $err . "</pre>";
} else {
    $response = json_decode($response);
    $client_token = $response->client_token;
}
curl_close($curl2);
