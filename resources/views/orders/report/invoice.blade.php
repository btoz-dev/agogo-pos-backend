<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LAPORAN TOTAL PENJUALAN PER ITEM</title>
    <style>
        body{
            padding: 0;
            margin: 0;
        }
        .page{
            font-size: 24px !important; 
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
            <h3>LAPORAN TOTAL PENJUALAN PER ITEM</h3>
            <p style="margin-left:260px">Tanggal Transaksi : {{$start_date_lap}}</p>
        </div>            
        <br>
        {{-- <hr> --}}
        {{-- <h4 style="line-height: 0px;">Invoice: #{{ $kas->id }}</h4>
        <p><small style="opacity: 0.5;">{{ $kas->created_at->format('d-m-Y H:i:s') }}</small></p> --}}
    </div>
    <br>
    <hr>
    
    <div class="page">
    <table class="layout display responsive-table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th style="text-align:center;">Kode Menu</th>
                                    <th style="text-align:center;">Nama Menu</th>
                                    <th style="text-align:center;">Total Quantity</th>                                
                                    <th style="text-align:right;">Total Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $row)
                                <tr>                                    
                                    <td style="text-align:center;border-bottom:0px">{{ $loop->iteration }}</td>
                                    <td style="text-align:center;border-bottom:0px">{{ $row->product_id }}</td>                                            
                                    <td style="text-align:center;border-bottom:0px">{{ $row->product->name }}</td>                                    
                                    <td style="text-align:center;border-bottom:0px">{{ $row->qty }}</td>                                                                    
                                    <td style="text-align:right;border-bottom:0px">{{ number_format($row->price) }}</td>                                                                    
                                </tr>
                                @empty
                                <tr>
                                    <td class="text-center" colspan="7">Tidak ada data pemebelian hari ini</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" style="text-align:right;border-bottom:0px;border-top: 1px solid black;">Grand Total : </th>
                                    <th colspan="1" style="text-align:right;border-bottom:0px;border-top: 1px solid black;"> {{number_format($total_harga)}}</th>                                    
                                </tr>
                            </tfoot>
                                </table>  

        
    </div>
</body>
</html>