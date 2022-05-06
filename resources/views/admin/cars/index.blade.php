@extends('layouts.header')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0">Cars Fare</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                  <li class="breadcrumb-item active">Cars Fare</li>
                </ol>
              </div><!-- /.col -->
            </div><!-- /.row -->
            <div class="row mb-2">
                <div class="col-sm-12">
                  <a class="btn btn-success" href="{{ route('add-fare') }}" role="button" style="float: right;">Add Car Fare</a> </td>
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
                            <th ><strong> Car Category: </strong></th>
                            <th ><strong> Fare: </strong></th>
                            <th ><strong> Action: </strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr>
                            <td> {{ $category->carCategory->name }} </td>
                            <td> {{ $category->fare }}/km </td>
                            <td>
                              <a href="{{ route('edit-fare', ['id' => $category->id]) }}"> <i class="fa fa-edit" ></i> <a>
                              <a href="{{ route('delete-fare', ['id' => $category->id]) }}"> <i class="fa fa-trash" style="font-size:20px;color:red"></i> <a>
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