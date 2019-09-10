<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        body{
            padding: 0;
            margin: 0;
        }
        .page{
            max-width: 80em;
            margin: 0 auto;
        }
        table 
        { 
        table-layout:auto !important; 
        width: auto !important;
        }


        table th,
        table td{
            text-align: center;
        }
        table.layout{
            width: 100%;
            border-collapse: collapse;
        }
        table.display{
            margin: 1em 0;
        }
        table.display th,
        table.display td{
            border: 1px solid #B3BFAA;
            padding: .2em 0,8em;
        }

        table.display th{ background: #fff; }
        table.display td{ background: #fff; }

        table.responsive-table{
            box-shadow: 0 1px 10px rgba(0, 0, 0, 0.2);
        }

        .listcust {
            margin: 0;
            padding: 0;
            list-style: none;
            display:table;
            border-spacing: 10px;
            border-collapse: separate;
            list-style-type: none;
        }

        .customer {
            padding-left: 600px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="http://101.255.125.227:82/uploads/profile/agogo.png" alt="Image"/>        
        <div style="float:right"><h2>LAPORAN PEMESANAN</h2></div>
        <hr>
        {{-- <h4 style="line-height: 0px;">Invoice: #{{ $kas->id }}</h4>
        <p><small style="opacity: 0.5;">{{ $kas->created_at->format('d-m-Y H:i:s') }}</small></p> --}}
    </div>
    
    <div class="page">
    <table class="layout display responsive-table">
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
                                            <td>{{ $row->status }}</td>
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
</body>
</html>