<?php

namespace App\Http\Controllers;

use App\Kas;
use Session;
use App\User;
use App\Order;
use Carbon\Carbon;
use App\Order_detail;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KasController extends Controller
{

    public function cekKas()
    {
        // $cek_kas = Kas::where('created_at', '>', Carbon::today())->count();
        $cek_kas = DB::table('kas')->orderBy('id', 'desc')->take(1)->get();
        $id_user = DB::table('users')->orderBy('id', 'asc')->take(1)->get();
        // return $cek_kas[0]->id;

        if ($cek_kas->isEmpty()) {

            $kas = Kas::create(array(
                'user_id'       => $id_user[0]->id,
                'saldo_awal'    => 0,
                'transaksi'     => 0,
                'saldo_akhir'   => 1
            ));

            return response()->json([
                'status' => 'counted',
                'message' => 'Kas Sudah dihitung',
            ], 200);
        }
        

        if ($cek_kas[0]->saldo_akhir > 0) {
            return response()->json([
                'status' => 'counted',
                'message' => 'Kas Sudah dihitung',
                'id_kas' => $cek_kas[0]->id,
            ], 200);
        }
        else {
            return response()->json([
                'status' => 'notcounted',
                'message' => 'Kas Belum dihitung',
                'id_kas' => $cek_kas[0]->id,
            ], 200);            
        }
    }

    public function postKas(Request $request)
    {

        //Check apakah user punya role 
        $get_role = User::role(['admin', 'manager'])
            ->where('username', $request[0]['username_approval'])->count();

        //Jika user sudah punya role admin / approver selanjutnya di cek password nya
        if (auth()->attempt(['username' => $request[0]['username_approval'], 'password' => $request[0]['pin_approval'], 'status' => 1]) && $get_role > 0) {
            DB::beginTransaction();
        try {
            $kas = Kas::create(array(
                'user_id'       => $request[0]['user_id'],
                'saldo_awal'    => $request[0]['saldo_awal'],
                'transaksi'     => $request[0]['transaksi'],
                'saldo_akhir'   => $request[0]['saldo_akhir']
            ));
            // return response($result);
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => $kas,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], 400);
        }
        }
        else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid Username / PIN'
            ], 400);
        }
        // return $users;
        
        
    }

    public function updateKas(Request $request,$id)
    {

        //Check apakah user punya role 
        $get_role = User::role(['admin', 'manager'])
            ->where('username', $request[0]['username_approval'])->count();

        //Jika user sudah punya role admin / approver selanjutnya di cek password nya
        if (auth()->attempt(['username' => $request[0]['username_approval'], 'password' => $request[0]['pin_approval'], 'status' => 1]) && $get_role > 0) {
            
        
            
            $transaksi = $request[0]['transaksi'];
            $saldo_akhir = $request[0]['saldo_akhir'];
            $diskon = $request[0]['diskon'];
            $tgl_hitung = $request[0]['tgl_hitung'];
            $refund = $request[0]['refund'];

            // return $saldo_akhir;

            $kas = DB::table('kas')->where('id', $id)->update(
                [
                'transaksi'   => $transaksi,
                'saldo_akhir' => $saldo_akhir,
                'diskon' => $diskon,
                'tgl_hitung' => $tgl_hitung,
                'total_refund' => $refund,
                ]
            );

            

            return response()->json([
                'status' => 'success',
                'message' => $kas,
            ]);        
        }
        else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid Username / PIN'
            ], 400);
        }
        // return $users;
        
        
    }

    public function laporan(Request $request)
    {
        
        Session::put('kas_start_date', null);
        Session::put('kas_end_date', null);
        $kas = Kas::orderBy('created_at', 'ASC')
                ->where('saldo_akhir', '<>', 1)
                ->with('user');

        if (!empty($request->user_id)) {
            $kas = $kas->where('user_id', $request->user_id);
        }

        if (!empty($request->start_date)) {
            $this->validate($request, [
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date'
            ]);
            $start_date = Carbon::parse($request->start_date)->format('Y-m-d') . ' 00:00:01';
            $end_date = Carbon::parse($request->start_date)->format('Y-m-d') . ' 23:59:59';

            Session::put('kas_start_date', $start_date);
            Session::put('kas_end_date', $end_date);
            
            $kas = $kas->whereBetween('created_at', [$start_date, $end_date])->get();
        } else {
            // $kas = $kas->take(1)->skip(0)->get();
            $kas = $kas->where('created_at', '>', Carbon::today())->take(100)->orderBy('created_at', 'ASC')->get();
        }

        $phd_today = Carbon::now()->toDateString();
        // 'phd_today' => $phd_today,
        

        // return $kas;

        // if (count($kas) > 0) {
        //     return 'hehe';
        // }
        // else{
        //     return 'hoho';
        // }
        // return $kas;

        return view('kas.laporan', [
            'kas' => $kas,
            'phd_today' => $phd_today,
            // 'sold' => $this->countItem($orders),
            // 'total' => $this->countTotal($orders),
            // 'total_customer' => $this->countCustomer($orders),
            // 'total_harga' => $this->countTotal_transaksi($kas),
            
        ]);
    }

    public function invoicePdf()
    {
        $start_date = Session::get('kas_start_date');
        $end_date = Session::get('kas_end_date');
        
        // return $start_date;
        //  
         $kas = Kas::orderBy('created_at', 'ASC')
                // ->where('created_at', '>', Carbon::today())
                ->with('user');

                // $kas;
       

                  
            $start = Carbon::parse($start_date)->format('Y-m-d') . ' 00:00:01';
            $end = Carbon::parse($start_date)->format('Y-m-d') . ' 23:59:59';

            $kas = $kas->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'DESC')->get();
        
            $start_date_lap = Carbon::parse($start_date)->format('d/m/Y');

        // return $kas->user->name;

        $pdf = PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif','isRemoteEnabled' => true])
        ->loadView('kas.report.invoice', compact('kas','start_date_lap'));

        
        return $pdf->stream();
    }

    public function getTrx()
    {

        $cek_kas = DB::table('kas')->orderBy('id', 'desc')->take(2)->get();
        $cek_kas[0]->created_at;

        // if (empty($cek_kas[1])) {
        //     $cek_kas[1] = $cek_kas[0]; 
        // }
        // else {
        //     $cek_kas[1] = $cek_kas[1];
        // }

        $sumOrders = DB::table('orders')
        ->where('created_at', '>', $cek_kas[0]->created_at)
        ->where('status','PAID')
        ->sum('total');

        $sumPreordersDP = DB::table('preorders')
        ->where('created_at', '>', $cek_kas[0]->created_at)
        ->where('status','UNPAID')
        // ->where('hari_pelunasan','notsameday')
        ->sum('uang_muka');

        // $sumPreordersBayarSame = DB::table('preorders')
        // ->where('tgl_pesan', '>', $cek_kas[1]->created_at)
        // ->where('tgl_pesan', '<', $cek_kas[1]->tgl_hitung)        
        // ->where('status','PAID')
        // ->where('hari_pelunasan','sameday')
        // ->sum('uang_dibayar');

        // $sumPreordersKembaliSame = DB::table('preorders')
        // ->where('tgl_pesan', '>', $cek_kas[1]->created_at)
        // ->where('tgl_pesan', '<', $cek_kas[1]->tgl_hitung)        
        // ->where('status','PAID')
        // ->where('hari_pelunasan','sameday')
        // ->sum('uang_kembali');

        $sumPreordersBayarSame = DB::table('preorders')
        ->where('tgl_pesan', '<', $cek_kas[0]->created_at)
        ->where('tgl_pesan', '>', $cek_kas[1]->created_at)        
        ->where('status','PAID')
        ->where('hari_pelunasan','sameday')
        ->sum('uang_dibayar');

        $sumPreordersKembaliSame = DB::table('preorders')
        ->where('tgl_pesan', '<', $cek_kas[0]->created_at)
        ->where('tgl_pesan', '>', $cek_kas[1]->created_at)        
        ->where('status','PAID')
        ->where('hari_pelunasan','sameday')
        ->sum('uang_kembali');

        //Untuk menangani ketika DP dan pelunanasn di beda kas di hari yg sama
        $pelunasanSamaHari = $sumPreordersBayarSame - $sumPreordersKembaliSame;

        // return $sumPreordersBayarSame;

        //Untuk menangani ketika DP dan pelunanasn di satu kas bersamaan di hari yg sama
        $sumSamedayTotal = DB::table('preorders')
        ->where('tgl_pesan', '>', $cek_kas[0]->created_at)
        ->where('status','PAID')
        ->where('hari_pelunasan','sameday')
        ->sum('total');

        // return $sumPreordersKembaliSame;

        // return $cek_kas[0]->created_at;

        

        

        $sumPreordersBayarNotSame = DB::table('preorders')
        ->where('created_at', '>', $cek_kas[0]->created_at)
        ->where('status','PAID')
        ->where('hari_pelunasan','notsameday')
        ->sum('uang_dibayar');

        $sumPreordersKembali = DB::table('preorders')
        ->where('created_at', '>', $cek_kas[0]->created_at)
        ->where('status','PAID')
        ->where('hari_pelunasan','notsameday')
        ->sum('uang_kembali');
        
        //Untuk menangani ketika DP dan pelunanasn di beda kas di hari yg beda
        $pelunasanBedaHari = $sumPreordersBayarNotSame - $sumPreordersKembali;

        $sumRefunds = DB::table('refunds')
        ->where('created_at', '>', $cek_kas[0]->created_at)
        // ->where('status','PAID')
        ->sum('total');

        $sumDiskonPreorders = DB::table('preorders')
        ->where('created_at', '>', $cek_kas[0]->created_at)
        ->where('status','PAID')
        ->sum('discount');

        $sumDiskonOrders = DB::table('orders')
        ->where('created_at', '>', $cek_kas[0]->created_at)
        ->where('status','PAID')
        ->sum('discount');

        $getSaldoAwal = Kas::select('saldo_awal')
        // ->where('created_at', '>', $cek_kas[0]->created_at)
        ->orderBy('id', 'desc')
        ->first();        


        // return $getSaldoAwal->saldo_awal;

        if(!empty( $getSaldoAwal->saldo_awal)) {            
            $saldoResult = $getSaldoAwal->saldo_awal ;
        }
        else {
            $saldoResult = 0;
        }

        $data = $sumOrders + $sumPreordersDP + $pelunasanSamaHari + $sumSamedayTotal + $pelunasanBedaHari;        
        $diskon = $sumDiskonOrders + $sumDiskonPreorders;


        return response()->json(array(
            'total_transaksi' => $data,
            'total_orders' => $sumOrders,
            'total_dp_preorders' => $sumPreordersDP,
            'total_pelunasan_preorders' => $pelunasanSamaHari + $pelunasanBedaHari + $sumSamedayTotal,
            'diskon' => $diskon,
            'saldo_awal' => $saldoResult,
            'total_refund' => $sumRefunds
        ), 200);

        
    }

    public function CheckApproval(Request $request)
    {
        //Check apakah user punya role 
        $get_role = User::role(['admin', 'manager'])
        ->where('username', $request[0]['username_approval'])->count();

        //Jika user sudah punya role admin / approver selanjutnya di cek password nya
        if (auth()->attempt(['username' => $request[0]['username_approval'], 'password' => $request[0]['pin_approval'], 'status' => 1]) && $get_role > 0) {

        return response()->json([
                'status' => 'success',
                'message' => 'user approve',
        ], 200);        

        }
        else {
            return response()->json([
                'status' => 'failed',
                'message' => 'user not approve'
            ], 200);
        }
    }
}
