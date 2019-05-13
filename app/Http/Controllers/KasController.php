<?php

namespace App\Http\Controllers;

use App\Kas;
use App\User;
use App\Order;
use Carbon\Carbon;
use App\Order_detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KasController extends Controller
{
    public function postKas(Request $request)
    {
        //Check apakah user punya role 
        $get_role = User::role(['admin', 'kasir'])
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
        $kas = Kas::orderBy('created_at', 'DESC')->with('user');

        if (!empty($request->user_id)) {
            $kas = $kas->where('user_id', $request->user_id);
        }

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $this->validate($request, [
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date'
            ]);
            $start_date = Carbon::parse($request->start_date)->format('Y-m-d') . ' 00:00:01';
            $end_date = Carbon::parse($request->end_date)->format('Y-m-d') . ' 23:59:59';

            $kas = $kas->whereBetween('created_at', [$start_date, $end_date])->get();
        } else {
            $kas = $kas->take(10)->skip(0)->get();
        }

        return view('kas.laporan', [
            'kas' => $kas,
            // 'sold' => $this->countItem($orders),
            // 'total' => $this->countTotal($orders),
            // 'total_customer' => $this->countCustomer($orders),
            // 'total_harga' => $this->countTotal_transaksi($kas),
            
        ]);
    }

    public function getTrx()
    {
        $today = 

        $sumOrders = DB::table('orders')
        ->where('created_at', '>', Carbon::today())
        ->sum('total');

        $sumPreorders = DB::table('preorders')
        ->where('created_at', '>', Carbon::today())
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
