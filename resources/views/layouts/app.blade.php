<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sportify' }}</title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #0f172a;
            color: #e5e7eb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #020617;
            border-radius: 1rem;
            padding: 2rem 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.9);
            border: 1px solid rgba(148, 163, 184, 0.2);
        }
        .card h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        .card p.subtitle {
            font-size: 0.9rem;
            color: #9ca3af;
            margin-bottom: 1.5rem;
        }
        label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #9ca3af;
            margin-bottom: 0.35rem;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.7rem 0.85rem;
            border-radius: 0.6rem;
            border: 1px solid rgba(148, 163, 184, 0.5);
            background: rgba(15, 23, 42, 0.9);
            color: #e5e7eb;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        }
        input:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 1px rgba(56, 189, 248, 0.6);
            background: rgba(15, 23, 42, 1);
        }
        .btn-primary {
            width: 100%;
            border: none;
            margin-top: 0.5rem;
            padding: 0.8rem 1rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.95rem;
            color: #0f172a;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            cursor: pointer;
            transition: transform 0.1s ease, box-shadow 0.1s ease, filter 0.1s ease;
            box-shadow: 0 18px 40px rgba(22, 163, 74, 0.5);
        }
        .btn-primary:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
            box-shadow: 0 22px 50px rgba(22, 163, 74, 0.6);
        }
        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 14px 30px rgba(22, 163, 74, 0.5);
        }
        .muted-link {
            font-size: 0.85rem;
            color: #9ca3af;
            text-align: center;
            margin-top: 1rem;
        }
        .muted-link a {
            color: #38bdf8;
            text-decoration: none;
            font-weight: 500;
        }
        .muted-link a:hover {
            text-decoration: underline;
        }
        .error-text {
            color: #f97373;
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
            background: rgba(248, 113, 113, 0.08);
            border: 1px solid rgba(248, 113, 113, 0.4);
            color: #fecaca;
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
            color: #9ca3af;
            text-decoration: none;
        }
        .top-link a:hover {
            color: #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="card">
        @yield('content')
    </div>
</body>
</html>


