<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            color: #333;
        }

        .container {
            width: 100%;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .section {
            margin-bottom: 20px;
        }

        .label {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        th {
            background: #f3f4f6;
        }

        .total {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>

<div class="container">

    <h1>Payment Invoice</h1>

    <div class="section">
        <p><span class="label">Invoice ID:</span> {{ $invoice->invoiceID }}</p>
        <p><span class="label">Invoice Date:</span>
            {{ $invoice->dateTimeGenerated->format('d M Y, H:i') }}
        </p>
    </div>

    <div class="section">
        <p><span class="label">Event Name:</span> {{ $event->event_name }}</p>
        <p><span class="label">Event Date:</span>
            {{ \Carbon\Carbon::parse($event->event_start_date)->format('d M Y') }}
        </p>
    </div>

    <table>
        <thead>
        <tr>
            <th>Description</th>
            <th>Amount (RM)</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Event Registration Fee</td>
            <td>{{ number_format($payment->paymentAmount, 2) }}</td>
        </tr>
        </tbody>
    </table>

    <p class="total">
        Total Paid: RM {{ number_format($payment->paymentAmount, 2) }}
    </p>

    <div class="footer">
        This invoice was generated automatically.<br>
        No signature is required.
    </div>

</div>

</body>
</html>
