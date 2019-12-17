@extends('layouts.master')

@section('title')
    <title>Laporan Pemesanan</title>
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
                            <li class="breadcrumb-item active">Order</li>
                        </ol>
                    </div> --}}
                </div>
            </div>
        </div>

        <section class="content" id="dw">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header with-border">
                                <h3 class="card-title">Filter Transaksi</h3>
                            </div>
                            <div class="card-body">

                            <form action="{{ route('preorder.index') }}" method="get">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Mulai Tanggal</label>
                                            <input type="date" name="start_date"
                                                class="form-control {{ $errors->has('start_date') ? 'is-invalid':'' }}"
                                                id="start_date"
                                                value="{{ request()->get('start_date') == null ? $phd_today  : request()->get('start_date') }}">
                                                
                                        </div>
                                        <div class="form-group">
                                            <button class="btn btn-primary btn-sm">Cari</button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Sampai Tanggal</label>
                                            <input type="date" name="end_date" 
                                                class="form-control {{ $errors->has('end_date') ? 'is-invalid':'' }}"
                                                id="end_date"
                                                value="{{ request()->get('end_date') == null ? $phd_today  : request()->get('end_date') }}">
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
                                <h3 class="card-title">Laporan Pemesanan</h3>
                            </div>
                            
                            <div class="card-body">                                
                                    <a href="{{ route('preorder.pdf')}}"
                                            {{-- <a href="{{ route('order.pdf'}}"  --}}
                                            target="_blank"
                                            class="btn btn-primary btn-sm">
                                            <i class="fa fa-print"></i> Export Data
                                    </a>
                                <h4 style="text-align: center;">Pemesanan Berhasil</h4> 
                            <div class="table-responsive">
                                <table id="example2" class="table table-bordered table-hover dataTable">
                                    <thead>
                                        <tr><th>Preorder ID</th>
                                            <th>Nama Pelanggan</th>
                                            <th>Tanggal Pesan</th>
                                            <th>Tanggal Selesai</th>
                                            <th>Jam Selesai</th>
                                            <th>Order Status</th>
                                            <th>Pencatat</th>
                                            <th>Total Harga</th>
                                            <th>DP</th>
                                            <th>Sisa</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($preorders as $row)
                                        <tr>
                                            <td>{{ $row->invoice }}</td>
                                            <td>{{ $row->nama }}</td>                                            
                                            <td>{{ Carbon\Carbon::parse($row->created_at)->format('d/m/Y') }}</td>  
                                            <td>{{ Carbon\Carbon::parse($row->tgl_selesai)->format('d/m/Y') }}</td>                                              
                                            <td>{{ $row->waktu_selesai }}</td>
                                            @if( $row->status =='PAID')         
                                            <td>Diambil</td>
                                            @else
                                            <td>Belum Diambil</td>
                                            @endif
                                            <td>{{ $row->user->name }}</td>
                                            <td>Rp {{ number_format($row->total) }}</td>
                                            <td>Rp {{ number_format($row->uang_muka) }}</td>
                                            <td>Rp {{ number_format($row->sisa_harus_bayar) }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td class="text-center" colspan="7">Tidak ada data transaksi Hari ini</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="6" style="text-align:right"></th>                
                                            <th colspan="1" style="text-align:right">Grand Total : </th>
                                            <th colspan="1" style="text-align:left">Rp {{number_format($total_harga)}}</th>
                                            <th colspan="1" style="text-align:left">Rp {{number_format($total_uang_muka)}}</th>
                                            <th colspan="1" style="text-align:left">Rp {{number_format($total_harus_bayar)}}</th>

                                        </tr>
                                        <tr>
                                                <th colspan="6" style="text-align:right"></th>
                                                <th colspan="1" style="text-align:right">Pembatalan Transaksi : </th>
                                                <th colspan="1" style="text-align:left">Rp {{number_format($total_harga_cancel)}}</th>
                                                <th colspan="1" style="text-align:left">Rp {{number_format($total_uang_muka_cancel)}}</th>
                                                <th colspan="1" style="text-align:right"></th>
                                            </tr>
                                            <tr>
                                                    <th colspan="6" style="text-align:right"></th>
                                                    <th colspan="1" style="text-align:right">Total Transaksi : </th>
                                                    <th colspan="1" style="text-align:left">Rp {{number_format($total_harga - $total_harga_cancel)}}</th>
                                                    <th colspan="1" style="text-align:left">Rp {{number_format($total_uang_muka - $total_uang_muka_cancel)}}</th>
                                                    <th colspan="1" style="text-align:right"></th>
                                                </tr>
                                    </tfoot>
                                </table>                            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
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
      "paging": false,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": true,
    //   dom: 'Bfrtip',
    //     buttons: [
    //         { extend: 'copy', footer: true },
    //         { extend: 'excel', footer: true },
    //         { extend: 'csv', footer: true },
    //         { extend: 'pdf', footer: true },
            
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