<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('tracking.app_name'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Theme variables: tuned to a Takealot-like blue palette and easy to retune */
            --bg: #eef6fb;
            --bg-strong: #dcecf7;
            --card: rgba(255, 255, 255, 0.92);
            --card-strong: #ffffff;
            --text: #24313d;
            --muted: #617284;
            --line: rgba(20, 58, 92, 0.12);
            --brand: #0b79bf;
            --brand-deep: #075d93;
            --brand-soft: rgba(11, 121, 191, 0.12);
            --accent: #00b8e6;
            --accent-soft: rgba(0, 184, 230, 0.14);
            --success: #0f8d6a;
            --danger: #c24a3f;
            --shadow: 0 24px 50px rgba(6, 72, 117, 0.12);
            --radius: 28px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Roboto", sans-serif;
            font-weight: 400;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(0, 184, 230, 0.18), transparent 30%),
                radial-gradient(circle at top right, rgba(11, 121, 191, 0.14), transparent 24%),
                linear-gradient(180deg, #f8fcfe 0%, var(--bg) 100%);
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.34) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.34) 1px, transparent 1px);
            background-size: 36px 36px;
            mask-image: linear-gradient(180deg, rgba(0, 0, 0, 0.22), transparent 75%);
            pointer-events: none;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        img {
            max-width: 100%;
            display: block;
        }

        .shell {
            position: relative;
            width: min(1160px, calc(100% - 32px));
            margin: 0 auto;
            padding: 26px 0 48px;
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 28px;
            padding: 18px 22px;
            border: 1px solid rgba(255, 255, 255, 0.74);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(14px);
            box-shadow: 0 8px 30px rgba(10, 82, 127, 0.08);
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            justify-content: flex-start;
            font-size: 1.05rem;
            font-weight: 500;
        }

        .brand-logo {
            width: 176px;
            height: 52px;
            object-fit: contain;
            object-position: left center;
        }

        .nav-links {
            display: inline-flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .nav-meta {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .nav-links a {
            padding: 10px 16px;
            border-radius: 999px;
            color: var(--muted);
            font-size: 0.94rem;
            font-weight: 500;
            transition: 0.2s ease;
        }

        .nav-links a:hover,
        .nav-links a.is-active {
            color: var(--text);
            background: rgba(255, 255, 255, 0.92);
            box-shadow: inset 0 0 0 1px rgba(11, 121, 191, 0.16);
        }

        .flash {
            margin-bottom: 18px;
            padding: 16px 18px;
            border-radius: 18px;
            border: 1px solid rgba(15, 141, 106, 0.18);
            background: rgba(15, 141, 106, 0.09);
            color: #125140;
            font-weight: 600;
        }

        .card {
            border: 1px solid rgba(255, 255, 255, 0.9);
            border-radius: var(--radius);
            background: var(--card);
            box-shadow: var(--shadow);
            backdrop-filter: blur(12px);
        }

        .card-section {
            padding: 28px;
        }

        .hero-grid,
        .content-grid,
        .admin-grid,
        .detail-grid {
            display: grid;
            gap: 22px;
            align-items: start;
        }

        .hero-grid {
            grid-template-columns: 1.2fr 0.9fr;
        }

        .content-grid,
        .detail-grid {
            grid-template-columns: 1.2fr 0.8fr;
        }

        .admin-grid {
            grid-template-columns: 1.05fr 1.2fr;
            align-items: start;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--brand-deep);
            font-size: 0.86rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        h1,
        h2,
        h3 {
            margin: 0;
            font-family: "Roboto", sans-serif;
            line-height: 1.02;
        }

        h1 {
            font-size: clamp(2.3rem, 5vw, 4.6rem);
            letter-spacing: -0.05em;
            font-weight: 500;
        }

        h2 {
            font-size: clamp(1.45rem, 3vw, 2.2rem);
            letter-spacing: -0.04em;
            font-weight: 500;
        }

        h3 {
            font-size: 1.2rem;
            letter-spacing: -0.03em;
            font-weight: 500;
        }

        p {
            margin: 0;
            color: var(--muted);
            line-height: 1.65;
        }

        .stack {
            display: grid;
            gap: 18px;
        }

        .muted {
            color: var(--muted);
        }

        .form-grid,
        .stats-grid,
        .mini-grid,
        .timeline-grid,
        .badge-grid,
        .input-grid {
            display: grid;
            gap: 16px;
        }

        .form-grid.two,
        .stats-grid.two,
        .mini-grid.two,
        .badge-grid.two,
        .input-grid.two {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .stats-grid.four {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .field {
            display: grid;
            gap: 8px;
        }

        .field label {
            font-size: 0.92rem;
            font-weight: 700;
        }

        .field input,
        .field textarea,
        .field select {
            width: 100%;
            padding: 15px 16px;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.84);
            color: var(--text);
            font: inherit;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .field textarea {
            min-height: 132px;
            resize: vertical;
        }

        .field input:focus,
        .field textarea:focus,
        .field select:focus {
            border-color: rgba(11, 121, 191, 0.46);
            box-shadow: 0 0 0 4px rgba(11, 121, 191, 0.12);
        }

        .field input.is-invalid,
        .field textarea.is-invalid,
        .field select.is-invalid {
            border-color: rgba(194, 74, 63, 0.5);
            box-shadow: 0 0 0 4px rgba(194, 74, 63, 0.1);
        }

        .snake-input-wrap {
            position: relative;
            padding: 2px;
            border-radius: 20px;
            overflow: hidden;
            isolation: isolate;
            background: rgba(11, 121, 191, 0.16);
            box-shadow:
                0 16px 34px rgba(11, 121, 191, 0.12),
                inset 0 0 0 1px rgba(11, 121, 191, 0.22);
        }

        .snake-input-wrap::before {
            content: "";
            position: absolute;
            inset: -130%;
            background:
                conic-gradient(
                    from 0deg,
                    transparent 0deg 300deg,
                    rgba(0, 184, 230, 0.14) 314deg,
                    rgba(0, 184, 230, 0.78) 330deg,
                    rgba(11, 121, 191, 1) 344deg,
                    rgba(0, 184, 230, 0.52) 356deg,
                    transparent 360deg
                );
            animation: snake-spin 2.8s linear infinite;
            filter: blur(4px);
        }

        .snake-input-wrap::after {
            content: "";
            position: absolute;
            inset: 2px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.98);
            box-shadow: inset 0 0 0 1px rgba(11, 121, 191, 0.2);
            z-index: 0;
        }

        .snake-input-wrap input {
            position: relative;
            z-index: 1;
            border: 0;
            background: transparent;
            box-shadow: none;
        }

        .snake-input-wrap:focus-within {
            box-shadow: 0 20px 40px rgba(11, 121, 191, 0.18);
        }

        .snake-input-wrap:focus-within::before {
            filter: blur(3px);
        }

        .field small,
        .helper {
            color: var(--muted);
            font-size: 0.84rem;
        }

        .error {
            color: var(--danger);
            font-size: 0.84rem;
            font-weight: 600;
            min-height: 1em;
        }

        .form-status {
            display: none;
            padding: 14px 16px;
            border-radius: 16px;
            font-size: 0.92rem;
            font-weight: 600;
        }

        .form-status.is-visible {
            display: block;
        }

        .form-status.is-success {
            color: #125140;
            background: rgba(15, 141, 106, 0.1);
            border: 1px solid rgba(15, 141, 106, 0.15);
        }

        .form-status.is-error {
            color: #7d2d24;
            background: rgba(194, 74, 63, 0.1);
            border: 1px solid rgba(194, 74, 63, 0.16);
        }

        .btn,
        .btn-secondary,
        .btn-ghost {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 20px;
            border: 0;
            border-radius: 18px;
            cursor: pointer;
            font: inherit;
            font-weight: 800;
            transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
        }

        .btn {
            color: #fff;
            background: linear-gradient(135deg, var(--brand) 0%, var(--accent) 100%);
            box-shadow: 0 16px 26px rgba(11, 121, 191, 0.24);
        }

        .btn-secondary {
            color: var(--brand-deep);
            background: #edf8fd;
            box-shadow: inset 0 0 0 1px rgba(11, 121, 191, 0.16);
        }

        .btn-ghost {
            color: var(--text);
            background: rgba(255, 255, 255, 0.72);
            box-shadow: inset 0 0 0 1px rgba(25, 25, 25, 0.08);
        }

        .btn:hover,
        .btn-secondary:hover,
        .btn-ghost:hover {
            transform: translateY(-1px);
        }

        .btn:disabled,
        .btn[disabled] {
            cursor: not-allowed;
            opacity: 0.52;
            transform: none;
            box-shadow: none;
            filter: grayscale(0.08);
        }

        .code-box,
        .metric,
        .count-box,
        .pill,
        .status-badge {
            border-radius: 22px;
        }

        .code-box {
            padding: 24px;
            border: 2px dashed rgba(11, 121, 191, 0.34);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.94) 0%, rgba(237, 248, 253, 0.96) 100%);
        }

        .code-label {
            margin-bottom: 10px;
            color: var(--brand-deep);
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .tracking-code {
            font-family: "Roboto", sans-serif;
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            font-weight: 700;
            letter-spacing: 0.14em;
        }

        .metric {
            padding: 20px;
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(33, 26, 18, 0.08);
        }

        .metric strong {
            display: block;
            margin-bottom: 6px;
            font-size: 1.3rem;
            font-family: "Roboto", sans-serif;
            font-weight: 500;
        }

        .timeline {
            position: relative;
            display: grid;
            gap: 18px;
        }

        .timeline::before {
            content: "";
            position: absolute;
            left: 16px;
            top: 6px;
            bottom: 6px;
            width: 2px;
            background: rgba(25, 25, 25, 0.1);
        }

        .timeline-item {
            position: relative;
            padding-left: 48px;
        }

        .timeline-item::before {
            content: "";
            position: absolute;
            left: 7px;
            top: 8px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #c7d7e4;
            box-shadow: 0 0 0 6px rgba(255, 255, 255, 0.75);
        }

        .timeline-item.completed::before,
        .timeline-item.current::before {
            background: var(--brand);
        }

        .timeline-item h3 {
            margin-bottom: 6px;
        }

        .timeline-item.pending h3,
        .timeline-item.pending p {
            color: #ada59b;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            background: rgba(255, 255, 255, 0.9);
            font-size: 0.86rem;
            font-weight: 700;
            color: var(--muted);
        }

        .status-badge.current {
            background: var(--accent-soft);
            color: var(--brand-deep);
        }

        .status-badge.completed {
            background: rgba(19, 131, 93, 0.1);
            color: var(--success);
        }

        .status-badge.pending {
            background: rgba(25, 25, 25, 0.05);
        }

        .status-badge.processing {
            background: rgba(11, 121, 191, 0.1);
            color: var(--brand-deep);
        }

        .status-badge.failed {
            background: rgba(194, 74, 63, 0.1);
            color: var(--danger);
        }

        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 18px;
            background: linear-gradient(180deg, rgba(237, 248, 253, 0.95) 0%, #fff 100%);
            border: 1px solid rgba(11, 121, 191, 0.2);
            font-weight: 700;
        }

        .countdown {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
            align-items: center;
        }

        .count-box {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            min-width: 132px;
            padding: 18px 12px;
            text-align: center;
            color: #fff;
            background: linear-gradient(180deg, var(--brand) 0%, var(--accent) 100%);
            box-shadow: 0 18px 30px rgba(11, 121, 191, 0.22);
        }

        .count-box strong {
            display: block;
            font-family: "Roboto", sans-serif;
            font-size: clamp(1.6rem, 4vw, 2.2rem);
            font-weight: 700;
            line-height: 1;
        }

        .count-box span {
            display: block;
            margin-top: 6px;
            font-size: 0.76rem;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .product {
            display: grid;
            grid-template-columns: 118px 1fr;
            gap: 18px;
            align-items: center;
        }

        .product-image,
        .placeholder-image {
            width: 118px;
            height: 118px;
            border-radius: 24px;
            object-fit: cover;
            background: var(--bg-strong);
            box-shadow: inset 0 0 0 1px rgba(25, 25, 25, 0.06);
        }

        .placeholder-image {
            display: grid;
            place-items: center;
            font-family: "Roboto", sans-serif;
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--brand-deep);
        }

        .locale-switcher {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.78);
            box-shadow: inset 0 0 0 1px rgba(25, 25, 25, 0.08);
        }

        .locale-switcher a {
            padding: 8px 12px;
            border-radius: 999px;
            color: var(--muted);
            font-size: 0.84rem;
            font-weight: 500;
            transition: 0.2s ease;
        }

        .locale-switcher a.is-active {
            color: var(--text);
            background: rgba(255, 255, 255, 0.96);
            box-shadow: inset 0 0 0 1px rgba(11, 121, 191, 0.18);
        }

        .table-wrap {
            overflow: auto;
            border-radius: 22px;
            border: 1px solid rgba(25, 25, 25, 0.08);
            background: rgba(255, 255, 255, 0.84);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
        }

        th,
        td {
            padding: 16px 18px;
            text-align: left;
            border-bottom: 1px solid rgba(25, 25, 25, 0.07);
            font-size: 0.95rem;
        }

        th {
            color: var(--muted);
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            background: rgba(232, 244, 250, 0.92);
        }

        .link-inline {
            color: var(--brand-deep);
            font-weight: 700;
        }

        .inline-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }

        .check {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 0.92rem;
            font-weight: 700;
        }

        .check input {
            width: 18px;
            height: 18px;
            accent-color: var(--brand);
        }

        .note-box {
            padding: 16px 18px;
            border-radius: 18px;
            background: rgba(11, 121, 191, 0.08);
            color: var(--brand-deep);
            font-size: 0.93rem;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 18px;
        }

        .pagination nav {
            width: 100%;
        }

        .pagination svg {
            width: 18px;
            height: 18px;
        }

        @keyframes snake-spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 980px) {
            .hero-grid,
            .content-grid,
            .admin-grid,
            .detail-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid.four,
            .form-grid.two,
            .stats-grid.two,
            .mini-grid.two,
            .badge-grid.two,
            .input-grid.two {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 720px) {
            .shell {
                width: min(100% - 20px, 100%);
                padding-top: 14px;
            }

            .nav {
                flex-direction: column;
                align-items: stretch;
                border-radius: 28px;
            }

            .brand {
                justify-content: flex-start;
            }

            .brand-logo {
                width: 156px;
                height: 46px;
            }

            .nav-meta {
                justify-content: space-between;
            }

            .nav-links {
                justify-content: space-between;
            }

            .card-section {
                padding: 22px;
            }

            .stats-grid.four,
            .form-grid.two,
            .stats-grid.two,
            .mini-grid.two,
            .badge-grid.two,
            .input-grid.two {
                grid-template-columns: 1fr;
            }

            .product {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .count-box {
                min-width: 50px !important;
            }
            .code-box, .metric, .count-box, .pill, .status-badge {
                border-radius: 7px !important;
            }
        }
    </style>
</head>
<body>
    @php
        $locales = ['en' => __('tracking.languages.en'), 'pt' => __('tracking.languages.pt')];
    @endphp
    <div class="shell">
        <header class="nav">
            <a href="{{ route('tracking.index') }}" class="brand">
                <img src="{{ asset('assets/logod.png') }}" alt="{{ __('tracking.app_name') }}" class="brand-logo">
            </a>

            <div class="nav-meta">
                <nav class="nav-links">
                    <a href="{{ route('tracking.index') }}" class="{{ request()->routeIs('tracking.*') ? 'is-active' : '' }}">{{ __('tracking.nav.tracking') }}</a>
                    @if (request()->routeIs('admin.*'))
                        <a href="{{ route('admin.tracks.index') }}" class="is-active">{{ __('tracking.nav.admin') }}</a>
                    @endif
                </nav>

                <div class="locale-switcher">
                    @foreach ($locales as $locale => $label)
                        <a
                            href="{{ request()->fullUrlWithQuery(['lang' => $locale]) }}"
                            class="{{ app()->getLocale() === $locale ? 'is-active' : '' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </header>

        @if (session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif

        @yield('content')
    </div>

    <script>
        const copiedText = @json(__('tracking.actions.copied'));

        document.querySelectorAll('[data-copy-value]').forEach(function (button) {
            button.addEventListener('click', function () {
                navigator.clipboard.writeText(button.dataset.copyValue).then(function () {
                    const original = button.textContent;
                    button.textContent = copiedText;
                    setTimeout(function () {
                        button.textContent = original;
                    }, 1600);
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
