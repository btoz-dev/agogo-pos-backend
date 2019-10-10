<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LAPORAN PENDAPATAN KASIR</title>
    <style>
        body{
            padding: 0;
            margin: 0;
        }
        .page{
            font-size: 20px !important; 
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
                <img src="http://101.255.125.227:82/uploads/profile/agogo.png" alt="Image" height="100px"/>        
                <div style="float:right;margin-top:-30px">
                    <h3>LAPORAN PENDAPATAN KASIR</h3>
                    <p style="margin-left:120px">Tanggal Transaksi : {{$start_date_lap}}</p>
                </div>            
                <br>
                {{-- <hr> --}}
                {{-- <h4 style="line-height: 0px;">Invoice: #{{ $kas->id }}</h4>
                <p><small style="opacity: 0.5;">{{ $kas->created_at->format('d-m-Y H:i:s') }}</small></p> --}}
            </div>
            <br>
            <hr>
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
        
        
            <table class="layout display responsive-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Kasir</th>
                            <th>Tanggal</th>
                            <th style="text-align:right;border-bottom:0px">Saldo Awal</th>
                            <th style="text-align:right;border-bottom:0px">Total Transaksi</th>
                            <th style="text-align:right;border-bottom:0px">Total Refund</th>                            
                            <th style="text-align:right;border-bottom:0px">Total Pedapatan</th>
                            <th style="text-align:right;border-bottom:0px">Kas Tersedia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kas as $row)
                        <tr>
                            {{-- <td>{{ $row->trx_date }}</td> --}}
                            <td>{{ $loop->iteration}}</td>
                            <td>{{ $row->user->name }}</td>                                            
                            <td>{{ Carbon\Carbon::parse($row->created_at)->format('d/m/Y') }}</td>  
                            <td style="text-align:right;border-bottom:0px">{{ number_format($row->saldo_awal) }}</td>
                            <td style="text-align:right;border-bottom:0px">{{ number_format($row->transaksi) }}</td>
                            <td style="text-align:right;border-bottom:0px">{{ number_format($row->total_refund) }}</td>
                            <td style="text-align:right;border-bottom:0px">{{ number_format($row->total_refund + $row->transaksi) }}</td>
                            <td style="text-align:right;border-bottom:0px">{{ number_format($row->saldo_awal + $row->total_refund + $row->transaksi) }}</td>

                        </tr>
                        @empty
                        <tr>
                            <td class="text-center" colspan="7">Tidak ada data perhitungan Kas hari ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        {{-- <tr>
                            <th colspan="2" style="text-align:right;border-bottom:0px;border-top: 1px solid black;">Grand Total : </th>
                            <th colspan="1" style="text-align:right;border-bottom:0px;border-top: 1px solid black;"> {{number_format($total_subtotal)}}</th>
                            <th colspan="1" style="text-align:right;border-bottom:0px;border-top: 1px solid black;"> {{number_format($total_discount)}}</th>
                            <th colspan="1" style="text-align:right;border-bottom:0px;border-top: 1px solid black;"> {{number_format($total_harga)}}</th>
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
                <h4 style="margin-top: 40%;float:right;margin-right:-7%">KASIR</h4>
                <h4 style="margin-top: 40%;float:right;margin-right:-35%">MANAGER</h4>
                {{-- <h4 style="line-height: 0px;">Invoice: #{{ $kas->id }}</h4>
                <p><small style="opacity: 0.5;">{{ $kas->created_at->format('d-m-Y H:i:s') }}</small></p> --}}
            </div>
    </div>
</body>
</html>