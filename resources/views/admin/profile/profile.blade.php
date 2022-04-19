@extends('layouts.header')

@section('content')
<style>
    input[type=text], select {
      width: 100%;
      padding: 12px 20px;
      margin: 8px 0;
      display: inline-block;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }

    input[type=date], select {
      width: 100%;
      padding: 12px 20px;
      margin: 8px 0;
      display: inline-block;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }

    input[type=file], select {
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
            <h1 class="m-0">Profile</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
              <li class="breadcrumb-item active">Profile</li>
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
                <form action="{{ route('update-profile') }}" method="post" enctype="multipart/form-data">
                @csrf
                  <a href="{{ route('change-password') }}" class="btn btn-primary btn-lg active" style="float:right" role="button" aria-pressed="true">Change Password</a>
                  <br>
                  <label for="fname">Name</label>
                  <input type="text" name="name" value="{{ Auth::user()->name }}">
                  @if($errors->has('name'))
                    <p class="error">{{ $errors->first('name') }}</p>
                  @endif
                  
                  <label for="uname">User Name</label>
                  <input type="text" name="uname" value="{{ Auth::user()->username }}">
                  @if($errors->has('uname'))
                    <p class="error">{{ $errors->first('uname') }}</p>
                  @endif
                  
                  <label for="email">Email</label>
                  <input type="text"  name="email" value="{{ Auth::user()->email }}">
                  @if($errors->has('email'))
                    <p class="error">{{ $errors->first('email') }}</p>
                  @endif
                  
                  <label for="phone">Phone Number</label>
                  <input type="text"  name="phone_number" value="{{ Auth::user()->phone_number }}">
                  @if($errors->has('phone_number'))
                    <p class="error">{{ $errors->first('phone_number') }}</p>
                  @endif
                  
                  <label for="image">Image</label>
                  <input class="fileUpload" accept="image/jpeg, image/jpg" name="image" type="file">
                  <div class="upload-demo-wrap"><img alt="your image" class="portimg" src="#"></div>
                  
                  @if(!empty(Auth::user()->image))
                  <a class="pakainfo fancybox" rel="ligthbox" href="{{ asset('storage/images/'.Auth::user()->image) }}">
                    <img class="img-responsive infinityknow" width="150px" alt="jquery fancybox popup example" title="jquery fancybox popup example" src="{{ asset('storage/images/'.Auth::user()->image) }}" 
                  </a>
                  <a class="fancybox-button fancybox-button--delete" title="Delete" href="{{ route('delete-image',['id' => Auth::user()->id]) }}">
                    <i class="fas fa-trash-alt"></i>
                  </a>
                  @endif

                  <input type="submit" id="submit" value="Submit">
                </form>
              </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@endsection