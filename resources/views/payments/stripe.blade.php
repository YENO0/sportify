<!DOCTYPE html>
<html>
<head>
    <title>Stripe Payment</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>

<h2>Stripe Payment</h2>

<div id="card-element"></div>
<button id="pay-btn">Pay</button>

<form id="confirm-form" method="POST" action="{{ route('payments.confirm') }}">
    @csrf
    <input type="hidden" name="event_id" value="{{ $event->eventID }}">
    <input type="hidden" name="student_id" value="{{ $student_id }}">
    <input type="hidden" name="payment_intent_id" id="payment_intent_id">
</form>

<script>
    const stripe = Stripe("{{ config('services.stripe.key') }}");
    const elements = stripe.elements();
    const card = elements.create('card');
    card.mount('#card-element');

    document.getElementById('pay-btn').addEventListener('click', async () => {
        const { paymentIntent, error } = await stripe.confirmCardPayment(
            "{{ $clientSecret }}",
            { payment_method: { card: card } }
        );

        if (error) {
            alert(error.message);
        } else {
            document.getElementById('payment_intent_id').value = paymentIntent.id;
            document.getElementById('confirm-form').submit();
        }
    });
</script>

</body>
</html>
