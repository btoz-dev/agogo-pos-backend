<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Pendapatan Kas</title>
    <style>
        body{
            padding: 0;
            margin: 0;
        }
        .page{
            max-width: 80em;
            margin: 0 auto;
        }
        table th,
        table td{
            text-align: left;
        }
        table.layout{
            
            border-collapse: collapse;
        }
        table.display{
            margin: 1em 0;
        }
        table.display th,
        table.display td{
            border-top: 1px solid ;
            /* border-bottom: 1px solid ; */
            padding: .5em 1em;
            border-spacing: 20px;
        }

        /* table.display th{ background: #D5E0CC; } */
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
        hr { 
            display: block;
            margin-top: 0.5em;
            margin-bottom: 0.5em;
            margin-left: auto;
            margin-right: auto;
            border-style: inset;
            border-width: 1px;
            } 
    </style>
</head>
<body>
    <div class="header">
        <img src="http://101.255.125.227:82/uploads/profile/agogo.png" alt="Image"/>
        <div style="float:right"><h2>Laporan Pendapatan Kasir</h2></div>
        
        <hr>
        {{-- <h4 style="line-height: 0px;">Invoice: #{{ $kas->id }}</h4>
        <p><small style="opacity: 0.5;">{{ $kas->created_at->format('d-m-Y H:i:s') }}</small></p> --}}
    </div>
    {{-- <div class="customer">
        <table>
            <tr>
                <th>Nama Pelanggan</th>
                <td>:</td>
                <td>{{ $order->customer->name }}</td>
            </tr>
            <tr>
                <th>No Telp</th>
                <td>:</td>
                <td>{{ $order->customer->phone }}</td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td>:</td>
                <td>{{ $order->customer->address }}</td>
            </tr>
        </table>
    </div> --}}
    <div class="page">
        
        
        <table class="layout display responsive-table" style="width: 58%; float: left;">
            <thead>
                <tr>
                    <th style="border-top: 0px solid;">Kasir : {{ $kas->user->name }}</th>
                    <th style="text-align: right;border-top: 0px solid;">Per Tanggal : {{date('d-m-Y', strtotime($kas->created_at)) }}</th>
                    
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
                    <td style="border-top: 2px solid;">Saldo Awal</td>      
                    <td style="text-align: right;border-top: 2px solid;">Rp {{ number_format($kas->saldo_awal) }} </td>                                                                                        
                </tr>
                <tr>
                    <td style="border-top: 0px solid;">Pendapatan </td>      
                    <td style="text-align: right;border-top: 0px solid;">Rp {{ number_format($kas->transaksi) }} </td>                                                                                                                                                                                                                                                                                                                                             
                </tr>
                <tr>
                    <td style="border-top: 0px solid;">Refund </td>      
                    <td style="text-align: right;border-top: 0px solid;">Rp {{ number_format($kas->total_refund) }} </td>                                                                                                                                                                                                                                                                                                                                             
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th style="border-top: 2px solid;">Total Pendapatan </th>      
                    <th style="text-align: right;border-top: 2px solid;">Rp {{ number_format($kas->transaksi - $kas->total_refund) }} </th>                                                                                                                                                                                                                                                                                                                                             
                </tr>
                <tr>
                    <th style="border-top: 0px solid;">Total Kas Tersedia </th>      
                    <th style="text-align: right;border-top: 0px solid;">Rp {{ number_format($kas->saldo_akhir) }} </th>                                                                                                                                                                                                                                                                                                                                             
                </tr>
                {{-- <tr>
                    <th colspan="3" style="text-align:right">Grand Total : </th>
                    <th colspan="3" style="text-align:left">Rp.{{number_format($total_harga)}}</th>
                </tr> --}}
            </tfoot>
            </table>
            <table class="display responsive-table" style="width: 40%; float: left; margin-left:20px; margin-top:-2px;border-spacing=0;">
                <thead>
                    <tr>
                        <th style="border-top: 0px solid;">Penerimaan Uang</th>
                        <th style="text-align: Center; border-left: 2px solid ;border-top: 0px solid;">Jumlah</th>
                        
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
                        <td style="text-align: right; border-left: 2px solid ;"></td>                                                                                        
                    </tr>
                    <tr>
                        <td>50.000</td>      
                        <td style="text-align: right;border-left: 2px solid ;"></td>                                                                                                                                                                                                                                                                                                                                                                                                                                     
                    </tr>
                    <tr>
                        <td>20.000</td>      
                        <td style="text-align: right;border-left: 2px solid ;"></td>                                                                                            
                    </tr>
                    <tr>
                        <td>10.000</td>      
                        <td style="text-align: right;border-left: 2px solid ;"></td>                                                                                            
                    </tr>
                    <tr>
                        <td>5000</td>      
                        <td style="text-align: right;border-left: 2px solid ;"></td>                                                                                            
                    </tr>
                    <tr>
                        <td>2000</td>      
                        <td style="text-align: right;border-left: 2px solid ;"></td>                                                                                            
                    </tr>
                    <tr>
                        <td>1000</td>      
                        <td style="text-align: right;border-left: 2px solid ;"></td>                                                                                            
                    </tr>
                    <tr>
                        <td>500</td>      
                        <td style="text-align: right;border-left: 2px solid ;"></td>                                                                                            
                    </tr>
                    <tr>
                        <td>200</td>      
                        <td style="text-align: right;border-left: 2px solid ;"></td>                                                                                            
                    </tr>
                    <tr>
                        <td>100</td>      
                        <td style="text-align: right;border-left: 2px solid ;"></td>                                                                                            
                    </tr>
                    <tr>
                        <td>10</td>      
                        <td style="text-align: right;border-left: 2px solid ;"></td>                                                                                            
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total Kas Selisih </th>      
                        <th style="text-align: right;border-left: 2px solid ;"></th>                                                                                                                                                                                                                                                                                                                                             
                    </tr>
                    
                </tfoot>
            </table>
            <div>
                <h4 style="margin-top: 60%;float:right;margin-right:25%">Keterangan</h4>
                <h4 style="margin-top: 20%;float:right;margin-right:-7%">KASIR</h4>
                <h4 style="margin-top: 20%;float:right;margin-right:-35%">MANAGER</h4>
                {{-- <h4 style="line-height: 0px;">Invoice: #{{ $kas->id }}</h4>
                <p><small style="opacity: 0.5;">{{ $kas->created_at->format('d-m-Y H:i:s') }}</small></p> --}}
            </div>
    </div>
</body>
</html>