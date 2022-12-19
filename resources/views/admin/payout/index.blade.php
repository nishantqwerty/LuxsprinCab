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
                            <th><strong> Driver: </strong></th>
                            <th><strong> Driver's Payment: </strong></th>
                            <th><strong> Release Payment: </strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($driver as $user)
                        <tr>
                            <td> {{ $user->user->name }} </td>
                            <td> ${{ ($user->amount) / 100 }} </td>
                            <td>
                                <button type="button" id="pay" value="{{ $user->driver_id }}" class="btn btn-primary btn-lg active pays" role="button" aria-pressed="true" data-toggle="modal" data-target="#exampleModal">Release Payment</button>
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
    <!-- Button trigger modal -->
    <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  Launch demo modal
</button> -->

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Payouts</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('payment') }}" method='POST'>
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Destinations</label>
                            <input type="text" name="Destinations" class="form-control active" placeholder="Destinations">
                            <input type="hidden" name="id" class="form-control active" value="{{ $user->driver_id }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Pay</button>
                    </div>
            </div>
            </form>
        </div>
    </div>
</div>


@endsection

<script>
    $(document).ready(function () {
        $(document).on('click', '.pays', function () {
            var id = $(this).val();
            $("#exampleModal").modal().show();
            
        })
    });
    $('#exampleModal').on('hidden.bs.modal', function(e) {
    $(this).find('form').trigger('reset');
})
</script>

