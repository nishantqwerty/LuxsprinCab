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

        #messageBox {
            border: 1px solid black;
            padding: 10px;
            margin-bottom: 10px;
        }

        div.column {
            width: 100%;
            border-radius: 5px;
            background-color: #f2f2f2;
            padding: 20px;
        }

        .messageBlock {
            margin: 10px;
            border: 1px solid rgb(204, 204, 204);
            border-radius: 7px;
            background-color: rgb(226, 226, 226);
            min-height: 500px;
            padding: 20px 100px;
        }

        .one,
        .two {
            border: 1px solid lightgray;
            border-radius: 8px;
            padding: 5px;
            background-color: lightblue;
            margin-bottom: 20px;
        }

        .two {
            margin-bottom: 20px;
            margin-left: 300px;
            background-color: lightgreen;
        }

        .inputBlock {
            margin: 20px 10px;
        }

        .inputBlock form {
            display: flex;
            justify-content: space-between;
        }

        .inputBlock input {
            width: 94%;
            border: 1px solid gray;
            border-radius: 30px;
            height: 40px;
            padding: 0 10px;
            font-size: 14px;
        }

        .inputBlock button {
            border: 1px solid rgb(43, 95, 14);
            border-radius: 50%;
            background-color: rgb(119, 248, 45);
            width: 40px;
            height: 40px;
            cursor: pointer;
        }
    </style>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Chat</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Show Chat</li>
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

                <form method="POST" action="{{ route('save-chat', ['id' => $_GET['id']]) }}">
                    @csrf
                    <div class="row">
                        <div class="column">
                            <div class="container">
                                <div class="messageBlock">
                                    @foreach ($chats as $category)
                                        @if ($category->user_role == SUPER_ADMIN)
                                            <div class="two col-md-6">
                                                <td> {{ $category->message }} </td>
                                            </div>
                                        @else
                                            <div class="one col-md-6">
                                                <td> {{ $category->message }} </td>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="inputBlock">
                                    <input id="inputMsg" type="text" name="msg" placeholder="Type a message" />
                                    <button id="submitMsg" type="submit">✔</button>
                </form>
            </div>
    </div>
    </div>
    </div>
    </section>
    <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
