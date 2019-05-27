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
        $cek_kas = Kas::where('created_at', '>', Carbon::today())->count();
        if ($cek_kas > 0) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Kas hari ini sudah di INPUT'
            ], 400);
        }
        else {
            return response()->json([
                'status' => 'success',
                'message' => 'Kas Masih Kosong'
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

    public function laporan(Request $request)
    {
        Session::put('kas_start_date', null);
        Session::put('kas_end_date', null);
        $kas = Kas::orderBy('created_at', 'DESC')
                // ->where('created_at', '>', Carbon::today())
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
            $kas = $kas->take(1)->skip(0)->get();
        }

        
        

        // return $kas;

        // if (count($kas) > 0) {
        //     return 'hehe';
        // }
        // else{
        //     return 'hoho';
        // }

        return view('kas.laporan', [
            'kas' => $kas,
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
         $kas = Kas::orderBy('created_at', 'DESC')
                // ->where('created_at', '>', Carbon::today())
                ->with('user');

                // $kas;
       

        if (!empty($start_date)) {            
            $start = Carbon::parse($start_date)->format('Y-m-d') . ' 00:00:01';
            $end = Carbon::parse($start_date)->format('Y-m-d') . ' 23:59:59';

            $kas = $kas->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'DESC')        
            ->first();
        } else {
            $kas = $kas->orderBy('created_at', 'DESC')        
            ->first();
        }

        // return $kas[0]->user->name;

        $pdf = PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif','isRemoteEnabled' => true])->loadView('kas.report.invoice', compact('kas'));

        
        return $pdf->stream();
    }

    public function getTrx()
    {
        $today = 

        $sumOrders = DB::table('orders')
        ->where('created_at', '>', Carbon::today())
        ->where('status','PAID')
        ->sum('total');

        $sumPreorders = DB::table('preorders')
        ->where('created_at', '>', Carbon::today())
        ->where('status','PAID')
        ->sum('total');

        $getSaldoAwal = Kas::select('saldo_awal')
        ->where('created_at', '>', Carbon::today())
        ->first();

        // return $getSaldoAwal->saldo_awal;

        if(!empty( $getSaldoAwal->saldo_awal)) {            
            $saldoResult = $getSaldoAwal->saldo_awal ;
        }
        else {
            $saldoResult = 0;
        }

        $data = $sumOrders + $sumPreorders;

        return response()->json(array(
            'total_transaksi' => $data,
            'saldo_awal' => $saldoResult,
        ), 200);

        
    }
}
