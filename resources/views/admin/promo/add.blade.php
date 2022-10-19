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
                        <h1 class="m-0">Promo Code</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Promo Code</li>
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
                        <form action="{{ route('save-promo') }}" method="post" enctype="multipart/form-data">
                            @csrf

                            <label for="title">Promo Code Name</label>
                            <input type="text" name="name" placeholder="Enter Promo Code Name"
                                @if ($errors->has('name')) <p class="error">{{ $errors->first('name') }}</p> 
                                @endif

                        <div class="row">
                            <label for="title">Discount %</label>
                            <input type="text" name="discount" placeholder="Enter Discount %"
                                value="{{ old('discount') }}">
                            @if ($errors->has('discount'))
                                <p class="error">{{ $errors->first('discount') }}</p>
                            @endif

                            <label for="title">Number of Times Promo Use</label>
                            <input type="number" name="total_number" placeholder="Enter Number"
                                value="{{ old('total_number') }}">
                            @if ($errors->has('total_number'))
                                <p class="error">{{ $errors->first('total_number') }}</p>
                            @endif

                            <label for="title">Publish Date & Time</label>
                            <input type="datetime-local" name="date" placeholder="Enter Number"
                                value="{{ old('date') }}">
                            @if ($errors->has('date'))
                                <p class="error">{{ $errors->first('date') }}</p>
                            @endif
                        </div>

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
                    '<div class="row"><div class = "col-md-12"><input type="text" name="reason[]" placeholder="Enter Cancellation Reasons" ><span class="fa fa-trash remove" id="remove_' +
                    i + '" ></span></div></div>');
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
