<?php

include('access_client_token.php');

$orderid = $_GET['id'];
$curl = curl_init();

curl_setopt_array(
    $curl,
    array(
        CURLOPT_URL => "https://api.sandbox.paypal.com/v2/checkout/orders/" . $orderid . "/capture",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_HEADER => false,
        CURLOPT_HTTPHEADER => array(

            "Content-Type: application/json",
            "Authorization: Bearer " . $access_token,
        ),
    )
);

$response = curl_exec($curl);
print_r($response);
