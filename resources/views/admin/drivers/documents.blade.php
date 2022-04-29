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
            <h1 class="m-0">Documents</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
              <li class="breadcrumb-item active">Documents</li>
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
          <h3 style="font-weight: bold;">Car Details:</h3>
        <div class="row">
            <div class="column" >
                <table>
                    <tr><th style="text-align: right;">Car Brand:</th></tr>
                    <tr><th style="text-align: right;">Brand Model</th></tr>
                    <tr><th style="text-align: right;">Model Year</th></tr>
                    <tr><th style="text-align: right;">Color</th></tr>
                    <tr><th style="text-align: right;">Car Number</th></tr>
                    <tr><th style="text-align: right;">Capacity</th></tr>
                    <tr><th style="text-align: right;">VIN Number</th></tr>
                </table>
            </div>
            <div class="column" >
                <table>
                <tr>
                    <th >{{ !empty($car_detail->brand) ? $car_detail->brand : '' }}</th>
                </tr>
                <tr>
                    <th >{{ !empty($car_detail->brand_model) ? $car_detail->brand_model : '' }}</th>
                </tr>
                <tr>
                    <th >{{ !empty($car_detail->model_year) ? $car_detail->model_year : 0 }}</th>
                </tr>
                <tr>
                    <th >{{ !empty($car_detail->color) ? $car_detail->color : '' }}</th>
                </tr>
                <tr>
                    <th >{{ !empty($car_detail->car_number) ? $car_detail->car_number : '' }}</th>
                </tr>
                <tr>
                    <th >{{ !empty($car_detail->capacity) ? $car_detail->capacity . "Seat" : '' }}</th>
                </tr>
                <tr>
                    <th >{{ !empty($car_detail->vin) ? $car_detail->vin : '' }}</th>
                </tr>
                </table>
            </div>
        </div>
        <h3 style="font-weight:bold; ">Car Documents:</h3>
        <div class="row">
            <div class="column" >
                <table>
                    <tr><th style="text-align: right;">License Number:</th></tr>
                    <tr><th style="text-align: right;">License Expiry Date:</th></tr>
                    <tr><th style="text-align: right;">License Front Side:</th></tr>
                    <tr><th style="text-align: right;">License Back Side:</th></tr>
                    <tr><th style="text-align: right;">Car Insurance Number:</th></tr>
                    <tr><th style="text-align: right;">Car Insurance Expiry Date:</th></tr>
                    <tr><th style="text-align: right;">Car Insurance Image:</th></tr>
                    <tr><th style="text-align: right;">Car Registeration Number:</th></tr>
                    <tr><th style="text-align: right;">Car Registeration Expiry Date:</th></tr>
                    <tr><th style="text-align: right;">Car Registeration Image:</th></tr>
                    <tr><th style="text-align: right;">Car Inspection Date:</th></tr>
                    <tr><th style="text-align: right;">Car Inspection Image:</th></tr>
                </table>
            </div>
            <div class="column" >
                <table>
                <tr>
                    <th >{{ !empty($document->license_number) ? $document->license_number : '' }}</th>
                </tr>
                <tr>
                    <th >{{ !empty($document->expiry_date) ? date('d-m-Y',strtotime($document->expiry_date)) : '' }}</th>
                </tr>
                <tr>
                    <th >
                        @if(!empty($document->license_front_side))
                          <a class="pakainfo fancybox" rel="ligthbox" href="{{asset("storage/license_front/$document->license_front_side") }}">
                            <img class="img-responsive infinityknow" width="15%" alt="jquery fancybox popup example" title="jquery fancybox popup example" src="{{ asset("storage/license_front/$document->license_front_side") }}" 
                          </a>
                        @endif
                      </th>
                </tr>
                <tr>
                    <th >
                        @if(!empty($document->license_back_side))
                          <a class="pakainfo fancybox" rel="ligthbox" href="{{asset("storage/license_back/$document->license_back_side") }}">
                            <img class="img-responsive infinityknow" width="15%" alt="jquery fancybox popup example" title="jquery fancybox popup example" src="{{ asset("storage/license_back/$document->license_back_side") }}" 
                          </a>
                        @endif
                      </th>
                </tr>
                <tr>
                    <th >{{ !empty($document->insurance_number) ? $document->insurance_number : '' }}</th>
                </tr>
                <tr>
                    <th >{{ !empty($document->insurance_expiry_date) ? date('d-m-Y',strtotime($document->insurance_expiry_date)) : '' }}</th>
                </tr>
                <tr>
                    <th >
                        @if(!empty($document->insurance_image))
                          <a class="pakainfo fancybox" rel="ligthbox" href="{{asset("storage/insurance_image/$document->insurance_image") }}">
                            <img class="img-responsive infinityknow" width="15%" alt="jquery fancybox popup example" title="jquery fancybox popup example" src="{{ asset("storage/license_back/$document->insurance_image") }}" 
                          </a>
                        @endif
                      </th>
                </tr>
                <tr>
                    <th >{{ !empty($document->car_registeration) ? $document->car_registeration : '' }}</th>
                </tr>
                <tr>
                    <th >{{ !empty($document->car_registeration_expiry_date) ? date('d-m-Y',strtotime($document->car_registeration_expiry_date)) : '' }}</th>
                </tr>
                <tr>
                    <th >
                        @if(!empty($document->car_registeration_photo))
                          <a class="pakainfo fancybox" rel="ligthbox" href="{{asset("storage/registeration_image/$document->car_registeration_photo") }}">
                            <img class="img-responsive infinityknow" width="15%" alt="jquery fancybox popup example" title="jquery fancybox popup example" src="{{ asset("storage/registeration_image/$document->car_registeration_photo") }}" 
                          </a>
                        @endif
                      </th>
                </tr>
                <tr>
                    <th >{{ !empty($document->car_inspection_date) ? date('d-m-Y',strtotime($document->car_inspection_date)) : '' }}</th>
                </tr>
                <tr>
                    <th >
                        @if(!empty($document->car_inspection_photo))
                          <a class="pakainfo fancybox" rel="ligthbox" href="{{asset("storage/inspection_photo/$document->car_inspection_photo") }}">
                            <img class="img-responsive infinityknow" width="15%" alt="jquery fancybox popup example" title="jquery fancybox popup example" src="{{ asset("storage/inspection_photo/$document->car_inspection_photo") }}" 
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