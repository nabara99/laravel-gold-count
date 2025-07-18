<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="light">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <a class="brand-link">
            <span>Finance</span>
        </a>
        <!--end::Brand Link-->
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation"
                aria-label="Main navigation" data-accordion="false" id="navigation">
                <li class="nav-item">
                    <a href="{{ route('home') }}"
                        class="nav-link {{ str_contains(Route::currentRouteName(), 'home') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                @if (auth()->user()->role->role_name == 'admin')
                    <li class="nav-item">
                        <a href="{{ route('user.index') }}"
                            class="nav-link {{ str_contains(Route::currentRouteName(), 'user') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-people"></i>
                            <p>
                                User Management
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('locations.index') }}"
                            class="nav-link {{ str_contains(Route::currentRouteName(), 'locations') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-geo-alt"></i>
                            <p>
                                Lokasi
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('workers.index') }}"
                            class="nav-link {{ str_contains(Route::currentRouteName(), 'workers') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-person-walking"></i>
                            <p>
                                Pekerja
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('absens.index') }}"
                            class="nav-link {{ str_contains(Route::currentRouteName(), 'absens') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-person-vcard"></i>
                            <p>
                                Absen Pekerja
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('stocks.index') }}"
                            class="nav-link {{ str_contains(Route::currentRouteName(), 'stocks') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-currency-exchange"></i>
                            <p>
                                Hasil
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('invest.index') }}"
                            class="nav-link {{ str_contains(Route::currentRouteName(), 'invest') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-currency-bitcoin"></i>
                            <p>
                                Manajemen Investor
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('periods.index') }}"
                            class="nav-link {{ str_contains(Route::currentRouteName(), 'periods') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-stopwatch"></i>
                            <p>
                                Setting Periode
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('transactions.index') }}"
                            class="nav-link {{ str_contains(Route::currentRouteName(), 'transactions') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-shop"></i>
                            <p>
                                Transaksi
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('report.absen') }}"
                            class="nav-link {{ str_contains(Route::currentRouteName(), 'report') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-filetype-pdf"></i>
                            <p>
                                Rekap Absen
                            </p>
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a href="{{ route('laporan-penghasilan.index') }}"
                            class="nav-link {{ str_contains(Route::currentRouteName(), 'laporan-penghasilan') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-filetype-pdf"></i>
                            <p>
                                Rekap Absen
                            </p>
                        </a>
                    </li> --}}
                @endif
            </ul>
            <!--end::Sidebar Menu-->
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>
