<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'name' => 'required',
            'username' => 'required',
            'password' => 'required|min:6',
            'level' => 'required',
            'id_outlet' => 'required'
    
        ]);

        if($validator->fails()) {
            return Response()->json($validator->errors());

        }

        $user = new User;
        $user->name = $request->name;
        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $user->level = $request->level;
        $user->id_outlet = $request->id_outlet;
        $user->save();

        $data = User::where('id', '=', $user->id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Registrasi User',
            'data' => $data
        ]);
    }

    public function cari_data($key)
    {
        $data = DB::table('users')
                ->join('outlet','users.id_outlet','=','outlet.id_outlet')
                ->select('users.*','outlet.nama_outlet')
                ->where('name','like','%' .$key.'%')
                ->orwhere('username','like','%' .$key.'%')
                ->orwhere('level','like','%' .$key.'%')
                ->orwhere('nama_outlet','like','%' .$key.'%')
                ->get();
                return response()->json($data);
    }

    public function getAll()
	{
		$data = DB::table('users')->join('outlet', 'users.id_outlet', '=', 'outlet.id_outlet')
								  ->select('users.*', 'outlet.nama_outlet')
								  ->get();
		
		return response()->json($data);
	}

    public function show(request $request, $id)
    {
        $user = User::where('id', $id)->first();
        return Response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $validator= Validator::make($request->all(),[
            'name' => 'required',
            'username' => 'required',
            'password' => 'required',
            'level' => 'required',
            'id_outlet' => 'required'

        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }

        $user = User::where('id', '=' ,$id)->first();
        $user->name = $request->name;
        $user->username = $request->username;
        if($request->password_edit != NULL){
            $user->password = Hash::make($request->password_edit);
        }
        $user->level = $request->level;
        $user->id_outlet = $request->id_outlet;
        $user->save();
        
        $data = User::where('id', '=', $user->id)->first();
        return response()->json([
            'success' => true,
            'message' => 'Data User Berhasil update',
            'data' => $data
        ]);
        
    }

    public function destroy($id)
    {
        $hapus = User::where('id',$id)->delete();

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
