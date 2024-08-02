<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Klub;
use Validator;
use Storage;

class KlubController extends Controller
{
    public function index()
    {
        $klub = Klub::latest()->get();
        $res = [
            "success" => true,
            "message" => 'Daftar Klub Sepak Bola',
            "Data" => $klub,
        ];
        return response()->json($res, 200);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'nama_klub' => 'required',
            'logo' => 'required|image|max:2048',
            'id_liga' => 'required',
        ]);

        if($validate->fails()){
            return response()->json([
                'success' => false,
                'message' => 'validasi gagal',
                'errors' => $validate->errors(),
            ], 422);
        }

        try{
            //upload gambar
            $path = $request->file('logo')->store('public/logo');

            $klub = new klub;
            $klub->nama_klub = $request->nama_klub;
            $klub->logo = $path;
            $klub->id_liga = $request->id_liga;
            $klub->save();
            return response()->json([
                'success' => true,
                'message' => 'data berhasil di buat',
                'data' => $klub,
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }

    public function show($id)
    {
        try{
            $klub = Klub::find($id);
            return response()->json([
                'success' => true,
                'message' => 'detail klub',
                'data' => $klub,
            ], 201);
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'nama_klub' => 'required',
            'logo' => 'nullable|image|max:2048',
            'id_liga' => 'required',
        ]);

        if($validate->fails()){
            return response()->json([
                'success' => false,
                'message' => 'validasi gagal',
                'errors' => $validate->errors(),
            ], 422);
        }

        try{
            $klub = Klub::findOrFail($id);
            if($request->hasFile('logo')){
                //delete foto / foto lama
                Storage::delete($klub->logo);
                $path = $request->file('logo')->store('public/logo');
                $klub->logo = $path;
            }

            $klub->nama_klub = $request->nama_klub;
            $klub->id_liga = $request->id_liga;
            $klub->save(); //required
            return response()->json([
                'success' => true,
                'message' => 'data berhasil di perbarui',
                'data' => $klub,
            ], 200);

        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }

    public function destroy($id)
    {
        try{
            $klub = klub::findOrFail($id);
            Storage::delete($klub->logo); //tambahan
            $klub->delete();
            return response()->json([
                'success' => true,
                'message' => 'data '. $klub->nama_klub . ' berhasil dihapus',
            ], 200);
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }
}
