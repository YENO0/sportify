<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction History</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f8;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #6366f1;
            color: white;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .status {
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
        }
        .status.registered { background: #10b981; }
        .status.cancelled { background: #ef4444; }
    </style>
</head>
<body>
<div class="container">
    <h2>Transaction History</h2>

    @if($transactions->isEmpty())
        <p>No transactions found.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Description</th>
                    <th>Payment Amount</th>
                    <th>Status</th>
                    <th>Joined Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                    @php
                        $joined = $transaction->eventJoined;
                        $event = $joined->event ?? null;
                        $invoice = $joined->invoice ?? null;
                    @endphp
                    <tr>
                        <td>{{ $event->event_name ?? 'N/A' }}</td>
                        <td>{{ $event->event_description ?? 'N/A' }}</td>
                        <td>RM {{ number_format($transaction->paymentAmount, 2) }}</td>
                        <td>
                            <span class="status {{ $joined->status ?? '' }}">
                                {{ ucfirst($joined->status ?? 'N/A') }}
                            </span>
                        </td>
                        <td>{{ $joined->joinedDate ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
</body>
</html>
