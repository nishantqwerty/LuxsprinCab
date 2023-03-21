@extends('layouts.header')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Message</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Messages</li>

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
                                <th><strong> User Name: </strong></th>
                                <th><strong> User role: </strong></th>
                                <th><strong> Chat: </strong></th>
                                <th><strong> Action: </strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($chats as $category)
                                <tr>

                                    <td> {{ @$category->user->name }} </td>
                                    <td>
                                        @if (@$category->user->user_role == USER)
                                            User
                                        @elseif(@$category->user->user_role == DRIVER)
                                            DRIVER
                                        @endif
                                    </td>
                                    <td> {{ @$category->message }}</td>
                                    <td>
                                        <a href="{{ route('show-chat', ['id' => $category->chat_room_id]) }}"><button
                                                class='btn btn-success'>Show Chat</button></a>
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
