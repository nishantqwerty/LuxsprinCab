@extends('layouts.header')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0">Route</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                  <li class="breadcrumb-item active">Route</li>
                </ol>
              </div><!-- /.col -->
            </div><!-- /.row -->
            <div class="row mb-2">
                <div class="col-sm-12">
                  <a class="btn btn-success" href="{{ route('add-promo') }}" role="button" style="float: right;">Add Route & Stops</a> </td>
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
                            <th ><strong> Promo Name: </strong></th>
                            <th ><strong> Discount %: </strong></th>
                            <th ><strong> Date Time: </strong></th>
                            <th ><strong> Redmeed: </strong></th>
                            <th ><strong> Action: </strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @foreach($routes as $route)
                        <tr>
                            <td> {{ $route->name }} </td>
                            <td> {{ $route->source }} </td>
                            <td> {{ $route->destination }}</td>
                            <td>
                              <a href="{{ route('edit-routes', ['id' => $route->id]) }}"> <i class="fa fa-edit" ></i> <a>
                              <a href="{{ route('delete-routes', ['id' => $route->id]) }}"> <i class="fa fa-trash" style="font-size:20px;color:red"></i> <a>
                              {{-- <a href="{{ route('view-fare', ['id' => $category->id]) }}"> <i class="fa fa-eye" ></i> <a> --}}
                            </td>
                        </tr>
                        @endforeach --}}
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