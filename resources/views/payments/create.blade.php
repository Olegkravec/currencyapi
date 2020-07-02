<html>
<head>
    <title>Create new payment</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
<div align="center" class="payments-create-form">
    <label for="card-holder-name">Card Holder:<br />
        <input id="card-holder-name" type="text">
    </label>

    <!-- Stripe Elements Placeholder -->
    <div id="card-element"></div>

    <button id="card-button" data-secret="{{ $intent->client_secret }}">
        Create Payment Method
    </button>
</div>


<script src="https://js.stripe.com/v3/"></script>

<script>
    const stripe = Stripe('{{ env('STRIPE_KEY') }}');

    const elements = stripe.elements();
    const cardElement = elements.create('card');

    cardElement.mount('#card-element');

    const cardHolderName = document.getElementById('card-holder-name');
    const cardButton = document.getElementById('card-button');

    cardButton.addEventListener('click', async (e) => {
        const { paymentMethod, error } = await stripe.createPaymentMethod(
            'card', cardElement, {
                billing_details: { name: cardHolderName.value }
            }
        );

        if (error) {
            alert(error);
        } else {
            console.table(paymentMethod);
            document.querySelector("body .payments-create-form").innerHTML = "Payment method confirmed";
            window.dispatchEvent(new Event("onSuccessfulPayment"));
            setTimeout(function () {
                var f = document.createElement("form");
                f.action = "{{ $postback_url }}?token={{$token}}";
                f.method = "POST";
                var input = document.createElement("input");
                input.type = "text";
                input.hidden = "hidden";
                input.name = "payment_method";
                input.value = paymentMethod.id;
                f.appendChild(input);
                document.body.appendChild(f);
                f.submit();
            });
        }
    });
</script>
</body>
</html>