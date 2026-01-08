<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Networld Bangladesh PLC</title>
    <!-- core:css -->
    <link rel="stylesheet" href="{{ asset('vendors/core/core.css') }}">
    <!-- endinject -->
    <!-- plugin css for this page -->

    <!-- end plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather-font/css/iconfont.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/flag-icon-css/css/flag-icon.min.css') }}">
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/demo_1/style.css') }}">
    <!-- End layout styles -->
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>


    <style>
        .sidebar-minimized .sidebar-body .nav-link span.link-title {
            display: none;
        }

        .sidebar-minimized .sidebar-body {
            width: 70px;
            /* minimized width */
        }

        .sidebar-body .nav-link i.link-icon {
            min-width: 25px;
        }

        .nav-icon {
            width: 24px;
            /* icon width */
            height: 24px;
            /* icon height */
            object-fit: contain;
            margin-right: -21px;
            /* space between icon and text */
        }
    </style>

</head>

<body>
    <div class="main-wrapper">

        <!-- partial:../../partials/_sidebar.html -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <a href="#" class="sidebar-brand">
                    NW<span>PLC</span>
                </a>
                <div class="sidebar-toggler not-active">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
            <div class="sidebar-body">
                <ul class="nav">

                    <li class="nav-item nav-category">ENGINEER ASSIGNE REPORTS</li>
                    <li class="nav-item">
                        <a href="{{ route('report.index') }}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">ENGINEER ASSIGNE </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('engineer_logs.create') }}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">CONVINCE</span>
                        </a>
                    </li>




                    <li class="nav-item">
                        <a href="{{ route('convencecheck') }}" class="nav-link">
                            <i class="link-icon" data-feather="box"></i>
                            <span class="link-title">CONVINCE CHECK</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('service-reports.index') }}"
                            class="nav-link {{ request()->routeIs('service-reports.*') ? 'active' : '' }}">
                            <i class="link-icon fas fa-wrench"></i>
                            <span class="link-title">SERVICES</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('advances.create') }}" class="nav-link">
                            <i class="link-icon" data-feather="plus"></i>
                            <span class="link-title">ADVANCE</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('advances.list_by_name') }}" class="nav-link">
                            <i class="link-icon" data-feather="list"></i>
                            <span class="link-title">ADVANCE LIST</span>
                        </a>
                    </li>
                    @if (auth()->user()->isAdmin() || auth()->user()->isChecker() || auth()->user()->isVerify())
                        <li class="nav-item">
                            <a href="{{ route('advances.list_by_date') }}" class="nav-link">
                                <i class="link-icon" data-feather="calendar"></i>
                                <span class="link-title">ADVANCE BY DATE</span>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->isAdmin() ||
                            auth()->user()->isPblManager() ||
                            auth()->user()->isMtbManager() ||
                            auth()->user()->isEblManager() ||
                            auth()->user()->isIbblManager() ||
                            auth()->user()->isCtManager())
                        <li class="nav-item nav-category">BANK DATA</li>
                        @if (auth()->user()->isAdmin() || auth()->user()->isPblManager())
                            <li class="nav-item">

                                <a href="{{ route('pubali.index') }}" class="nav-link">

                                    <img src="{{ asset('assets/images/pubali icon.png') }}" alt="Pubali"
                                        class="nav-icon">
                                    <span class="link-title"> PUBALI</span></a>
                            </li>
                        @endif
                        @if (auth()->user()->isAdmin() || auth()->user()->isMtbManager())
                            <li class="nav-item">
                                <a href="{{ route('mtb.index') }}" class="nav-link">
                                    <img src="{{ asset('assets/images/MTB_icon.jpg') }}" alt="MTB"
                                        class="nav-icon">
                                    <span class="link-title"> MTB</span></a>
                            </li>
                        @endif
                        @if (auth()->user()->isAdmin() || auth()->user()->isEblManager())
                            <li class="nav-item">
                                <a href="{{ route('ebl.index') }}" class="nav-link">
                                    <img src="{{ asset('assets/images/EBL_icon.png') }}" alt="EBL"
                                        class="nav-icon">
                                    <span class="link-title"> EBL</span></a>
                            </li>
                        @endif
                        @if (auth()->user()->isAdmin() || auth()->user()->isIbblManager())
                            <li class="nav-item">
                                <a href="{{ route('ibbl.index') }}" class="nav-link">
                                    <img src="{{ asset('assets/images/IBBL_icon.png') }}" alt="IBBL"
                                        class="nav-icon">
                                    <span class="link-title"> IBBL</span></a>
                            </li>
                        @endif
                        @if (auth()->user()->isAdmin() || auth()->user()->isCtManager())
                            <li class="nav-item">
                                <a href="{{ route('cbl.index') }}" class="nav-link">
                                    <img src="{{ asset('assets/images/CITY_icon.png') }}" alt="CITY"
                                        class="nav-icon">
                                    <span class="link-title"> CITY</span></a>
                            </li>
                        @endif

                        @if (auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a href="{{ route('register') }}" class="nav-link">
                                    <i class="link-icon" data-feather="settings"></i>
                                    <span class="link-title"> SETTING </span></a>
                            </li>
                        @endif
                    @endif

                    <li class="nav-item">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                            style="display: inline;">
                            @csrf
                            <button type="submit" class="nav-link"
                                style="background: none; border: none; color: inherit; cursor: pointer;">
                                <icon class="link-icon" data-feather="log-out"></icon>
                                <span class="link-title"> LOG OUT </span>
                            </button>
                        </form>
                    </li>

            </div>
        </nav>
        <!-- partial -->

        <div class="page-wrapper">

            <!-- partial:../../partials/_navbar.html -->
            <nav class="navbar">
                <a href="#" class="sidebar-toggler">
                    <i data-feather="menu"></i>
                </a>
                <div class="navbar-content">
                    <form class="search-form">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text ">
                                    <h4>Networld Bangladesh PLC</h4>
                                </div>
                            </div>

                        </div>
                    </form>
                    <div class=" pt-4 w-100 text-right">
                        <h5>{{ Auth()->User()->name }}</h5>
                    </div>


                </div>
            </nav>
            <!-- partial -->
            {{-- all content page wriping  --}}
            <div class="page-content">
                <div>

                    @yield('content')

                </div>

            </div>

            <!-- Copyright footer section-->
            <footer class="footer d-flex flex-column flex-md-row align-items-center justify-content-between">
                <p class="text-muted text-center text-md-left">Copyright © 2026 <a href="">NETWORLD</a>. All
                    rights reserved</p>

            </footer>
            <!--end Copyright footer section-->


        </div>
    </div>

    <!-- core:js -->
    <script src="{{ asset('assets/vendors/core/core.js') }}"></script>

    <!-- inject:js -->
    <script src="{{ asset('assets/vendors/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/template.js') }}"></script>
    <!-- endinject -->

</body>

</html>
