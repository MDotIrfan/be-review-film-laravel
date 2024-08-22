<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Casts;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CastRequest;

class CastController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api','isAdmin'])->only(['store','update','destroy']);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //ambil data terbaru dari tabel casts
        $casts = Casts::get();

        //jika tak ada data
        if(!$casts){
            return response()->json([
                'message' => 'Data Tidak Ditemukan',  
            ], 404);;
        }

        //jika berhasil
        return response()->json([
            'message' => 'Berhasil Tampil Semua Cast',
            'data'    => $casts  
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CastRequest $request)
    {
        //membuat error validation required
        $validator = Validator::make($request->all(), [
            'age'   => 'required|integer',
            'name' => 'required',
            'bio' => 'required',
        ]);
        
        //mengirim validasi error jika ada kesalahan input
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //menyimpan ke database
        $cast = Casts::create([
            'name'     => $request->name,
            'age'   => $request->age,
            'bio' => $request->bio,
        ]);

        //jika berhasil menyimpan ke database
        if($cast) {

            return response()->json([
                'message' => 'Berhasil Tambah Cast',
            ], 201);

        } 

        //jika gagal menyimpan ke database
        return response()->json([
            'message' => 'Cast tidak Berhasil disimpan',
        ], 409);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //mencari cast berdasarkan ID
        $casts = Casts::with('list_movies')->find($id);

        //jika tak ada data
        if(!$casts){
            return response()->json([
                'message' => 'Data Tidak Ditemukan',  
            ], 404);
        }

        //mengirim response dalam bentuk JSON
        return response()->json([
            'message' => 'Berhasil detail data dengan id '.$id,
            'data'    => $casts 
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CastRequest $request, string $id)
    {
       //membuat error validation required
       $validator = Validator::make($request->all(), [
        'age'   => 'required',
        'name' => 'required',
        'bio' => 'required',
        ]);
    
        //mengirim validasi error jika ada kesalahan input
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //mencari cast berdasarkan ID
        $cast = Casts::findOrFail($id);

        if($cast) {

            //mengupdate data cast
            $cast->update([
                'name'     => $request->name,
                'age'   => $request->age,
                'bio' => $request->bio,
            ]);

            return response()->json([
                'message' => 'Berhasil melakukan update Cast id : '.$id,
            ], 200);

        }

        //data cast tidak ditemukan
        return response()->json([
            'message' => 'Cast Tidak Ditemukan',
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //mencari cast berdasarkan ID
        $cast = Casts::findOrfail($id);

        if($cast) {

            //menghapus cast
            $cast->delete();

            return response()->json([
                'message' => 'Data dengan id : '.$id.' berhasil terhapus',
            ], 200);

        }

        //jika data cast tidak ditemukan
        return response()->json([
            'message' => 'Cast Tidak Ditemukan',
        ], 404);
    }
}
