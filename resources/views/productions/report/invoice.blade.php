<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LAPORAN PERGERAKAN STOK PRODUKSI</title>
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
                    <h3 style="margin-left:10px">LAPORAN PERGERAKAN STOK PRODUKSI</h3>
                    <p style="margin-left:370px;font-size:20px">Tanggal Produksi : {{$start_date_lap}}</p>
                </div>            
                <br>
        {{-- <h4 style="line-height: 0px;">Invoice: #{{ $kas->id }}</h4>
        <p><small style="opacity: 0.5;" style="text-align:center;border-bottom:0px">{{ $kas->created_at->format('d-m-Y H:i:s') }}</small></p> --}}
    </div>
    <br>
    <hr>
    
    <div class="page">
    <table class="layout display responsive-table" style="border-bottom:0px">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Tgl Produksi</th>
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
                                            <td style="text-align:center;border-bottom:0px">{{$loop->iteration}}</td>
                                            <td style="text-align:center;border-bottom:0px">{{ Carbon\Carbon::parse($row->created_at)->format('d/m/Y') }}</td>
                                            <td style="text-align:center;border-bottom:0px">{{ $row->product->code }}</td>
                                            <td style="text-align:center;border-bottom:0px">{{ $row->product->name }}</td>
                                            <td style="text-align:center;border-bottom:0px">{{ $row->produksi1 }}</td>
                                            <td style="text-align:center;border-bottom:0px">{{ $row->produksi2 }}</td>
                                            <td style="text-align:center;border-bottom:0px">{{ $row->produksi3 }}</td>
                                            <td style="text-align:center;border-bottom:0px">{{ $row->total_produksi }}</td>
                                            <td style="text-align:center;border-bottom:0px">{{ $row->penjualan_pesanan }}</td>
                                            <td style="text-align:center;border-bottom:0px">{{ $row->penjualan_toko }}</td>
                                            <td style="text-align:center;border-bottom:0px">{{ $row->ket_rusak }}</td>
                                            <td style="text-align:center;border-bottom:0px">{{ $row->ket_lain }}</td>
                                            <td style="text-align:center;border-bottom:0px">{{ $row->sisa_stock }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td class="text-center" colspan="7">Tidak ada data produksi hari ini</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>                                        
                                    </tfoot>
                                </table>  

    
    <hr>
        
    </div>
</body>
</html>