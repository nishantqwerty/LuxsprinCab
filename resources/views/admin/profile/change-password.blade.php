@extends('layouts.header')

@section('content')
<style>
  .error{
    color: red;
  }
    input[type=password], select {
      width: 100%;
      padding: 12px 20px;
      margin: 8px 0;
      display: inline-block;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }
    
    input[type=submit] {
      width: 100%;
      background-color: #4CAF50;
      color: white;
      padding: 14px 20px;
      margin: 8px 0;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    
    input[type=submit]:hover {
      background-color: #45a049;
    }
    
    div.column {
      border-radius: 5px;
      background-color: #f2f2f2;
      padding: 20px;
    }
    </style>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Change Password</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
              <li class="breadcrumb-item active">Change Password</li>
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
            <div class="column">
                <form action="{{ route('save-password') }}" method="post" enctype="multipart/form-data">
                @csrf
                  <label for="fname">Old Password</label>
                  <input type="password" name="old_pass" placeholder="Enter your old password">
                  @if($errors->has('old_pass'))
                    <p class="error">{{ $errors->first('old_pass') }}</p>
                  @endif
                  
                  <label for="lname">New Password</label>
                  <input type="password" name="password" placeholder="Enter new password">
                  @if($errors->has('password'))
                    <p class="error">{{ $errors->first('password') }}</p>
                  @endif
                  
                  <label for="uname">Confirm New Password</label>
                  <input type="password" name="confirm_pass" placeholder="Confirm your new password">
                  @if($errors->has('confirm_pass'))
                    <p class="error">{{ $errors->first('confirm_pass') }}</p>
                  @endif
                  
                  <input type="submit" value="Submit">
                </form>
              </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  @endsection
