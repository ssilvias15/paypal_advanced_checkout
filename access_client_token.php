<?php

$curl = curl_init();

//API CREDENTIALS
$clientId = "ARSqHC295J546ZD6NANrJMi7ZS7YCJhQaQc1qDBwwKY1Aqxkm0YuiSxvBGHzZH4iWMPNynrAu0SeKw-o";
//$clientId = "AVBHmsgNyCn_dsjYcFoQr5eFY8a1MChnCDtkf3xSBg4sbKo5yyHRhp5YeQJWAuoL6nuuzOwqzuWFg8t7";
$secret = "ELenJV_oi9daE2HEZc3HgjO5grCa3DfiUxYuZNg7OSHbOKFU0uncjckPVxzCEThTB-sKqrAgeNbTm86E";
//$secret = "EAtLQSnY9VvB1NQJlXByJLtQqNvPAmPd7ysnK2NxcnNsSylMhnE0K7ooXEQxgewHYaR98YLM-ICDpVfG";

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
