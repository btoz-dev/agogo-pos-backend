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
        width: 100% !important;
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
        <div style="float:right"><h2>Laporan Total Pendapatan Harian</h2></div>
        <hr>
        {{-- <h4 style="line-height: 0px;">Invoice: #{{ $kas->id }}</h4>
        <p><small style="opacity: 0.5;">{{ $kas->created_at->format('d-m-Y H:i:s') }}</small></p> --}}
    </div>
    
    <div class="page">
    <table class="layout display responsive-table">
                            <thead>
                                <tr>
                                    <th>Tanggal Transaksi</th>
                                    <th>Transaksi</th>
                                    <th>Discount</th>
                                    <th>Total Transaksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $row)
                                <tr>
                                    {{-- <td>{{ $row->trx_date }}</td> --}}
                                    <td>{{ Carbon\Carbon::parse($row->trx_date)->format('d/m/Y') }}</td>                                            
                                    <td>Rp {{ number_format($row->subtotal) }}</td>
                                    {{-- <td>{{ $row->customer->name }}</td> --}}
                                    <td>Rp {{ number_format($row->discount) }}</td>
                                    <td>Rp {{ number_format($row->total) }}</td>

                                </tr>
                                @empty
                                <tr>
                                    <td class="text-center" colspan="7">Tidak ada data penjualan bulan ini</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="1" style="text-align:right">Grand Total : </th>
                                    <th colspan="1" style="">Rp {{number_format($total_subtotal)}}</th>
                                    <th colspan="1" style="">Rp {{number_format($total_discount)}}</th>
                                    <th colspan="1" style="">Rp {{number_format($total_harga)}}</th>
                                </tr>
                            </tfoot>
                                </table>  

        
    </div>
</body>
</html>