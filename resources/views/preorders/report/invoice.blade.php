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
            font-size: 16px !important; 
            max-width: 80em;
            margin: 0 auto;
        }
        table 
        { 
        table-layout:auto !important; 
        width: 100% !important;
        }


        table th,
        table td{
            text-align: center;
        }

        table, th, td {
            border-bottom: 1px solid black;
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
            border-bottom: 1px solid black;
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
                <img src="http://101.255.125.227:82/uploads/profile/agogo.png" alt="Image" height="100px"/>        
                <div style="float:right;margin-top:-30px">
                    <h2 style="margin-left:220px">Laporan Pemesanan</h2>
                    <p style="margin-left:250px">Tanggal Cetak : {{ date('d M Y', strtotime($today)) }}</p>
                </div>            
                <br>
        {{-- <h4 style="line-height: 0px;">Invoice: #{{ $kas->id }}</h4>
        <p><small style="opacity: 0.5;">{{ $kas->created_at->format('d-m-Y H:i:s') }}</small></p> --}}
    </div>
    <br>
    <hr>
    
    <div class="page">
    <table class="layout display responsive-table" style="border-bottom:0px">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Order</th>                                            
                                            <th>Tanggal</th>
                                            <th>Tanggal Selesai</th>
                                            <th>Jam</th>
                                            <th>Status</th>
                                            <th>Pencatat</th>
                                            <th>Pelanggan</th>
                                            <th style="text-align:right;">Total Harga</th>
                                            <th style="text-align:right;">DP</th>
                                            <th style="text-align:right;">Sisa</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($preorders as $row)
                                        <tr>
                                            <td style="text-align:center;border-bottom:0px">{{$loop->iteration}}</td>
                                            <td style="text-align:center;border-bottom:0px">{{ $row->invoice }}</td>                                            
                                            <td style="text-align:center;border-bottom:0px">{{ Carbon\Carbon::parse($row->created_at)->format('d-m-Y') }}</td>  
                                            <td style="text-align:center;border-bottom:0px">{{ Carbon\Carbon::parse($row->tgl_selesai)->format('d-m-Y') }}</td>  
                                            <td style="text-align:center;border-bottom:0px">{{ $row->waktu_selesai }}</td>
                                            <td style="text-align:center;border-bottom:0px">{{ $row->status }}</td>
                                            <td style="text-align:center;border-bottom:0px">{{ $row->user->name }}</td>
                                            <td style="text-align:center;border-bottom:0px">{{ $row->nama }}</td>
                                            <td style="text-align:right;border-bottom:0px">{{ number_format($row->total) }}</td>
                                            <td style="text-align:right;border-bottom:0px">{{ number_format($row->uang_muka) }}</td>
                                            <td style="text-align:right;border-bottom:0px">{{ number_format($row->sisa_harus_bayar) }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td class="text-center" colspan="7">Tidak ada data transaksi Hari ini</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="7" style="text-align:right;border-bottom:0px;border-top: 1px solid black;"></th>                
                                            <th colspan="1" style="text-align:right;border-bottom:0px;border-top: 1px solid black;">Grand Total</th>
                                            <th colspan="1" style="text-align:right;border-bottom:0px;border-top: 1px solid black;">{{number_format($total_harga)}}</th>
                                            <th colspan="1" style="text-align:right;border-bottom:0px;border-top: 1px solid black;">{{number_format($total_uang_muka)}}</th>
                                            <th colspan="1" style="text-align:right;border-bottom:0px;border-top: 1px solid black;">{{number_format($total_harus_bayar)}}</th>

                                        </tr>
                                        <tr>
                                                <th colspan="7" style="text-align:right;border-bottom:0px"></th>
                                                <th colspan="1" style="text-align:right;border-bottom:0px">Pembatalan Transaksi</th>
                                                <th colspan="1" style="text-align:right;border-bottom:0px">{{number_format($total_harga_cancel)}}</th>
                                                <th colspan="1" style="text-align:right;border-bottom:0px">{{number_format($total_uang_muka_cancel)}}</th>
                                                <th colspan="1" style="text-align:right;border-bottom:0px"></th>
                                            </tr>
                                            <tr>
                                                    <th colspan="7" style="text-align:right;border-bottom:0px"></th>
                                                    <th colspan="1" style="text-align:right;border-bottom:0px">Total Transaksi</th>
                                                    <th colspan="1" style="text-align:right;border-bottom:0px">{{number_format($total_harga - $total_harga_cancel)}}</th>
                                                    <th colspan="1" style="text-align:right;border-bottom:0px">{{number_format($total_uang_muka - $total_uang_muka_cancel)}}</th>
                                                    <th colspan="1" style="text-align:right;border-bottom:0px"></th>
                                                </tr>
                                    </tfoot>
                                </table>  

        
    </div>
</body>
</html>