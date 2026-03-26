@php
    /*
    |--------------------------------------------------------------------------
    | Sidebar nav items
    |--------------------------------------------------------------------------
    | Each item uses a NAMED route from routes/web.php.
    | We generate URLs with route('name') so clicks actually navigate.
    */
    $sideNavItems = [
        [
            'name' => 'Dashboard',
            'route' => 'dashboard',
        ],
        [
            'name' => 'Customers',
            'route' => 'customers',
        ],
        [
            'name' => 'Accounts',
            'route' => 'accounts',
        ],
    ];
@endphp

<div class="side-navbar">
    <div class="side-navbar-header">
        <div class="side-navbar-mark" aria-hidden="true"></div>
        <div class="side-navbar-title">
            <div class="side-navbar-brand">Bare Metals</div>
            <div class="side-navbar-subtitle">Admin</div>
        </div>
    </div>

    <nav class="side-navbar-nav" aria-label="Navigation">
        <ul class="side-navbar-list">
            @foreach ($sideNavItems as $item)
                @php
                    $isActive = request()->routeIs($item['route']);
                @endphp

                <li class="side-navbar-item">
                    <a href="{{ route($item['route']) }}" class="side-navbar-link {{ $isActive ? 'is-active' : '' }}"
                        @if ($isActive) aria-current="page" @endif>

                        <span class="side-navbar-text">{{ $item['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</div>
