<!--
=========================================================
* Material Dashboard 2 - v3.0.0
=========================================================

* Product Page: https://www.creative-tim.com/product/material-dashboard
* Copyright 2021 Creative Tim (https://www.creative-tim.com) & UPDIVISION (https://www.updivision.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by www.creative-tim.com & www.updivision.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
@props(['bodyClass'])
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets') }}/img/apple-icon.png">
    <link rel="icon" type="image/png" href="{{ asset('assets') }}/img/favicon.png">
    <title>
        UniMana
    </title>
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css"
        href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
    <!-- Nucleo Icons -->
    <link href="{{ asset('assets') }}/css/nucleo-icons.css" rel="stylesheet" />
    <link href="{{ asset('assets') }}/css/nucleo-svg.css" rel="stylesheet" />
    <!--input.css-->
    <link
        href="{{ asset('assets/css/common/input.css') }}?v={{ filemtime(public_path('assets/css/common/input.css')) }}"
        rel="stylesheet" />
    <!--spinner.css-->
    <link
        href="{{ asset('assets/css/common/spinner.css') }}?v={{ filemtime(public_path('assets/css/common/spinner.css')) }}"
        rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('assets') }}/css/material-dashboard.css?v=3.0.0" rel="stylesheet" />

    <!-- Swal -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Jquery -->
    <script src="https://code.jquery.com/jquery-3.7.0.js" integrity="sha256-JlqSTELeR4TLqP0OG9dxM7yDPqX1ox/HfgiSLBj8+kM="
        crossorigin="anonymous"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body class="{{ $bodyClass }}">
    @debug
        {{ print_r(session()->all()) }}
    @enddebug
    <div id="loading"></div>
    <!-- Modal -->
    @include('components.modals.form-modal', ['title' => ''])
    <!-- End Modal -->

    <!-- Show Modal -->
    @include('components.modals.show-modal', ['title' => ''])
    <!-- End Show Modal -->
    <script>
        (function(yourcode) {
            yourcode(window.jQuery, window, document);
        }(function($, window, document) {
            (function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            })
        }));
    </script>
    <!-- self defined script -->
    <script
        src="{{ asset('assets/models/common/userAction.js') }}?v={{ filemtime(public_path('assets/models/common/userAction.js')) }}">
    </script>
    <script
        src="{{ asset('assets/models/common/domStatus.js') }}?v={{ filemtime(public_path('assets/models/common/domStatus.js')) }}">
    </script>
    <script
        src="{{ asset('assets/models/common/domTrigger.js') }}?v={{ filemtime(public_path('assets/models/common/domTrigger.js')) }}">
    </script>
    <script
        src="{{ asset('assets/models/common/notification.js') }}?v={{ filemtime(public_path('assets/models/common/notification.js')) }}">
    </script>
    <!--spinner.css-->
    <script
        src="{{ asset('assets/models/common/spinner.js') }}?v={{ filemtime(public_path('assets/models/common/spinner.js')) }}">
    </script>
    {{ $slot }}
    <script src="{{ asset('assets') }}/js/core/popper.min.js"></script>
    <script src="{{ asset('assets') }}/js/core/bootstrap.min.js"></script>
    <script src="{{ asset('assets') }}/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="{{ asset('assets') }}/js/plugins/smooth-scrollbar.min.js"></script>
    @stack('js')

    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('assets') }}/js/material-dashboard.min.js?v=3.0.0"></script>
</body>

</html>
