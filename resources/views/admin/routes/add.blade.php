@extends('layouts.header')

@section('content')
    <style>
        .error {
            color: red;
        }

        input[type=textarea],
        select {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type=text],
        select {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type=file],
        select {
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
            width: 100%;
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
                        <h1 class="m-0">Add Route & Stops</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Add Route & Stops</li>
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
                        <form action="{{ route('save-routes') }}" method="post" enctype="multipart/form-data">
                            @csrf

                            <label for="title">Route Name</label>
                            <input type="text" name="name" placeholder="Enter Route Name" value="{{ old('name') }}">
                            @if ($errors->has('name'))
                                <p class="error">{{ $errors->first('name') }}</p>
                            @endif
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="title">Source</label>
                                    <input type="text" name="source" placeholder="Enter Source"
                                        value="{{ old('source') }}">
                                    @if ($errors->has('source'))
                                        <p class="error">{{ $errors->first('source') }}</p>
                                    @endif
                                </div>

                                {{-- <div class="col-md-4">
                            <label for="title">Destination</label>
                            <input type="text" name="dest" placeholder="Enter Destination" value="{{ old('dest') }}">
                            @if ($errors->has('dest'))
                            <p class="error">{{ $errors->first('dest') }}</p>
                            @endif
                        </div> --}}
                                <div class="col-md-4">
                                    <label for="title">Source Latitude</label>
                                    <input type="text" name="source_lat" placeholder="Latitude"
                                        value="{{ old('source_lat') }}">
                                    @if ($errors->has('source_lat'))
                                        <p class="error">{{ $errors->first('source_lat') }}</p>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <label for="title">Source Longitude</label>
                                    <input type="text" name="source_long" placeholder="Longitude"
                                        value="{{ old('source_long') }}">
                                    @if ($errors->has('source_long'))
                                        <p class="error">{{ $errors->first('source_long') }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="title">Destination</label>
                                    <input type="text" name="dest" placeholder="Enter Destination"
                                        value="{{ old('dest') }}">
                                    @if ($errors->has('dest'))
                                        <p class="error">{{ $errors->first('dest') }}</p>
                                    @endif
                                </div>

                                <div class="col-md-4">
                                    <label for="title">Destination Latitude</label>
                                    <input type="text" name="dest_lat" placeholder="Latitude"
                                        value="{{ old('dest_lat') }}">
                                    @if ($errors->has('dest_lat'))
                                        <p class="error">{{ $errors->first('dest_lat') }}</p>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <label for="title"> Destination Longitude</label>
                                    <input type="text" name="dest_long" placeholder="Longitude"
                                        value="{{ old('dest_long') }}">
                                    @if ($errors->has('dest_long'))
                                        <p class="error">{{ $errors->first('dest_long') }}</p>
                                    @endif
                                </div>
                            </div>
                            <h2>Stops</h2>
                            <div class="row-add"></div>
                            <a href="#" id="addBtn"> Add New Stop</i> <a>
                                    <input type="submit" value="Submit">
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <script>
        $(document).ready(function() {
            var i = 1;
            $('#addBtn').click(function() {
                $('.row-add').append(
                    '<div class="row"><div class="col-md-4"><label for="title">Stop Name</label><input type="text" name="stop_name[]" placeholder="Enter stop name" ></div><div class="col-md-4"><label for="title">Address</label><input type="text" name="stop_address[]" placeholder="Enter Address"></div><div class="col-md-2"><label for="title">Latitude</label><input type="text" name="stop_lat[]" placeholder="Latitude"></div><div class="col-md-2"><label for="title">Longitude</label><input type="text" name="stop_long[]" placeholder="Longitude"></div><span class="fa fa-trash remove" id="remove_' +
                    i + '" ></span></div>');
                i++;
            });

            $(document).on('click', '.remove', function() {
                var id = $(this).closest('span').attr('id');
                // alert(id);
                $('#' + id).closest('div').remove();
            });
        });
    </script>
@endsection
