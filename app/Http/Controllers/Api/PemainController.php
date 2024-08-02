<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pemain;
use Validator;
use Storage;

class PemainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pemain = Pemain::latest()->get();
        $res = [
            "success" => true,
            "message" => 'Daftar Pemain Sepak Bola',
            "Data" => $pemain,
        ];
        return response()->json($res, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'nama' => 'required',
            'foto' => 'required|image|mimes:jpg,png',
            'tgl_lahir' => 'required',
            'harga_pasar' => 'required|numeric',
            'posisi' => 'required',
            'negara' => 'required',
            'id_klub' => 'required',
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
            $path = $request->file('foto')->store('public/foto');

            $pemain = new Pemain;
            $pemain->nama = $request->nama;
            $pemain->foto = $path;
            $pemain->tgl_lahir = $request->tgl_lahir;
            $pemain->harga_pasar = $request->harga_pasar;
            $pemain->posisi = $request->posisi;
            $pemain->negara = $request->negara;
            $pemain->id_klub = $request->id_klub;
            $pemain->save();
            return response()->json([
                'success' => true,
                'message' => 'data berhasil di buat',
                'data' => $pemain,
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'nama' => 'required',
            'foto' => 'nullable|image|mimes:jpg,png',
            'tgl_lahir' => 'required',
            'harga_pasar' => 'required|numeric',
            'posisi' => 'required',
            'negara' => 'required',
            'id_klub' => 'required',
        ]);

        if($validate->fails()){
            return response()->json([
                'success' => false,
                'message' => 'validasi gagal',
                'errors' => $validate->errors(),
            ], 422);
        }

        try{
            $pemain = Pemain::findOrFail($id);
            //ganti gambar
            if($request->hasFile('foto')){
                //delete foto / foto lama
                Storage::delete($pemain->foto);

                $path = $request->file('foto')->store('public/foto');
                $pemain->foto = $path;
            }

            $pemain->nama = $request->nama;
            $pemain->tgl_lahir = $request->tgl_lahir;
            $pemain->harga_pasar = $request->harga_pasar;
            $pemain->posisi = $request->posisi;
            $pemain->negara = $request->negara;
            $pemain->id_klub = $request->id_klub;
            $pemain->save();
            return response()->json([
                'success' => true,
                'message' => 'data berhasil di perbarui',
                'data' => $pemain,
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $pemain = Pemain::findOrFail($id);
            Storage::delete($pemain->foto); //tambahan
            $pemain->delete();
            return response()->json([
                'success' => true,
                'message' => 'data '. $pemain->nama . ' berhasil dihapus',
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
