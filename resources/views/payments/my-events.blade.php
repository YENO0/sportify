<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Registered Events</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f8;
            padding: 20px;
        }
        .container {
            max-width: 900px;
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
    <h2>My Registered Events</h2>

    @if($eventJoined->isEmpty())
        <p>You haven't registered for any events yet.</p>
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
                @foreach($eventJoined as $joined)
                    <tr>
                        <td>{{ $joined->event->event_name }}</td>
                        <td>{{ $joined->event->event_description }}</td>
                        <td>RM {{ number_format($joined->payment->paymentAmount, 2) }}</td>
                        <td>
                            <span class="status {{ $joined->status }}">
                                {{ ucfirst($joined->status) }}
                            </span>
                        </td>
                        <td>{{ $joined->joinedDate->format('d-m-Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
</body>
</html>
