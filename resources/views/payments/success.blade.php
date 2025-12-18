<!DOCTYPE html>
<html>
<head>
    <title>Payment Success</title>
</head>
<body>

<h2>Payment Successful 🎉</h2>

<p><strong>Event:</strong> {{ $payment->eventJoined->event->event_name }}</p>
<p><strong>Amount Paid:</strong> RM{{ number_format($payment->paymentAmount, 2) }}</p>
<p><strong>Payment Method:</strong> {{ $payment->paymentMethod }}</p>

<a href="/">Back to Home</a>

</body>
</html>
