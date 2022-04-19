@extends('layouts.header')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0">{{ $section }}</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                  <li class="breadcrumb-item active">{{ $section }}</li>
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
                            <th ><strong> Name: </strong></th>
                            <th ><strong> User Name: </strong></th>
                            <th ><strong> Email: </strong></th>
                            <th ><strong> Phone Number: </strong></th>
                            <th ><strong> Action: </strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td> {{ $user->name }} </td>
                            <td> {{ $user->username }} </td>
                            <td> {{ $user->email }} </td>
                            <td> {{ $user->phone_number }} </td>
                            <td>
                              <a href="{{ route('edit', ['id' => $user->id]) }}"> <i class="fa fa-edit" ></i> <a>
                              <a href="{{ route('delete', ['id' => $user->id]) }}"> <i class="fa fa-trash" style="font-size:20px;color:red"></i> <a>
                              <a href="{{ route('view', ['id' => $user->id]) }}"> <i class="fa fa-eye" ></i> <a>
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