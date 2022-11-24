@extends('layouts.header')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Cancellation Section</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Cancellation Section</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
                <div class="row mb-2">
                    <div class="col-sm-12">
                        <a class="btn btn-success" href="{{ route('add-route-message') }}" role="button"
                            style="float: right;">Add Message</a> </td>
                    </div>
                </div>
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
                                <th><strong> Reasons: </strong></th>
                                <th><strong> Action: </strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reasons as $reason)
                                <tr>
                                    <td> {{ $reason->messages }} </td>
                                    <td>
                                        <a href="{{ route('edit-route-message', ['id' => $reason->id]) }}"> <i
                                                class="fa fa-edit"></i> <a>
                                                <a href="{{ route('delete-route-message', ['id' => $reason->id]) }}"> <i
                                                        class="fa fa-trash" style="font-size:20px;color:red"></i> <a>
                                                        {{-- <a href="{{ route('view-fare', ['id' => $category->id]) }}"> <i class="fa fa-eye" ></i> <a> --}}
                                    </td>
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
