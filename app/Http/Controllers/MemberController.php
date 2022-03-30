<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'nama_member' => 'required',
            'alamat_member' => 'required',
            'jenis_kelamin' => 'required',
            'no_telp' => 'required',

        ]);

        if($validator->fails()) {
            return Response()->json($validator->errors());

        }

        $member = new Member;
        $member->nama_member = $request->nama_member;
        $member->alamat_member = $request->alamat_member;
        $member->jenis_kelamin = $request->jenis_kelamin;
        $member->no_telp = $request->no_telp;
        $member->save();
        $data = Member::where('id_member', '=', $member->id_member)->first();

        return response()->json([
            'success' => true,
            'message' => 'Data member berhasil ditambahkan',
            'data' => $data
        ]);
    }

    public function getAll(request $request)
    {
        $member = Member::get();
        return Response()->json($member);
    }
    public function show(request $request, $id)
    {
        $member = Member::where('id_member', $id)->first();
        return Response()->json($member);
    }
    
    public function cari_data($key)
    {
        $data = Member::where('nama_member','like','%'.$key.'%')
                ->orWhere('alamat_member','LIKE','%'.$key.'%')
                ->get();
                return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $validator= Validator::make($request->all(),[
            'nama_member' => 'required',
            'alamat_member' => 'required',
            'jenis_kelamin' => 'required',
            'no_telp' => 'required',

        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        $member = Member::where('id_member', '=' ,$id)->first();
        $member->nama_member = $request->nama_member;
        $member->alamat_member = $request->alamat_member;
        $member->jenis_kelamin = $request->jenis_kelamin;
        $member->no_telp = $request->no_telp;
        $member->save();
        $data = Member::where('id_member', '=', $member->id_member)->first();

        return response()->json([
            'success' => true,
            'message' => 'Data member berhasil di Edit !!',
            'data' => $data
        ]);
        
    }

    public function destroy($id)
    {
        $hapus = Member::where('id_member',$id)->delete();

        if($hapus) {
            return response()->json([
                'success' => true,
                'message' => 'Data member berhasil dihapus'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data member gagal dihapus'
            ]);            
        }
    }
}
