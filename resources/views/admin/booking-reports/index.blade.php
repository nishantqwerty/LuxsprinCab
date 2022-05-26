@extends('layouts.header')

@section('content')
    <link href="{{ asset('assets/css/components.min.css') }}" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="{{ asset('assets/js/jquery.min.js') }}"></script>
    {{-- <script type="text/javascript" src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script> --}}
    <script type="text/javascript" src="{{ asset('assets/js/echarts.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.3/moment-with-locales.min.js"
        integrity="sha512-vFABRuf5oGUaztndx4KoAEUVQnOvAIFs59y4tO0DILGWhQiFnFHiR+ZJfxLDyJlXgeut9Z07Svuvm+1Jv89w5g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.js"
        integrity="sha512-mh+AjlD3nxImTUGisMpHXW03gE6F4WdQyvuFRkjecwuWLwD2yCijw4tKA3NsEFpA1C3neiKhGXPSIGSfCYPMlQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"
        integrity="sha512-BkpSL20WETFylMrcirBahHfSnY++H2O1W+UnEEO4yNIl+jI2+zowyoGJpbtk6bx97fBXf++WJHSSK2MV4ghPcg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Booking Reports</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Booking Reports</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <form action="{{ route('booking-reports-date') }}" method="post">
            @csrf
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-2">
                            <input type="date" id="start_date" name="start_date"
                                value="{{ isset($start_date) ? $start_date : 'dd-mm-yyyy' }}">
                            {{-- <input type="text" id="daterange_textbox" name="date" class="form-control" readonly> --}}
                        </div>
                        <div class="col-md-2">
                            <input type="date" id="end_date" name="end_date"
                                value="{{ isset($end_date) ? $end_date : 'dd-mm-yyyy' }}">
                        </div>
                        <div class="col-md-2">
                            <select name="ride_category" id="">
                                @if (isset($ride_category))
                                    <option value="private" @if ($ride_category == 'private') selected @endif>Private
                                    </option>
                                    <option value="sharing" @if ($ride_category == 'sharing') selected @endif>Sharing
                                    </option>
                                @else
                                    <option value="private">Private</option>
                                    <option value="sharing">Sharing</option>
                                @endif
                            </select>
                        </div>
                        {{-- <a class="btn btn-primary" href="{{ route('booking-reports') }}" role="button">Submit</a> --}}
                        <input type="submit" name="" id="">
                        <!-- Small boxes (Stat box) -->
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-6">
                            <!-- small box -->
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>
                                        {{ $total_booking }}
                                    </h3>

                                    <p>Total Bookings</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                                {{-- <a href="{{ route('users') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> --}}
                            </div>
                        </div>
                        <div class="col-lg-4 col-6">
                            <!-- small box -->
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>
                                        {{ $completed_booking }}
                                    </h3>

                                    <p>Completed Bookings</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person"></i>
                                </div>
                                {{-- <a href="{{ route('drivers') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> --}}
                            </div>
                        </div>

                        <div class="col-lg-4 col-6">
                            <!-- small box -->
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>
                                        {{ $cancelled_booking }}
                                    </h3>

                                    <p>Cancelled Booking</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person"></i>
                                </div>
                                {{-- <a href="{{ route('drivers') }}" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a> --}}
                            </div>
                        </div>

                        {{-- <div class="chart has-fixed-height" id="bars_basic"></div> --}}
                        <div class="chart has-fixed-height" id="pie_basic"></div>
                    </div>
                    <!-- /.row (main row) -->
                    <div class="row">
                        <div class="panel-body">
                        </div>

                    </div>
                </div><!-- /.container-fluid -->
            </section>
        </form>
        <!-- /.content -->
    </div>
    <script>
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() + 1; //January is 0!
        var yyyy = today.getFullYear();
        if (dd < 10) {
            dd = '0' + dd
        }
        if (mm < 10) {
            mm = '0' + mm
        }

        today = yyyy + '-' + mm + '-' + dd;
        document.getElementById("start_date").setAttribute("max", today);
        document.getElementById("end_date").setAttribute("max", today);
    </script>

    <script type="text/javascript">
        var pie_basic_element = document.getElementById('pie_basic');
        if (pie_basic_element) {
            var pie_basic = echarts.init(pie_basic_element);
            pie_basic.setOption({

                textStyle: {
                    fontFamily: 'Roboto, Arial, Verdana, sans-serif',
                    fontSize: 13
                },

                title: {
                    text: 'Booking Data',
                    left: 'center',
                    textStyle: {
                        fontSize: 17,
                        fontWeight: 500
                    },
                    subtextStyle: {
                        fontSize: 12
                    }
                },

                tooltip: {
                    trigger: 'item',
                    backgroundColor: 'rgba(0,0,0,0.75)',
                    padding: [10, 15],
                    textStyle: {
                        fontSize: 13,
                        fontFamily: 'Roboto, sans-serif'
                    },
                    formatter: "{a} <br/>{b}: {c} ({d}%)"
                },

                legend: {
                    orient: 'horizontal',
                    bottom: '0%',
                    left: 'center',
                    data: ['Total Booking', 'Completed Booking', 'Cancelled Booking'],
                    itemHeight: 8,
                    itemWidth: 8
                },

                series: [{
                    name: '',
                    type: 'pie',
                    radius: '70%',
                    center: ['50%', '50%'],
                    itemStyle: {
                        normal: {
                            borderWidth: 1,
                            borderColor: '#fff'
                        }
                    },
                    data: [{
                            value: {{ $total_booking }},
                            name: 'Total Booking'
                        },
                        {
                            value: {{ $completed_booking }},
                            name: 'Completed Booking'
                        },
                        {
                            value: {{ $cancelled_booking }},
                            name: 'Cancelled Booking'
                        }
                    ]
                }]
            });
        }
    </script>
    <!-- /.content-wrapper -->
@endsection
