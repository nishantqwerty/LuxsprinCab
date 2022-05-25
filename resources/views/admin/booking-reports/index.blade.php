@extends('layouts.header')

@section('content')
    <link href="{{ asset('assets/css/components.min.css') }}" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
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
        <section class="content">
            <div class="container-fluid">
                <!-- Small boxes (Stat box) -->
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
                    <div class="col-md-4">
                        <input type="text" id="daterange_textbox" class="form-control" readonly>
                    </div>
                    <div class="chart has-fixed-height" id="bars_basic"></div>
                </div>
                <!-- /.row (main row) -->
                <div class="row">
                    <div class="panel-body">
                    </div>

                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <script type="text/javascript">
        $(document).ready(function() {

            fetch_data();

            var sale_chart;

            function fetch_data(start_date = '', end_date = '') {
                var dataTable = $('#bookings').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "order": [],
                    "ajax": {
                        url: "{{ route('/data') }}",
                        type: "POST",
                        data: {
                            action: 'fetch'
                        },
                        headers: {
                            'X-CSRF-Token': '{{ csrf_token() }}',
                        },
                    },
                    "drawCallback": function(settings) {
                        var sales_date = [];
                        var sale = [];

                        for (var count = 0; count < settings.aoData.length; count++) {
                            sales_date.push(settings.aoData[count]._aData[2]);
                            sale.push(parseFloat(settings.aoData[count]._aData[1]));
                        }

                        var chart_data = {
                            labels: sales_date,
                            datasets: [{
                                label: 'Sales',
                                backgroundColor: 'rgba(153, 102, 255)',
                                color: '#fff',
                                data: sale
                            }]
                        };

                        var group_chart3 = $('#bar_chart');

                        if (sale_chart) {
                            sale_chart.destroy();
                        }

                        sale_chart = new Chart(group_chart3, {
                            type: 'bar',
                            data: chart_data
                        });
                    }
                });
            }

            $('#daterange_textbox').daterangepicker({
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                },
                format: 'YYYY-MM-DD'
            }, function(start, end) {
                $('#order_Table').DataTable.destroy();
                fetch_data(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
            });
        });

        var bars_basic_element = document.getElementById('bars_basic');
        if (bars_basic_element) {
            var bars_basic = echarts.init(bars_basic_element);
            bars_basic.setOption({
                color: ['#3398DB'],
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: [{
                    type: 'category',
                    data: ['Total Bookings', 'Completed Bookings', 'Cancelled Bookings'],
                    axisTick: {
                        alignWithLabel: true
                    }
                }],
                yAxis: [{
                    type: 'value'
                }],
                series: [{
                    name: 'Total Bookings',
                    type: 'bar',
                    barWidth: '20%',
                    data: [
                        {{ $total_booking }},
                        {{ $completed_booking }},
                        {{ $cancelled_booking }}
                    ]
                }]
            });
        }
    </script>
    <!-- /.content-wrapper -->
@endsection
