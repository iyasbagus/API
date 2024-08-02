<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fan;
use Validator;
use Storage;

class FanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fans = Fan::with('klub')->latest()->get();
        return response()->json([
            'success' => true,
            'message' => 'Daftar fans',
            'data' => $fans,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'nama' => 'required',
            'klub' => 'required|array',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validate->errors(),
            ], 422);
        }

        try{
            $fan = new Fan;
            $fan->nama = $request->nama;
            $fan->save(); //required

            //lampirkan banyak club
            $fan->klub()->attach($request->klub);

            return response()->json([
                'success' => true,
                'message' => 'data berhasil di buat',
                'data' => $fan,
            ], 201);

        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'nama' => 'required',
            'klub' => 'require|array',
        ]);

        if($validate->fails()){
            return response()->json([
                'success' => false,
                'message' => 'validasi gagal',
                'errors' => $validate->errors(),
            ], 422);
        }

        try{
            $fan = Fan::findOrFail($id);
            $fan->nama = $request->nama;
            $fan->save(); //required

            //lampirkan banyak club
            $fan->klub()->sync($request->klub); //sync ibaratkan refresh data

            return response()->json([
                'success' => true,
                'message' => 'data berhasil di perbarui',
                'data' => $fan,
            ], 201);

        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $fan = Fan::findOrFail($id);
            $fan->klub()->detach(); //detach ibaratkan hapus data
            $fan->delete();

            return response()->json([
                'success' => true,
                'message' => 'data '. $fan->nama . ' berhasil dihapus',
            ], 201);

        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
}
