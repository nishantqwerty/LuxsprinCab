@extends('layouts.header')

@section('content')
<style>
    * {
      box-sizing: border-box;
    }
    
    .row {
      display: flex;
      margin-left:-5px;
      margin-right:-5px;
    }
    
    .column {
      flex: 50%;
      padding: 5px;
    }
    
    table {
      border-collapse: collapse;
      border-spacing: 0;
      width: 100%;
      border: 1px solid #ddd;
    }
    
    th, td {
      text-align: left;
      padding: 16px;
    }
    
    tr:nth-child(even) {
      background-color: #f2f2f2;
    }
    </style>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">View User</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
              <li class="breadcrumb-item active">View User</li>
            </ol>
          </div>
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="column" >
                <table>
                    <tr><th style="text-align: right;">Name</th></tr>
                    <tr><th style="text-align: right;">Email</th></tr>
                    <tr><th style="text-align: right;">Phone Number</th></tr>
                    <tr><th style="text-align: right;">Status</th></tr>
                    <tr><th style="text-align: right;">Image</th></tr>
                </table>
            </div>
            <div class="column" >
                <table>
                <tr>
                    <th >{{ !empty($user->name) ? $user->name : '' }}</th>
                </tr>
                <tr>
                    <th >{{ !empty($user->email) ? $user->email : '' }}</th>
                </tr>
                <tr>
                    <th >{{ !empty($user->phone_number) ? $user->phone_number : 0 }}</th>
                </tr>
                <tr>
                    <th >{{ ($user->is_active == USER_ACTIVE) ? 'ACTIVE' : "INACTIVE"  }}</th>
                </tr>
                <tr>
                    <th >
                      @if(!empty($user->image))
                        <a class="pakainfo fancybox" rel="ligthbox" href="{{ asset('/storage/images/'.$user->image) }}">
                          <img class="img-responsive infinityknow" width="15%" alt="jquery fancybox popup example" title="jquery fancybox popup example" src="{{ asset('storage/images/'.$user->image) }}" 
                        </a>
                      @endif
                    </th>
                </tr>
                </table>
            </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  @endsection