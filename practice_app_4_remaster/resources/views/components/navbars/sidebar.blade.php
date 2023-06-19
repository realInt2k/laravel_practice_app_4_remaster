@props(['activePage'])

<aside
    class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0 d-flex text-wrap align-items-center" href=" {{ route('dashboard') }} ">
            <img src="{{ asset('assets') }}/img/logo-ct.png" class="navbar-brand-img h-100" alt="main_logo">
            <span class="ms-2 font-weight-bold text-white">Uni Mana</span>
        </a>
    </div>
    <hr class="horizontal light mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto  max-height-vh-100" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Control Panel</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'user-profile' ? 'active bg-gradient-primary' : '' }} "
                    href="{{ route('user-profile') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i style="font-size: 1.2rem;" class="fas fa-user-circle ps-2 pe-2 text-center"></i>
                    </div>
                    <span class="nav-link-text ms-1">User Profile</span>
                </a>
            </li>
            @adminOnly()
                <li class="nav-item">
                    <a class="nav-link text-white {{ $activePage == 'user-management' ? ' active bg-gradient-primary' : '' }} "
                        href="{{ route('users.index') }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i style="font-size: 1rem;" class="fas fa-lg fa-list-ul ps-2 pe-2 text-center"></i>
                        </div>
                        <span class="nav-link-text ms-1">User Management</span>
                    </a>
                </li>
            @endadminOnly

            @adminOnly()
                <li class="nav-item">
                    <a class="nav-link text-white {{ $activePage == 'product-management' ? ' active bg-gradient-primary' : '' }} "
                        href="{{ route('products.index') }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i style="font-size: 1rem;" class="fas fa-lg fa-list-ul ps-2 pe-2 text-center"></i>
                        </div>
                        <span class="nav-link-text ms-1">Product Management</span>
                    </a>
                </li>
            @else
                <li class="nav-item">
                    <a class="nav-link text-white {{ $activePage == 'product-management' ? ' active bg-gradient-primary' : '' }} "
                        href="{{ route('products.index') }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i style="font-size: 1rem;" class="fas fa-lg fa-list-ul ps-2 pe-2 text-center"></i>
                        </div>
                        <span class="nav-link-text ms-1">Browse Product</span>
                    </a>
                </li>
            @endadminOnly

            @adminOnly()
                <li class="nav-item">
                    <a class="nav-link text-white {{ $activePage == 'role-management' ? ' active bg-gradient-primary' : '' }} "
                        href="{{ route('roles.index') }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i style="font-size: 1rem;" class="fas fa-lg fa-list-ul ps-2 pe-2 text-center"></i>
                        </div>
                        <span class="nav-link-text ms-1">Role Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ $activePage == 'category-management' ? ' active bg-gradient-primary' : '' }} "
                        href="{{ route('categories.index') }}">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i style="font-size: 1rem;" class="fas fa-lg fa-list-ul ps-2 pe-2 text-center"></i>
                        </div>
                        <span class="nav-link-text ms-1">Category Management</span>
                    </a>
                </li>
            @endadminOnly
        </ul>
    </div>
    <div class="sidenav-footer position-absolute w-100 bottom-0 ">
        <div class="mx-3">
            <a class="btn bg-gradient-primary w-100" href="../../documentation/getting-started/installation.html"
                target="_blank">View documentation</a>
        </div>
    </div>
</aside>
