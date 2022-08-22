<?php
include('access_client_token.php');
?>
<!DOCTYPE html>
<html>
<style>
    input[type=text],
    select {
        width: 50%;
        padding: 12px 20px;
        margin: 8px 0;
        display: inline-block;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    #submit {
        width: 100%;
        background-color: #4CAF50;
        color: white;
        padding: 14px 20px;
        margin: 8px 0;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    #submit:hover {
        background-color: #45a049;
    }

    .card_container {
        border-radius: 5px;
        background-color: #f2f2f2;
        padding: 20px;
    }
</style>

<head>
    <meta charset="utf-8" />
    <!-- Optimal rendering on mobile devices. -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Sample CSS styles for demo purposes. You can override these styles to match your web page's branding. -->
    <link rel="stylesheet" type="text/css" href="https://www.paypalobjects.com/webstatic/en_US/developer/docs/css/cardfields.css" />
</head>

<body>
    <!-- JavaScript SDK -->
    <script src="https://www.paypal.com/sdk/js?components=buttons,hosted-fields&client-id=ARSqHC295J546ZD6NANrJMi7ZS7YCJhQaQc1qDBwwKY1Aqxkm0YuiSxvBGHzZH4iWMPNynrAu0SeKw-o&enable-funding=paylater" data-client-token="<?php echo $client_token; ?>">
    </script>

    <!-- Buttons container -->
    <table border="0" align="center" valign="top" bgcolor="#FFFFFF" style="width: 39%">
        <tr>
            <td colspan="2">
                <div id="paypal-button-container"></div>
            </td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
    </table>

    <div class="card_container">
        <form id="card-form">
            <label for="card-number">Card Number</label>
            <div id="card-number" class="card_field"></div>
            <div>
                <label for="expiration-date">Expiration Date</label>
                <div id="expiration-date" class="card_field"></div>
            </div>
            <div>
                <label for="cvv">CVV</label>
                <div id="cvv" class="card_field"></div>
            </div>
            <label for="card-holder-name">Name on Card</label>
            <input type="text" id="card-holder-name" name="card-holder-name" autocomplete="off" placeholder="card holder name" />
            <div>
                <label for="card-billing-address-street">Billing Address</label>
                <input type="text" id="card-billing-address-street" name="card-billing-address-street" autocomplete="off" placeholder="street address" />
            </div>
            <div>
                <label for="card-billing-address-unit">&nbsp;</label>
                <input type="text" id="card-billing-address-unit" name="card-billing-address-unit" autocomplete="off" placeholder="unit" />
            </div>
            <div>
                <input type="text" id="card-billing-address-city" name="card-billing-address-city" autocomplete="off" placeholder="city" />
            </div>
            <div>
                <input type="text" id="card-billing-address-state" name="card-billing-address-state" autocomplete="off" placeholder="state" />
            </div>
            <div>
                <input type="text" id="card-billing-address-zip" name="card-billing-address-zip" autocomplete="off" placeholder="zip / postal code" />
            </div>
            <div>
                <input type="text" id="card-billing-address-country" name="card-billing-address-country" autocomplete="off" placeholder="country code" />
            </div>
            <br /><br />
            <button value="submit" id="submit" class="btn">Pay</button>
        </form>
    </div>

    <script>
        // This is handling the button
        paypal.Buttons({
            createOrder: function(data, actions) {
                console.log('The button has been clicked, creating order');
                return fetch("create_order.php").then(function(res) {
                    return res.json();
                }).then(function(orderData) {
                    console.log("This is the order created");
                    console.log(orderData);
                    console.log("Order ID: ", orderData.id);
                    return orderData.id;
                });
            },
            onApprove: function(data, actions) {
                console.log("Capture payment data: ", data);
                return fetch('capture_payment.php?id=' + data.orderID)
                    .then(function(res) {
                        return res.json();
                    }).then(function(orderData) {
                        console.log("Capture Payment response");
                        console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
                        const trans = orderData.purchase_units[0].payments.captures[0];
                        // Show message to the buyer
                        alert(`Transaction id ${trans.id}, status ${trans.status}`);
                        return orderData.id;
                    });
            }
        }).render('#paypal-button-container');

        // If this returns false or the card fields aren't visible, see Step #1.
        if (paypal.HostedFields.isEligible()) {
            console.log('Hosted Fields eligibility: ', paypal.HostedFields.isEligible());
            let orderId;
            // Renders card fields
            paypal.HostedFields.render({
                // Call your server to set up the transaction
                createOrder: function(data, actions) {
                    return fetch("create_order.php")
                        .then((res) => res.json())
                        .then((orderData) => {
                            console.log('This is the order created');
                            console.log('orderData');
                            orderId = orderData.id; // needed later to complete capture
                            console.log('orderID: ', orderData);
                            return orderData.id
                        })
                },
                styles: {
                    '.valid': {
                        color: 'green'
                    },
                    '.invalid': {
                        color: 'red'
                    }
                },
                fields: {
                    number: {
                        selector: "#card-number",
                        placeholder: "4111 1111 1111 1111"
                    },
                    cvv: {
                        selector: "#cvv",
                        placeholder: "123"
                    },
                    expirationDate: {
                        selector: "#expiration-date",
                        placeholder: "MM/YY"
                    }
                }
            }).then((cardFields) => {
                document.querySelector("#card-form").addEventListener("submit", (event) => {
                    event.preventDefault();
                    cardFields
                        .submit({
                            contingencies: ["SCA_ALWAYS"],
                            // Cardholder's first and last name
                            cardholderName: document.getElementById("card-holder-name").value,
                            // Billing Address
                            billingAddress: {
                                // Street address, line 1
                                streetAddress: document.getElementById(
                                    "card-billing-address-street"
                                ).value,
                                // Street address, line 2 (Ex: Unit, Apartment, etc.)
                                extendedAddress: document.getElementById(
                                    "card-billing-address-unit"
                                ).value,
                                // State
                                region: document.getElementById("card-billing-address-state").value,
                                // City
                                locality: document.getElementById("card-billing-address-city")
                                    .value,
                                // Postal Code
                                postalCode: document.getElementById("card-billing-address-zip")
                                    .value,
                                // Country Code
                                countryCodeAlpha2: document.getElementById(
                                    "card-billing-address-country"
                                ).value,
                            },
                        })
                        .then(function(payload) {
                            console.log("This is the payload for capture ", payload);
                            console.log('Preparing to capture Order ID: ', orderId);
                            console.log("Checking 3DS parameters");
                            console.log('liabilityShifted: ', payload.liabilityShifted);
                            console.log('authenticationReason: ', payload.authenticationReason);
                            console.log('authenticationStatus: ', payload.authenticationStatus);
                            // Reponse parameters https://developer.paypal.com/docs/checkout/advanced/customize/3d-secure/response-parameters/
                            switch (payload.liabilityShifted) {
                                case 'undefined':
                                    console.log('The authentication system is not available. Do not continue with authorization. Request cardholder to retry');
                                    alert('Please retry');
                                    break;
                                case 'true':
                                    if (payload.authenticationStatus === 'YES' && payload.authenticationReason === 'SUCCESSFUL') {
                                        console.log('Liability confirmed. Continue with authorization');
                                        fetch('capture_payment.php?id=' + orderId)
                                            .then((res) => res.json())
                                            .then((orderData) => {
                                                console.log("Capture payment data: ", orderData);
                                                // Three cases to handle:
                                                //   (1) Recoverable INSTRUMENT_DECLINED -> call actions.restart()
                                                //   (2) Other non-recoverable errors -> Show a failure message
                                                //   (3) Successful transaction -> Show confirmation or thank you
                                                // This example reads a v2/checkout/orders capture response, propagated from the server
                                                // You could use a different API or structure for your 'orderData'
                                                var errorDetail =
                                                    Array.isArray(orderData.details) && orderData.details[0];
                                                if (errorDetail && errorDetail.issue === "INSTRUMENT_DECLINED") {
                                                    return actions.restart(); // Recoverable state, per:
                                                    // https://developer.paypal.com/docs/checkout/integration-features/funding-failure/
                                                }
                                                if (errorDetail) {
                                                    var msg = "Sorry, your transaction could not be processed.";
                                                    if (errorDetail.description)
                                                        msg += "\n\n" + errorDetail.description;
                                                    if (orderData.debug_id) msg += " (" + orderData.debug_id + ")";
                                                    return alert(msg); // Show a failure message
                                                }
                                                // Show a success message or redirect
                                                alert("Transaction completed!");
                                            });
                                    }
                                    break;
                                case 'false':
                                    console.log('Do not continue with authorization. Request cardholder to retry.');
                                    alert('Please retry');
                                    break;
                                default:
                                    alert('Please retry');
                                    console.log('Something went wrong with checking the liabilityShifted')
                            }

                        })
                        .catch((err) => {
                            console.log(err);
                            alert("Payment could not be captured! " + JSON.stringify(err));
                        });
                });
            });
        } else {
            // Hides card fields if the merchant isn't eligible
            document.querySelector("#card-form").style = 'display: none';
        }
    </script>
</body>

</html>