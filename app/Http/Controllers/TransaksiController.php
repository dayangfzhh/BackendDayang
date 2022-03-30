<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi;
use App\Models\User;
use App\Models\DetailTransaksi;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon; //untuk memanggil tanggal
use JWTAuth;

class TransaksiController extends Controller
{

    //public $response;
    public $user; //untuk menampung data user yang sedang login

    public function __construct() //sebagai method yang otomatis dipanggil/tidak perlu diambil manual
    {
        $this-> user = JWTAuth::parseToken()->authenticate();
    }

    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[    
            'id_member' => 'required',

        ]);

        if($validator->fails()) {
            return Response()->json($validator->errors());

        }

        $transaksi = new Transaksi();
        $transaksi->id_member = $request->id_member;
        $transaksi->tanggal = Carbon::now(); //gunanya carbon supaya scr otomatis mengambil tanggal
        $transaksi->batas_waktu = Carbon::now()->addDays(3); //dibuat prosesnya selama 3 hari
        //$transaksi->tanggal_bayar = $request->tanggal_bayar;
        $transaksi->status = 'baru';
        $transaksi->dibayar = 'belum_dibayar';
        $transaksi->id = $this->user->id;
        $transaksi->total = 0;
        $transaksi->save();

        $data = Transaksi::where('id_transaksi', '=' ,$transaksi->id_transaksi)->first(); 
        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil ditambahkan',
            'data' => $data
        ]);

    }
    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_member' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors());
        }

        $transaksi = Transaksi::where('id_transaksi', '=', $id)->first();
        $transaksi->id_member = $request->id_member;

        $transaksi->save();

        $data = Transaksi::where('id_transaksi', '=' ,$transaksi->id_transaksi)->first(); 
        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil diubah',
            'data' => $data
        ]);
    }

    public function getAll()
        {

        $id_user = $this->user->id;
        $data_user = User::where('id', '=', $id_user)->first();        
        
        $data = DB::table('transaksi')->join('member', 'transaksi.id_member', '=', 'member.id_member')
                                      ->join('users', 'transaksi.id', 'users.id')
                                      ->select('transaksi.*', 'member.nama_member' , 'users.name')
                                    //   ->select('transaksi.id', 'member.nama_member', 'transaksi.tanggal', 'transaksi.status' , 'users.name')
                                      ->where('users.id_outlet', $data_user->id_outlet)
                                      ->orderBy('transaksi.tanggal', 'DESC')
                                      ->get();
        
        return response()->json(['success' => true, 'data' => $data]);
        
        }

    public function getById($id)
    {
        $data = Transaksi::where('id_transaksi', '=', $id)->first();  
        $data = DB::table('transaksi')->join('member', 'transaksi.id_member', '=', 'member.id_member')
                                      ->join('users', 'users.id', '=', 'users.id')       
                                      ->select('transaksi.*', 'member.nama_member','users.name')
                                      ->where('transaksi.id_transaksi', '=', $id)
                                      ->first();
        return response()->json($data);
    }
    public function cari_data($key)
    {
        $id_user = $this->user->id;
        $data_user = User::where('id', '=', $id_user)->first();    

        $data = DB::table('transaksi')
                ->join('users', 'transaksi.id', 'users.id')
                ->join('member','transaksi.id_member','=','member.id_member')
                ->select('transaksi.*','member.nama_member', 'users.name')
                ->where('users.id_outlet', $data_user->id_outlet)
                ->where('nama_member','like','%' .$key.'%')
                ->orWhere('status','like','%' .$key.'%')
                ->orWhere('name','like','%' .$key.'%')
                ->orderBy('transaksi.tanggal', 'DESC')
                ->get();
                return response()->json($data);
    }

    public function changeStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required'
        ]);
        
        if($validator->fails()) {
            return response()->json($validator->errors());
        }
        
        $transaksi = Transaksi::where('id_transaksi', '=', $id)->first();
        $transaksi->status = $request->status;
        
        $transaksi->save();
        
        return response()->json(['message' => 'Status berhasil diubah']);
    }

    public function bayar($id)
    {
        $transaksi = Transaksi::where('id_transaksi', '=', $id)->first();
        $subtotal = DetailTransaksi::where('id_transaksi', $id)->sum('subtotal');

        $transaksi->tanggal_bayar = Carbon::now();
        $transaksi->status = "diambil";
        $transaksi->dibayar = "dibayar";
        $transaksi->total = $subtotal; 
                
        
        $transaksi->save();
        
        return response()->json(['message' => 'Pembayaran berhasil']);
    }
    
    public function report(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_outlet' => 'required',
            'tahun' => 'required',
            'bulan' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $id_outlet = $request->id_outlet;

        $data = DB::table('transaksi')
            ->join('member', 'transaksi.id_member', '=', 'member.id_member')
            ->join('users', 'users.id','=','transaksi.id')
            ->select('transaksi.id', 'transaksi.tanggal', 'transaksi.tanggal_bayar', 'transaksi.total', 'member.nama_member','users.name')
            ->where('users.id_outlet', '=', $id_outlet)
            ->whereYear('tanggal', '=', $tahun)
            ->whereMonth('tanggal', '=', $bulan)
            ->orderBy('transaksi.tanggal','ASC')
            ->get();

        return response()->json($data);
    }
}
