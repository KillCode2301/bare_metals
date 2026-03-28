<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bare Metals</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="toast-stack" class="toast-stack" aria-live="polite" aria-atomic="true"></div>

    @php
        $flashPayload = array_filter(
            [
                'success' => session('success'),
                'error' => session('error'),
                'warning' => session('warning'),
            ],
            static fn ($v) => $v !== null && $v !== '',
        );
        if (! isset($flashPayload['error']) && isset($errors) && $errors->any()) {
            $flashPayload['error'] = $errors->first();
        }
    @endphp
    @if (! empty($flashPayload))
        <script type="application/json" id="app-flash-payload">
            {!! json_encode($flashPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) !!}
        </script>
    @endif

    <div class="app-shell">
        <aside class="app-sidebar" aria-label="Primary">
            <x-side-navbar />
        </aside>

        <main class="app-main">
            <div class="app-content">
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>