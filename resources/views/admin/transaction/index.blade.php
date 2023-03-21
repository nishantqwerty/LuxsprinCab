@extends('layouts.header')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Transaction Management</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Transactions</li>

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
                                <th><strong> User: </strong></th>
                                <th><strong> Amount: </strong></th>
                                <th><strong> Status: </strong></th>
                                <th><strong> Payment Mode: </strong></th>
                                <th><strong> Refund Status: </strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $transaction)
                                <tr>
                                    <td> {{ isset($transaction->user->name) ? $transaction->user->name : "" }} </td>
                                    <td> ${{ ($transaction->amount)/100 }} </td>
                                    <td> {{ $transaction->status }} </td>
                                    <td> {{ $transaction->payment_mode }} </td>
                                    <td>
                                        @if($transaction->is_refunded == 0)
                                            <a href="{{ route('refund',['id' => $transaction->charge_id]) }}" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Refund</a>
                                        @elseif($transaction->is_refunded == 1)
                                        <button type="button" class="btn btn-secondary">Refunded</button>
                                        @endif
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
