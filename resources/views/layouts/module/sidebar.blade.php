<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="brand-link">
        <img src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="POS" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">Agogo Bakery</span>
    </a>

    <div class="sidebar">

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item has-treeview">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->is('home*') ? 'active' : '' }}">
                        <i class="nav-icon fa fa-dashboard"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>

                {{-- @if (auth()->user()->can('show products') || auth()->user()->can('delete products') || auth()->user()->can('create products')) --}}
                <li class="nav-item has-treeview 
                    {{ request()->is('kategori*') ? 'menu-open' : '' }}  
                    {{ request()->is('produk*') ? 'menu-open' : '' }}">

                    <a href="#" class="nav-link 
                    {{ request()->is('kategori*') ? 'active' : '' }}
                    {{ request()->is('produk*') ? 'active' : '' }}">
                        <i class="nav-icon fa fa-server"></i>
                        <p>
                            Manajemen Produk
                            <i class="right fa fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('kategori.index') }}" class="nav-link {{ request()->is('kategori*') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p>Kategori</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('produk.index') }}" class="nav-link {{request()->is('produk*') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p>Produk</p>
                            </a>
                        </li>
                    </ul>
                </li>
                {{-- @endif --}}

                {{-- @role('kasir')
                <li class="nav-item">
                    <a href="{{ route('order.transaksi') }}" class="nav-link">
                        <i class="nav-icon fa fa-shopping-cart"></i>
                        <p>
                            Transaksi
                        </p>
                    </a>
                </li>
                @endrole --}}

                <li class="nav-item has-treeview
                {{ request()->is('order*') ? 'menu-open' : '' }}
                {{ request()->is('paid_order*') ? 'menu-open' : '' }}
                {{ request()->is('laporan_penjualan*') ? 'menu-open' : '' }}
                {{ request()->is('preorder*') ? 'menu-open' : '' }}
                {{ request()->is('laporan_kas*') ? 'menu-open' : '' }}
                {{ request()->is('laporan_produksi*') ? 'menu-open' : '' }}
                ">
                    <a href="#" class="nav-link
                    {{ request()->is('order*') ? 'active' : '' }}
                    {{ request()->is('paid_order*') ? 'active' : '' }}
                    {{ request()->is('laporan_penjualan*') ? 'active' : '' }}
                    {{ request()->is('preorder*') ? 'active' : '' }}
                    {{ request()->is('laporan_kas*') ? 'active' : '' }}
                    {{ request()->is('laporan_produksi*') ? 'active' : '' }}
                    ">
                        <i class="nav-icon fa fa-shopping-bag"></i>
                        <p>
                            Laporan
                            <i class="right fa fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="font-size: 14px;">
                        {{-- <li class="nav-item">
                                    <a href="{{ route('order.paid_order') }}" class="nav-link {{ request()->is('paid_order*') ? 'active' : '' }}">
                                        <i class="fa fa-circle-o nav-icon"></i>
                                        <p>Penjualan Terbayar</p>
                                    </a>
                        </li> --}}
                        <li class="nav-item">
                            <a href="{{ route('order.index') }}" class="nav-link {{ request()->is('order*') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                {{-- <p>Penjualan Harian</p> --}}
                                <p>Total Penjualan Per Item</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('order.laporan_penjualan') }}" class="nav-link {{ request()->is('laporan_penjualan*') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                {{-- <p>Penjualan Bulanan</p> --}}
                                <p>Total Pendapatan Harian</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('preorder.index') }}" class="nav-link {{ request()->is('preorder*') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p>Pemesanan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('kas.laporan') }}" class="nav-link {{ request()->is('laporan_kas*') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p>Kas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('production.laporan') }}" class="nav-link {{ request()->is('laporan_produksi*') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                {{-- <p>Stok</p> --}}
                                <p>Pergerakan Stok Produksi</p>
                            </a>
                        </li>
                    </ul>
                </li>
                
                {{-- @role('admin') --}}
                <li class="nav-item has-treeview
                {{ request()->is('role*') ? 'menu-open' : '' }}
                {{ request()->is('role-permission*') ? 'menu-open' : '' }}
                {{ request()->is('users*') ? 'menu-open' : '' }}
                {{ request()->is('default-avatar') ? 'menu-open' : '' }}
                ">
                    <a href="#" class="nav-link 
                    {{ request()->is('role*') ? 'active' : '' }}
                    {{ request()->is('role-permission*') ? 'active' : '' }}
                    {{ request()->is('users*') ? 'active' : '' }}
                    {{ request()->is('default-avatar') ? 'active' : '' }}
                    ">
                        <i class="nav-icon fa fa-users"></i>
                        <p>
                            Manajemen Users
                            <i class="right fa fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        {{-- <li class="nav-item">
                            <a href="{{ route('role.index') }}" class="nav-link {{ request()->is('role') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p>Role</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('users.roles_permission') }}" class="nav-link {{ request()->is('role-permission*') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p>Role Permission</p>
                            </a>
                        </li> --}}
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p>Users</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('users.avatar') }}" class="nav-link {{ request()->is('default-avatar') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p>Set Default Avatar</p>
                            </a>
                        </li>
                    </ul>
                </li>
                {{-- @endrole    --}}

                <li class="nav-item has-treeview">
                    <a class="nav-link" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                        <i class="nav-icon fa fa-sign-out"></i>
                        <p>
                            {{ __('Logout') }}
                        </p>
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>