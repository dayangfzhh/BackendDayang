<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paket;
use Illuminate\Support\Facades\Validator;

class PaketController extends Controller
{
    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'jenis' => 'required',
            'harga' => 'required',
    
        ]);

        if($validator->fails()) {
            return Response()->json($validator->errors());

        }

        $paket = new Paket;
        $paket->jenis = $request->jenis;
        $paket->harga = $request->harga;
        $paket->save();
        return response()->json(['message' => 'Berhasil Tambah Paket']);

    }

    public function getAll()
    {
        return Paket::all();
    }
    public function show(request $request, $id)
    {
        $paket = Paket::where('id_paket', $id)->first();
        return Response()->json($paket);
    }

    public function update(Request $request, $id)
    {
        $validator= Validator::make($request->all(),[
            'jenis' => 'required',
            'harga' => 'required',

        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        $paket = Paket::where('id_paket', '=' ,$id)->first();
        $paket->jenis = $request->jenis;
        $paket->harga = $request->harga;
        $paket->save();

        $data = Paket::where('id_paket', '=', $paket->id_paket)->first();

        return response()->json([
            'success' => true,
            'message' => 'Data paket berhasil diubah',
            'data' => $data
        ]);
        
    }

    public function destroy($id)
    {
        $hapus = Paket::where('id_paket',$id)->delete();

        if($hapus) {
            return response()->json([
                'success' => true,
                'message' => "Data paket berhasil dihapus"
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Data paket gagal dihapus"
            ]);
        }
    }
}
