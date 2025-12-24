<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sportify' }}</title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f8fafc; /* Light background */
            color: #1f2937; /* Dark grey text */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #ffffff; /* White card background */
            border-radius: 1rem;
            padding: 2rem 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.1); /* Lighter shadow */
            border: 1px solid rgba(0, 0, 0, 0.1); /* Lighter border */
        }
        .card h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        .card p.subtitle {
            font-size: 0.9rem;
            color: #4b5563; /* Darker subtitle */
            margin-bottom: 1.5rem;
        }
        label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #374151; /* Darker label */
            margin-bottom: 0.35rem;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.7rem 0.85rem;
            border-radius: 0.6rem;
            border: 1px solid rgba(209, 213, 219, 1); /* Light border */
            background: #f9fafb; /* Lighter input background */
            color: #1f2937; /* Darker input text */
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        }
        input:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.3); /* Lighter focus shadow */
            background: #ffffff; /* White on focus */
        }
        .btn-primary {
            width: 100%;
            border: none;
            margin-top: 0.5rem;
            padding: 0.8rem 1rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.95rem;
            color: #ffffff; /* White text */
            background: linear-gradient(135deg, #22c55e, #16a34a);
            cursor: pointer;
            transition: transform 0.1s ease, box-shadow 0.1s ease, filter 0.1s ease;
            box-shadow: 0 18px 40px rgba(34, 197, 94, 0.3); /* Lighter shadow */
        }
        .btn-primary:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
            box-shadow: 0 22px 50px rgba(34, 197, 94, 0.4); /* Lighter shadow */
        }
        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 14px 30px rgba(34, 197, 94, 0.3); /* Lighter shadow */
        }
        .muted-link {
            font-size: 0.85rem;
            color: #4b5563; /* Darker muted link */
            text-align: center;
            margin-top: 1rem;
        }
        .muted-link a {
            color: #2563eb; /* Darker blue link */
            text-decoration: none;
            font-weight: 500;
        }
        .muted-link a:hover {
            text-decoration: underline;
        }
        .error-text {
            color: #ef4444; /* Standard red */
            font-size: 0.78rem;
            margin-top: 0.25rem;
        }
        .alert {
            padding: 0.65rem 0.75rem;
            border-radius: 0.6rem;
            font-size: 0.8rem;
            margin-bottom: 0.9rem;
        }
        .alert-danger {
            background: rgba(254, 226, 226, 1); /* Light red background */
            border: 1px solid rgba(252, 165, 165, 1); /* Red border */
            color: #991b1b; /* Dark red text */
        }
        .field {
            margin-bottom: 0.9rem;
        }
        .top-link {
            text-align: right;
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }
        .top-link a {
            color: #4b5563; /* Darker link */
            text-decoration: none;
        }
        .top-link a:hover {
            color: #1f2937; /* Even darker on hover */
        }
        .captcha-container {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        .captcha-image {
            border: 1px solid rgba(209, 213, 219, 1); /* Light border */
            border-radius: 0.6rem;
            margin-right: 0.5rem;
        }
        .reload {
            background: #e5e7eb; /* Light grey background */
            border: 1px solid rgba(209, 213, 219, 1); /* Light border */
            color: #374151; /* Darker text */
            font-size: 1.2rem;
            border-radius: 0.6rem;
            cursor: pointer;
            padding: 0.2rem 0.6rem;
        }
        .reload:hover {
            background: #d1d5db; /* Darker grey on hover */
        }
    </style>
</head>
<body>
    <div class="card">
        @yield('content')
    </div>
    @stack('scripts')
</body>
</html>


