<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->invoiceID ?? 'INV-' . $payment->paymentID }}</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #374151;
            background: #ffffff;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
        }

        .company-info h1 {
            color: #1f2937;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .company-info p {
            color: #6b7280;
            font-size: 14px;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h2 {
            color: #6366f1;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .invoice-title .invoice-number {
            font-size: 18px;
            color: #1f2937;
            font-weight: 600;
        }

        /* Invoice Info */
        .invoice-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-bottom: 40px;
            background: #f9fafb;
            padding: 25px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .info-section h3 {
            color: #1f2937;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-item {
            margin-bottom: 8px;
            display: flex;
        }

        .info-label {
            color: #6b7280;
            min-width: 120px;
            font-weight: 500;
        }

        .info-value {
            color: #1f2937;
            font-weight: 500;
        }

        /* Event Details */
        .event-details {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            border: 1px solid #bae6fd;
        }

        .event-details h3 {
            color: #0369a1;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .event-details h3 i {
            font-style: normal;
            font-weight: 700;
        }

        .event-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        /* Items Table */
        .items-table {
            margin-bottom: 30px;
            overflow: hidden;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .table-header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            padding: 18px 20px;
            font-weight: 600;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f9fafb;
        }

        th {
            text-align: left;
            padding: 15px 20px;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
        }

        td {
            padding: 15px 20px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .description {
            font-weight: 500;
            color: #1f2937;
        }

        .amount {
            text-align: right;
            font-weight: 600;
            color: #1f2937;
        }

        /* Totals */
        .totals {
            background: #f9fafb;
            padding: 25px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            margin-bottom: 40px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
        }

        .total-label {
            font-size: 16px;
            color: #6b7280;
        }

        .total-value {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
        }

        .grand-total {
            border-top: 2px solid #e5e7eb;
            margin-top: 15px;
            padding-top: 15px;
        }

        .grand-total .total-label {
            font-size: 20px;
            color: #1f2937;
            font-weight: 700;
        }

        .grand-total .total-value {
            font-size: 24px;
            color: #10b981;
            font-weight: 800;
        }

        /* Payment Status */
        .payment-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #d1fae5;
            color: #065f46;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            margin-top: 10px;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 30px 20px 0;
            color: #6b7280;
            font-size: 13px;
            border-top: 1px solid #e5e7eb;
            margin-top: 40px;
        }

        .footer p {
            margin-bottom: 8px;
        }

        .contact-info {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 15px;
            color: #4b5563;
        }

        /* QR Code Placeholder */
        .qr-section {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            background: #f9fafb;
            border-radius: 12px;
            border: 1px dashed #d1d5db;
        }

        .qr-placeholder {
            display: inline-block;
            width: 120px;
            height: 120px;
            background: #e5e7eb;
            border-radius: 8px;
            position: relative;
            margin: 10px;
        }

        .qr-placeholder::after {
            content: "QR Code";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #6b7280;
            font-weight: 500;
        }

        /* Utility Classes */
        .text-success {
            color: #10b981;
        }

        .text-primary {
            color: #6366f1;
        }

        .text-muted {
            color: #6b7280;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        .mb-30 {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<div class="container">
    
    <!-- Header -->
    <div class="header">
        <div class="company-info">
            <h1>Sportify Events</h1>
            <p>Event Registration Platform</p>
            <p class="text-muted">sportify@example.com | +60 12-345 6789</p>
        </div>
        <div class="invoice-title">
            <h2>INVOICE</h2>
            <div class="invoice-number">#{{ $invoice->invoiceID ?? 'INV-' . ($payment->paymentID ?? 'N/A') }}</div>
            <div class="payment-status">
                PAID
            </div>
        </div>
    </div>

    <!-- Invoice Information -->
    <div class="invoice-info">
        <div class="info-section">
            <h3>BILLED TO</h3>
            <div class="info-item">
                <span class="info-label">Name:</span>
                <span class="info-value">{{ $student->name ?? 'Customer' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $student->email ?? 'customer@example.com' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Student ID:</span>
                <span class="info-value">{{ $student->studentID ?? 'N/A' }}</span>
            </div>
        </div>
        <div class="info-section">
            <h3>INVOICE DETAILS</h3>
            <div class="info-item">
                <span class="info-label">Invoice Date:</span>
                <span class="info-value">{{ now()->format('d M Y, H:i') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Payment Date:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($payment->paymentDate)->format('d M Y, H:i') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Payment Method:</span>
                <span class="info-value">{{ $payment->paymentMethod }}</span>
            </div>
        </div>
    </div>

    <!-- Event Details -->
    <div class="event-details">
        <h3>EVENT INFORMATION</h3>
        <div class="event-grid">
            <div class="info-item">
                <span class="info-label">Event Name:</span>
                <span class="info-value">{{ $event->event_name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Event Date:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($event->event_start_date)->format('d M Y') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Location:</span>
                <span class="info-value">{{ $event->location ?? 'To be announced' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Registration ID:</span>
                <span class="info-value">REG-{{ $payment->eventJoined->eventJoinedID ?? $payment->paymentID }}</span>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="items-table">
        <div class="table-header">
            ITEM DETAILS
        </div>
        <table>
            <thead>
                <tr>
                    <th width="70%">Description</th>
                    <th width="30%">Amount (RM)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="description">
                        <strong>{{ $event->event_name }}</strong><br>
                        <span class="text-muted" style="font-size: 13px;">
                            Event Registration Fee • General Admission
                        </span>
                    </td>
                    <td class="amount">
                        RM {{ number_format($payment->paymentAmount, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Totals -->
    <div class="totals">
        <div class="total-row">
            <span class="total-label">Subtotal</span>
            <span class="total-value">RM {{ number_format($payment->paymentAmount, 2) }}</span>
        </div>
        <div class="total-row">
            <span class="total-label">Processing Fee</span>
            <span class="total-value">RM 0.00</span>
        </div>
        <div class="total-row">
            <span class="total-label">Service Tax</span>
            <span class="total-value">RM 0.00</span>
        </div>
        <div class="total-row grand-total">
            <span class="total-label">Total Paid</span>
            <span class="total-value">RM {{ number_format($payment->paymentAmount, 2) }}</span>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Terms & Conditions:</strong></p>
        <p class="text-muted mb-20">
            1. This invoice is proof of payment for event registration.<br>
            2. Registration is non-refundable but transferable with prior notice.<br>
            3. Present this invoice/QR code at the event venue for check-in.<br>
            4. For any queries, contact support@sportify.com
        </p>
        
        <div class="contact-info">
            <div>📍 {{ $event->location ?? 'Venue to be confirmed via email' }}</div>
            <div>📞 +60 12-345 6789</div>
            <div>📧 support@sportify.com</div>
        </div>
        
        <p style="margin-top: 20px;">
            This is a computer-generated invoice. No signature is required.
        </p>
    </div>

</div>

</body>
</html>