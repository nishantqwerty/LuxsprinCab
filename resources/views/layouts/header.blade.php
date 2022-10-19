<!DOCTYPE html>
<html lang="en">

<head>

    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #2196F3;
        }

        img.portimg {
            display: none;
            width: 20%;
            height: 20%;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME') }}</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- JQVMap -->
    {{-- <link rel="stylesheet" href="{{ asset('plugins/jqvmap/jqvmap.min.css') }}"> --}}
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">
</head>

<body class="hold-transition sidebar-mini layout-fixed">

    <div class="wrapper">

        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="AdminLTELogo"
                height="60" width="60">
        </div>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                @include('flash-message')
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('dashboard') }}" class="brand-link">
                <img src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
                    class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">{{ env('APP_NAME') }}</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        @if (!empty(Auth::user()->image))
                            <img src="{{ asset('storage/images/' . Auth::user()->image) }}"
                                class="img-circle elevation-2" alt="User Image">
                        @else
                            <img src="{{ asset('dist/img/avatar.png') }}" class="img-circle elevation-2"
                                alt="User Image">
                        @endif
                    </div>
                    <div class="info">
                        <a href="{{ route('profile') }}" class="d-block">{{ Auth::user()->name }}</a>
                    </div>
                </div>

                <!-- SidebarSearch Form -->
                <div class="form-inline">
                    <div class="input-group" data-widget="sidebar-search">
                        <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                            aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-sidebar">
                                <i class="fas fa-search fa-fw"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                        <li class="nav-item menu-open">
                            @if (Request::Segment(2) == 'dashboard')
                                <a href="{{ route('dashboard') }}" class="nav-link active">
                                @else
                                    <a href="{{ route('dashboard') }}" class="nav-link">
                            @endif
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Dashboard
                            </p>
                            </a>
                        </li>
                        <li class="nav-item menu-open">
                            @if ((Request::Segment(2) == 'users' && (Request::Segment(3) == '' || Request::Segment(3) == 'view' || Request::Segment(3) == 'edit')) || Request::Segment(2) == 'user-promotion')
                                <a href="{{ route('users') }}" class="nav-link active ">
                                @else
                                    <a href="{{ route('users') }}" class="nav-link ">
                            @endif
                            <i class="nav-icon fas fa-user"></i>
                            <p>
                                Users Section
                            </p>
                            </a>
                        </li>

                        <li class="nav-item menu-open">
                            @if (Request::Segment(2) == 'drivers')
                                <a href="{{ route('drivers') }}" class="nav-link active ">
                                @else
                                    <a href="{{ route('drivers') }}" class="nav-link ">
                            @endif
                            <i class="nav-icon fas fa-user"></i>
                            <p>
                                Drivers Section
                            </p>
                            </a>
                        </li>

                        <li class="nav-item menu-open">
                            @if (Request::Segment(2) == 'car-fare')
                                <a href="{{ route('car-fare') }}" class="nav-link active ">
                                @else
                                    <a href="{{ route('car-fare') }}" class="nav-link ">
                            @endif
                            <i class="nav-icon fas fa-user"></i>
                            <p>
                                Car Fare
                            </p>
                            </a>
                        </li>

                        {{-- <li class="nav-item menu-open">
                            @if (Request::Segment(2) == 'route-stops')
                                <a href="{{ route('route-stops') }}" class="nav-link active ">
                                @else
                                    <a href="{{ route('route-stops') }}" class="nav-link ">
                            @endif
                            <i class="nav-icon fas fa-user"></i>
                            <p>
                                Routes and Stops
                            </p>
                            </a>
                        </li> --}}

                        <li class="nav-item menu-open">
                            @if (Request::Segment(2) == 'messages')
                                <a href="{{ route('messages') }}" class="nav-link active ">
                                @else
                                    <a href="{{ route('messages') }}" class="nav-link ">
                            @endif
                            <i class="nav-icon fas fa-user"></i>
                            <p>
                                Chat
                            </p>
                            </a>
                        </li>

                        <li class="nav-item menu-open">
                            @if (Request::Segment(2) == 'support')
                                <a href="{{ route('support') }}" class="nav-link active ">
                                @else
                                    <a href="{{ route('support') }}" class="nav-link ">
                            @endif
                            <i class="nav-icon fas fa-user"></i>
                            <p>
                                Support Messages
                            </p>
                            </a>
                        </li>

                        <li class="nav-item menu-open">
                            @if (Request::Segment(2) == 'cancellation')
                                <a href="{{ route('cancellation') }}" class="nav-link active ">
                                @else
                                    <a href="{{ route('cancellation') }}" class="nav-link ">
                            @endif
                            <i class="nav-icon fas fa-user"></i>
                            <p>
                                Cancellation Messages
                            </p>
                            </a>
                        </li>

                        <li class="nav-item menu-open">
                            @if (Request::Segment(2) == 'booking-reports')
                                <a href="{{ route('booking-reports') }}" class="nav-link active ">
                                @else
                                    <a href="{{ route('booking-reports') }}" class="nav-link ">
                            @endif
                            <i class="nav-icon fas fa-user"></i>
                            <p>
                                Booking Reports
                            </p>
                            </a>
                        </li>

                        <li class="nav-item menu-open">
                            @if (Request::Segment(2) == 'transaction-reports')
                                <a href="{{ route('transaction-reports') }}" class="nav-link active ">
                                @else
                                    <a href="{{ route('transaction-reports') }}" class="nav-link ">
                            @endif
                            <i class="nav-icon fas fa-user"></i>
                            <p>
                                Transaction Reports
                            </p>
                            </a>
                        </li>

                        <li class="nav-item menu-open">
                            @if (Request::Segment(2) == 'transaction')
                                <a href="{{ route('transaction') }}" class="nav-link active ">
                                @else
                                    <a href="{{ route('transaction') }}" class="nav-link ">
                            @endif
                            <i class="nav-icon fas fa-user"></i>
                            <p>
                                Transactions
                            </p>
                            </a>
                        </li>

                        <li class="nav-item menu-open">
                            @if (Request::Segment(2) == 'panic')
                                <a href="{{ route('panic') }}" class="nav-link active ">
                                @else
                                    <a href="{{ route('panic') }}" class="nav-link ">
                            @endif
                            <i class="nav-icon fas fa-user"></i>
                            <p>
                                Panic Management
                            </p>
                            </a>
                        </li>

                        <li class="nav-item menu-open">
                            @if (Request::Segment(2) == 'promo')
                                <a href="{{ route('promo') }}" class="nav-link active ">
                                @else
                                    <a href="{{ route('promo') }}" class="nav-link ">
                            @endif
                            <i class="nav-icon fas fa-user"></i>
                            <p>
                                Promo Code Management
                            </p>
                            </a>
                        </li>

                        <li class="nav-item menu-open">
                            <a href="{{ route('logout') }}" class="nav-link ">
                                <i class="nav-icon fa fa-power-off"></i>
                                <p>
                                    Logout
                                </p>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css"
            media="screen">
        <script src="//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js"></script>
        <script src="https://cdn.ckeditor.com/4.13.0/standard/ckeditor.js"></script>
        @yield('content')
        {{-- <script>
    imgInp.onchange = evt => {
      const [file] = imgInp.files
      if (file) {
        blah.src = URL.createObjectURL(file)
      }
    }
  </script> --}}

        <script>
            function readURL() {
                var $input = $(this);
                var $newinput = $(this).parent().parent().parent().find('.portimg ');
                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        reset($newinput.next('.delbtn'), true);
                        $newinput.attr('src', e.target.result).show();
                        $newinput.after('<i class="fas fa-trash-alt delbtn removebtn"></i>');
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            }
            $(".fileUpload").change(readURL);
            $("form").on('click', '.delbtn', function(e) {
                reset($(this));
            });

            function reset(elm, prserveFileName) {
                if (elm && elm.length > 0) {
                    var $input = elm;
                    $input.prev('.portimg').attr('src', '').hide();
                    if (!prserveFileName) {
                        $($input).parent().parent().parent().find('input.fileUpload ').val("");
                        //input.fileUpload and input#uploadre both need to empty values for particular div
                    }
                    elm.remove();
                }
            }
        </script>

        <script type="text/javascript">
            //jquery fancybox popup Images example
            $(".fancybox").fancybox({
                openEffect: "none",
                closeEffect: "none",
            });
        </script>
        <footer class="main-footer">
            <strong>Copyright &copy; <?php echo date('Y'); ?>&nbsp;<a
                    href="{{ env('APP_URL') }}">{{ env('APP_NAME') }}</a>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                {{-- <b>Version</b> 3.2.0 --}}
            </div>
        </footer>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- ChartJS -->
    <script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
    <!-- Sparkline -->
    <script src="{{ asset('plugins/sparklines/sparkline.js') }}"></script>
    <!-- JQVMap -->
    {{-- <script src="{{ asset('plugins/jqvmap/jquery.vmap.min.js') }}"></script>
<script src="{{ asset('plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script> --}}
    <!-- jQuery Knob Chart -->
    <script src="{{ asset('plugins/jquery-knob/jquery.knob.min.js') }}"></script>
    <!-- daterangepicker -->
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <!-- Summernote -->
    <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('dist/js/demo.js') }}"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="{{ asset('dist/js/pages/dashboard.js') }}"></script>
</body>

</html>
