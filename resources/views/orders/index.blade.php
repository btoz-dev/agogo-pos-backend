@extends('layouts.master')

@section('title')
    <title>Laporan Penjualan Produk</title>
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

                            <form action="{{ route('order.index') }}" method="get">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Pilih Tanggal</label>
                                            <input type="text" name="start_date" 
                                                class="form-control {{ $errors->has('start_date') ? 'is-invalid':'' }}"
                                                id="start_date"
                                                value="{{ request()->get('start_date') }}"
                                                >
                                        </div>
                                        <div class="form-group">
                                            <button class="btn btn-primary btn-sm">Cari</button>
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Sampai Tanggal</label>
                                            <input type="text" name="end_date" 
                                                class="form-control {{ $errors->has('end_date') ? 'is-invalid':'' }}"
                                                id="end_date"
                                                value="{{ request()->get('end_date') }}">
                                        </div>
                                    </div> --}}
                                </div>
                            </form>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header with-border">
                                <h3 class="card-title">Laporan Penjualan By Produk Per Hari</h3>
                            </div>
                            <div class="card-body">
                            <div class="table-responsive">
                                <table id="example2" class="table table-bordered table-hover dataTable">
                                    <thead>
                                        <tr>
                                            <th>Kode Menu</th>
                                            {{-- <th>Pelanggan</th> --}}
                                            <th>Nama Menu</th>
                                            <th>Total Quantity</th>
                                            <th>Total Harga</th>
                                            {{-- <th>Tgl Transaksi</th> --}}
                                            {{-- <th>Aksi</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($orders as $row)
                                        <tr>
                                            <td>{{ $row->product_id }}</td>
                                            <td>{{ $row->product->name }}</td>
                                            {{-- <td>{{ $row->customer->name }}</td> --}}
                                            <td>{{ number_format($row->qty) }}</td>
                                            <td>Rp {{ number_format($row->price) }}</td>
                                            {{-- <td>{{ $row->user->name }}</td> --}}
                                            {{-- <td>{{ $row->created_at->format('d-m-Y H:i:s') }}</td> --}}
                                            {{-- <td>
                                                <a href="{{ route('order.pdf', $row->invoice) }}" 
                                                    target="_blank"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fa fa-print"></i>
                                                </a>
                                                <a href="{{ route('order.excel', $row->invoice) }}" 
                                                    target="_blank"
                                                    class="btn btn-info btn-sm">
                                                    <i class="fa fa-file-excel-o"></i>
                                                </a>
                                            </td> --}}
                                        </tr>
                                        @empty
                                        <tr>
                                            <td class="text-center" colspan="7">Tidak ada data transaksi</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" style="text-align:right">Grand Total : </th>
                                            <th colspan="3" style="text-align:left">Rp.{{number_format($total_harga)}}</th>
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
    </div>
@endsection

@section('js')
    <script>
        $('#start_date').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd'
        });

        $('#end_date').datepicker({
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
      "info": true,
      "autoWidth": true,
      "bPaginate":true,
      "sPaginationType":"full_numbers",
      "bLengthChange": true,
      "bInfo" : true

      dom: 'Bfrtip',
        buttons: [
            'excel', 'pdf', 'print',
        ]
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