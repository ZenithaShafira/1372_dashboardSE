<!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/') }}">
            <img src="{{ asset('img/logo-bps.png') }}" class="logo-full" alt="Logo BPS">
            <img src="{{ asset('img/logo-bps-kecil.svg') }}" class="logo-icon" alt="Logo BPS">
        </a>

        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Dashboard -->
        <!-- <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span></a>
        </li> -->

        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Monitoring
        </div>

        <li class="nav-item">
            <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapsePML"
                aria-expanded="{{ request()->routeIs('monitoring.perpml') ? 'true' : 'false' }}" >
                    <i class="fas fa-fw fa-cog"></i>
                    <span>PER PML</span>
                </a>
                <div id="collapsePML" class="collapse {{ request()->routeIs('monitoring.perpml') ? 'show' : '' }}"
                    data-parent="#accordionSidebar">

                    <div class="bg-white py-2 collapse-inner rounded">

                        @foreach($all_pml as $pml)
                            <a class="collapse-item {{ request()->route('id') == $pml->id ? 'active' : '' }}"
                            href="{{ route('monitoring.perpml', $pml->id) }}">
                                {{ $pml->nama }}
                            </a>
                        @endforeach

                    </div>
                </div>
        </li>
    </ul>
<!-- End of Sidebar -->