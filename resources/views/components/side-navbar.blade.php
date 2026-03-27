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
            'icon' => 'home',
        ],
        [
            'name' => 'Customers',
            'route' => 'customers.index',
            'icon' => 'users',
        ],
        [
            'name' => 'Accounts',
            'route' => 'accounts.index',
            'icon' => 'banknotes',
        ],
        [
            'name' => 'Metal Types',
            'route' => 'metal-types.index',
            'icon' => 'cog',
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

                        <div class="side-navbar-icon">
                            <x-dynamic-component :component="'heroicon-o-' . $item['icon']" style="width:18px;height:18px;" />
                        </div>
                        <span class="side-navbar-text">{{ $item['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</div>
