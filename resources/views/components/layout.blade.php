<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bare Metals</title>

    @vite('resources/css/app.css')
</head>
<body>
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