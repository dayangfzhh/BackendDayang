<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use App\Models\Paket;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use JWTAuth;

class DetailTransaksiController extends Controller
{
    public $user;
    public $response;
    public function __construct()
    {
        $this->response = new ResponseHelper();
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_transaksi' => 'required',
            'id_paket' => 'required',
            'qty' => 'required',
            
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors());
        }
        
        $detil = new DetailTransaksi();
        $detil->id_transaksi = $request->id_transaksi;
        $detil->id_paket = $request->id_paket;

        //GET HARGA PAKET
        $paket = Paket::where('id_paket', '=', $detil->id_paket)->first();
        $harga = $paket->harga;

        $detil->qty = $request->qty;
        $detil->subtotal = $detil->qty * $harga;
        $detil->save();

        $transaksi = Transaksi::find($request->id_transaksi);
        $total_transaksi = DetailTransaksi::where('id_transaksi', $request->id_transaksi)->sum('subtotal');
        $transaksi->update(['total' => $total_transaksi]);
        $data = DetailTransaksi::where('id_transaksi', '=', $detil->id_transaksi)->first();
        return response()->json(['message' => 'Berhasil tambah detil transaksi', 'data' => $data]);
    }

    public function getById($id)
    {
        //untuk ambil detil dari transaksi tertentu

        $data = DB::table('detail_transaksi')->join('paket', 'detail_transaksi.id_paket', 'paket.id_paket')
                                            ->select('detail_transaksi.*', 'paket.jenis')
                                            ->where('detail_transaksi.id_transaksi', '=', $id)
                                            ->get();
        return response()->json($data);                        
    }


    public function getTotal($id)
    {
        $total = DetailTransaksi::where('id_transaksi', $id)->sum('subtotal');
        
        return response()->json([
            'total' => $total
        ]);
    }
}
