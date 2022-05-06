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
                            <th ><strong> Documents: </strong></th>
                            <th ><strong> Validate: </strong></th>
                            <th ><strong> Status: </strong></th>
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
                            <td> <a href="{{ route('view-documents',['id' => $user->id]) }}">View Documents</a> </td>
                            <td>
                              @if($user->is_validated == DRIVER_UNDER_VERIFICATION)
                                <a href="{{ route('accept-reject',['id' => $user->id,'status' => DRIVER_APPROVED]) }}"><i class="fa fa-check" aria-hidden="true" style="color:green" title="Approve"></i></a>&nbsp;&#47;&nbsp;
                                <a href="{{ route('reject',['id' => $user->id,'status' => DRIVER_REJECTED]) }}"><i class="fa fa-window-close" aria-hidden="true" style="color:red" title="Reject"></i></a>
                              @elseif($user->is_validated == DRIVER_APPROVED)
                                <button type="button" class="btn btn-success">Approved</button>
                              @elseif($user->is_validated == DRIVER_REJECTED)
                                <button type="button" class="btn btn-danger">Rejected</button>
                              @endif
                            </td>
                            <td>
                              @if($user->is_active == DRIVER_ACTIVE)
                                <a href="{{ route('change-driver-status',['id' => $user->id,'status' => DRIVER_INACTIVE]) }}" class="btn btn-success" title="Click To Deactivate">Active</a>
                              @else
                                <a href="{{ route('change-driver-status',['id' => $user->id,'status' => DRIVER_ACTIVE]) }}" class="btn btn-secondary" title="Click To Activate">In Active</a>
                              @endif 
                            </td>
                            <td>
                              <a href="{{ route('edit-driver', ['id' => $user->id]) }}"> <i class="fa fa-edit" ></i> <a>
                              <a href="{{ route('delete-driver', ['id' => $user->id]) }}"> <i class="fa fa-trash" style="font-size:20px;color:red"></i> <a>
                              <a href="{{ route('view-driver', ['id' => $user->id]) }}"> <i class="fa fa-eye" ></i> <a>
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