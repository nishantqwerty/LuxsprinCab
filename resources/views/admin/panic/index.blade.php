@extends('layouts.header')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Panic Management</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Panics</li>

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
                    <table class="table table-striped table-hover table-reflow">
                        <thead>
                            <tr>
                                <th><strong> Booking Id: </strong></th>
                                <th><strong> User Name: </strong></th>
                                <th><strong> User Number: </strong></th>
                                <th><strong> Lat & Long: </strong></th>
                                <th><strong> Car Number: </strong></th>
                                <th><strong> Date & Time: </strong></th>
                                {{-- <th><strong> Action: </strong></th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($panic as $pnc)
                                <tr>
                                    <td> {{ $pnc->booking_id }} </td>
                                    <td> {{ $pnc->user->name }} </td>
                                    <td> {{ $pnc->user->phone_number }} </td>
                                    <td><a target="_blank" href="https://www.google.com/maps/search/?api=1&query={{ $pnc->lat.','.$pnc->long }}"> {{ $pnc->lat.' , '.$pnc->long }}</a></td>
                                    <td> {{ $pnc->booking->cardetails->car_number }} </td>
                                    <td> {{ date('Y-m-d H:i:s',strtotime($pnc->created_at)) }} </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- ./col -->
                </div>
                <!-- /.row (main row) -->
            </div><!-- /.container-fluid -->
        </section>

        <!-- /.content -->
    </div>
@endsection
