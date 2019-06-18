@extends('layouts.master')

@section('title')
    <title>Laporan Produksi</title>

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
                                            <label for="">Mulai Tanggal</label>
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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="">Sampai Tanggal</label>
                                            <input type="text" name="end_date" 
                                                class="form-control {{ $errors->has('end_date') ? 'is-invalid':'' }}"
                                                id="end_date"
                                                value="{{ request()->get('end_date') }}">
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
                                <h3 class="card-title">Laporan Produksi</h3>
                            </div>
                            <div class="card-body">
                            <div class="table-responsive">
                                <table id="example2" class="table table-bordered table-hover dataTable">
                                    <thead>
                                        <tr>
                                            <th>Kode Menu</th>
                                            <th>Nama Menu</th>
                                            <th>Produksi 1</th>
                                            <th>Produksi 2</th>
                                            <th>Produksi 3</th>
                                            <th>Total Produksi</th>
                                            <th>Pesanan diambil</th>
                                            <th>Total Penjualan</th>
                                            <th>Rusak</th>
                                            <th>Lain - lain</th>
                                            <th>Sisa Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($stock as $row)
                                        <tr>
                                            <td>{{ $row->product->code }}</td>
                                            <td>{{ $row->product->name }}</td>
                                            <td>{{ $row->produksi1 }}</td>
                                            <td>{{ $row->produksi2 }}</td>
                                            <td>{{ $row->produksi3 }}</td>
                                            <td>{{ $row->total_produksi }}</td>
                                            <td>{{ $row->penjualan_pesanan }}</td>
                                            <td>{{ $row->penjualan_toko }}</td>
                                            <td>{{ $row->ket_rusak }}</td>
                                            <td>{{ $row->ket_lain }}</td>
                                            <td>{{ $row->sisa_stock }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td class="text-center" colspan="7">Tidak ada data transaksi</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
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
      dom: 'Bfrtip',
        buttons: [
            'pdf',
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