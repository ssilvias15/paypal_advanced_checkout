<?php
include('access_client_token.php');

$data = '
		{
		"intent": "CAPTURE",

			 "purchase_units": [{
				 "reference_id": "123456789",
				  "description": "Description of Item",
				  "custom_id": "Custom-ID",
				  "soft_descriptor": "MERCHANT NAME",
				  "amount": {
					"currency_code": "USD",
					"value": "211.00",
					"breakdown": {
					  "item_total": {
					  "currency_code": "USD",
					  "value": "165.00"
					},
					"shipping": {
						"currency_code": "USD",
						 "value": "10.00"
					},
					"tax_total": {
					  "currency_code": "USD",
					  "value": "36.00"
					}
				  }
				},
				"items": [{
				  "name": "Item 0",
				  "description": "Description 0",
				  "unit_amount": {
					"currency_code": "USD",
					"value": "55.00"
				  },
				  "tax": {
					"currency_code": "USD",
					"value": "12.00"
				  },
				  "quantity": "1",
				  "category": "PHYSICAL_GOODS"
				},
				{
				  "name": "Item 1",
				  "description": "Description 1",				  
				  "unit_amount": {
					"currency_code": "USD",
					"value": "55.00"
				  },
				  "tax": {
					"currency_code": "USD",
					"value": "12.00"
				  },
				  "quantity": "2",
				  "category": "PHYSICAL_GOODS"
				}
				],
				"shipping": {
					"full_name":"Rossi Bruno",
				 "address":
					{
					"address_line_1": "Dublin",
					"address_line_2": "Dublin",
					"admin_area_2": "Dublin",
					"admin_area_1": "Dublin",
					"postal_code": "D01G509",
					"country_code": "IE"
					}	
				}
			  }],
		
	
			  "application_context": {
				"shipping_preference":"SET_PROVIDED_ADDRESS" 
			
			 
			}
		
		}';


$curl = curl_init();

curl_setopt_array(
	$curl,
	array(
		CURLOPT_URL => "https://api.sandbox.paypal.com/v2/checkout/orders/",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYHOST => false,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_HEADER => false,
		CURLOPT_HTTPHEADER => array(

			"Content-Type: application/json",
			"Authorization: Bearer " . $access_token,
		),
		CURLOPT_POSTFIELDS => $data,
	)
);


$response = curl_exec($curl);
print_r($response);
