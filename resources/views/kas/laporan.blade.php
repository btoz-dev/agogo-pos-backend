@extends('layouts.master')

@section('title')
    <title>Laporan Pendapatan Kasir</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                    </div>
                    {{-- <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active">Kas</li>
                        </ol>
                    </div> --}}
                </div>
            </div>
        </div>
        {{-- @if (is_null($kas[0]->user->name)) --}}
        @if(count($kas) > 0)
        <section class="content" id="dw">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header with-border">
                                <h3 class="card-title">Filter Transaksi</h3>
                            </div>
                            <div class="card-body">

                            <form action="{{ route('kas.laporan') }}" method="get">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Pilih Tanggal</label>
                                            <input type="date" name="start_date" 
                                                class="form-control {{ $errors->has('start_date') ? 'is-invalid':'' }}"
                                                id="start_date"
                                                value="{{ request()->get('start_date') == null ? date('Y-m-d')   : request()->get('start_date') }}">                                                
                                        </div>
                                        <div class="form-group">
                                            <button class="btn btn-primary btn-sm">Cari</button>
                                        </div>
                                    </div>
                                    
                                </div>
                            </form>

                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header with-border">
                                <h3 class="card-title">Laporan Pendapatan Kasir</h3>
                            </div>
                            <div class="card-body">
                                    <a href="{{ route('kas.pdf')}}"
                                    {{-- <a href="{{ route('order.pdf'}}"  --}}
                                    target="_blank"
                                    class="btn btn-primary btn-sm">
                                    <i class="fa fa-print"></i> Export Data
                                </a>
                            <div class="table-responsive">
                                <table id="example2" class="table table-hover dataTable">
                                    <thead>
                                        {{-- <tr>
                                            <th>Kasir : {{ $kas[0]->user->name }}</th>
                                            <th style="text-align: right;">Per Tanggal : {{date('d-m-Y', strtotime($kas[0]->created_at)) }}</th>
                                            
                                        </tr> --}}
                                        <tr>
                                            <th>Kasir</th>
                                            <th>Date</th>
                                            <th>Saldo Awal</th>
                                            <th>Total Transaksi</th>
                                            <th>Total Refund</th>
                                            <th>Saldo Akhir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($kas as $row)
                                        <tr>
                                            <td>{{ $row->user->name }}</td>                                            
                                            <td>{{ Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i:s') }}</td>  
                                            <td>Rp {{ number_format($row->saldo_awal) }}</td>
                                            <td>Rp {{ number_format($row->transaksi) }}</td>
                                            <td>Rp {{ number_format($row->total_refund) }}</td>
                                            <td>Rp {{ number_format($row->saldo_akhir) }}</td>                                            
                                        </tr>
                                        @empty
                                        <tr>
                                            <td class="text-center" colspan="7">Tidak ada data transaksi</td>
                                        </tr>
                                        @endforelse
                                        {{-- <tr>
                                            <td>Saldo Awal</td>      
                                            <td style="text-align: right;">Rp {{ number_format($kas[0]->saldo_awal) }} </td>                                                                                        
                                        </tr>
                                        <tr>
                                            <td>Pendapatan </td>      
                                            <td style="text-align: right;">Rp {{ number_format($kas[0]->transaksi) }} </td>                                                                                                                                                                                                                                                                                                                                             
                                        </tr>
                                        <tr>
                                            <td>Refund </td>      
                                            <td style="text-align: right;">Rp {{ number_format($kas[0]->total_refund) }} </td>                                                                                                                                                                                                                                                                                                                                             
                                        </tr> --}}
                                    </tbody>
                                    <tfoot>
                                        {{-- <tr>
                                            <th>Total Pendapatan </th>      
                                            <th style="text-align: right;">Rp {{ number_format($kas[0]->transaksi - $kas[0]->total_refund) }} </th>                                                                                                                                                                                                                                                                                                                                             
                                        </tr>
                                        <tr>
                                            <th>Total Kas Tersedia </th>      
                                            <th style="text-align: right;">Rp {{ number_format($kas[0]->saldo_akhir) }} </th>                                                                                                                                                                                                                                                                                                                                             
                                        </tr> --}}
                                        {{-- <tr>
                                            <th colspan="3" style="text-align:right">Grand Total : </th>
                                            <th colspan="3" style="text-align:left">Rp.{{number_format($total_harga)}}</th>
                                        </tr> --}}
                                    </tfoot>
                                </table>
                            </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-5">
                        <div class="card">
                            
                            <div class="card-body">
                            <div class="table-responsive">
                                <table id="example2" class="table table-bordered table-hover dataTable">
                                    <thead>
                                        <tr>
                                            <th>Penerimaan Uang</th>
                                            <th style="text-align: Center;">Jumlah</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- @forelse ($kas as $row)
                                        <tr>
                                            <td>{{ $row->user->name }}</td>
                                            <td>{{ $row->created_at }}</td>
                                            <td>Rp {{ number_format($row->saldo_awal) }}</td>
                                            <td>Rp {{ number_format($row->transaksi) }}</td>
                                            <td>Rp {{ number_format($row->total_refund) }}</td>
                                            <td>Rp {{ number_format($row->saldo_akhir) }}</td>
                                            <td>Rp {{ number_format($row->saldo_akhir) }}</td>                            
                                        </tr>
                                        @empty
                                        <tr>
                                            <td class="text-center" colspan="7">Tidak ada data transaksi</td>
                                        </tr>
                                        @endforelse --}}
                                        <tr>
                                            <td>100.000</td>      
                                            <td style="text-align: right;"></td>                                                                                        
                                        </tr>
                                        <tr>
                                            <td>50.000</td>      
                                            <td style="text-align: right;"></td>                                                                                                                                                                                                                                                                                                                                                                                                                                     
                                        </tr>
                                        <tr>
                                            <td>20.000</td>      
                                            <td style="text-align: right;"></td>                                                                                            
                                        </tr>
                                        <tr>
                                            <td>10.000</td>      
                                            <td style="text-align: right;"></td>                                                                                            
                                        </tr>
                                        <tr>
                                            <td>5000</td>      
                                            <td style="text-align: right;"></td>                                                                                            
                                        </tr>
                                        <tr>
                                            <td>2000</td>      
                                            <td style="text-align: right;"></td>                                                                                            
                                        </tr>
                                        <tr>
                                            <td>1000</td>      
                                            <td style="text-align: right;"></td>                                                                                            
                                        </tr>
                                        <tr>
                                            <td>500</td>      
                                            <td style="text-align: right;"></td>                                                                                            
                                        </tr>
                                        <tr>
                                            <td>200</td>      
                                            <td style="text-align: right;"></td>                                                                                            
                                        </tr>
                                        <tr>
                                            <td>100</td>      
                                            <td style="text-align: right;"></td>                                                                                            
                                        </tr>
                                        <tr>
                                            <td>10</td>      
                                            <td style="text-align: right;"></td>                                                                                            
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Total Kas Selisih </th>      
                                            <th style="text-align: right;"></th>                                                                                                                                                                                                                                                                                                                                             
                                        </tr>
                                        
                                    </tfoot>
                                </table>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        @else
        <section class="content" id="dw">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header with-border">
                                    <h3 class="card-title">Filter Transaksi</h3>
                                </div>
                                <div class="card-body">
    
                                <form action="{{ route('kas.laporan') }}" method="get">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">Pilih Tanggal</label>
                                                <input type="date" name="start_date"
                                                class="form-control {{ $errors->has('start_date') ? 'is-invalid':'' }}"
                                                id="start_date"
                                                value="{{ request()->get('start_date') == null ? date('m/d/Y')  : request()->get('start_date') }}">
                                                
                                            </div>
                                            <div class="form-group">
                                                <button class="btn btn-primary btn-sm">Cari</button>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </form>
    
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header with-border">
                                    <h3 class="card-title">Laporan Pendapatan Kasir</h3>
                                </div>
                                <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example2" class="table table-hover dataTable">
                                        <thead>
                                            
                                        </thead>
                                        <tbody>
                                            <p>Laporan Kas Hari ini masih kosong</p>
                                        </tbody>
                                        <tfoot>
                                           
                                        </tfoot>
                                    </table>
                                </div>
                                </div>
                            </div>
                        </div>                        
                        <div class="col-md-5">
                            <div class="card">
                                
                                <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example2" class="table table-bordered table-hover dataTable">
                                        <thead>
                                            <tr>
                                                <th>Penerimaan Uang</th>
                                                <th style="text-align: Center;">Jumlah</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- @forelse ($kas as $row)
                                            <tr>
                                                <td>{{ $row->user->name }}</td>
                                                <td>{{ $row->created_at }}</td>
                                                <td>Rp {{ number_format($row->saldo_awal) }}</td>
                                                <td>Rp {{ number_format($row->transaksi) }}</td>
                                                <td>Rp {{ number_format($row->total_refund) }}</td>
                                                <td>Rp {{ number_format($row->saldo_akhir) }}</td>
                                                <td>Rp {{ number_format($row->saldo_akhir) }}</td>                            
                                            </tr>
                                            @empty
                                            <tr>
                                                <td class="text-center" colspan="7">Tidak ada data transaksi</td>
                                            </tr>
                                            @endforelse --}}
                                            <tr>
                                                <td>100.000</td>      
                                                <td style="text-align: right;"></td>                                                                                        
                                            </tr>
                                            <tr>
                                                <td>50.000</td>      
                                                <td style="text-align: right;"></td>                                                                                                                                                                                                                                                                                                                                                                                                                                     
                                            </tr>
                                            <tr>
                                                <td>20.000</td>      
                                                <td style="text-align: right;"></td>                                                                                            
                                            </tr>
                                            <tr>
                                                <td>10.000</td>      
                                                <td style="text-align: right;"></td>                                                                                            
                                            </tr>
                                            <tr>
                                                <td>5000</td>      
                                                <td style="text-align: right;"></td>                                                                                            
                                            </tr>
                                            <tr>
                                                <td>2000</td>      
                                                <td style="text-align: right;"></td>                                                                                            
                                            </tr>
                                            <tr>
                                                <td>1000</td>      
                                                <td style="text-align: right;"></td>                                                                                            
                                            </tr>
                                            <tr>
                                                <td>500</td>      
                                                <td style="text-align: right;"></td>                                                                                            
                                            </tr>
                                            <tr>
                                                <td>200</td>      
                                                <td style="text-align: right;"></td>                                                                                            
                                            </tr>
                                            <tr>
                                                <td>100</td>      
                                                <td style="text-align: right;"></td>                                                                                            
                                            </tr>
                                            <tr>
                                                <td>10</td>      
                                                <td style="text-align: right;"></td>                                                                                            
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Total Kas Selisih </th>      
                                                <th style="text-align: right;"></th>                                                                                                                                                                                                                                                                                                                                             
                                            </tr>
                                            
                                        </tfoot>
                                    </table>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    </div>
@endsection

@section('js')
    <script>
        $('#start_date').datepicker({
            todayHighlight: true,
            autoclose: true,
            format: 'yyyy-mm-dd'
        });

        $('#end_date').datepicker({
            todayHighlight: true,
            autoclose: true,
            format: 'yyyy-mm-dd'
        });
    </script>
    <script>
  $(function () {
    $("#example1").DataTable();
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": false,
      "autoWidth": true,
    //   dom: 'Bfrtip',
    //     buttons: [
    //         'excel', 'pdf', 'print',
    //     ]
    });
  });
</script>
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js"></script>
@endsection