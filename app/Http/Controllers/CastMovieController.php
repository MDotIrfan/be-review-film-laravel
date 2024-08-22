<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CastMovie;
use Illuminate\Support\Facades\Validator;

class CastMovieController extends Controller
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
        $genres = CastMovie::get();

        //jika tak ada data
        if(!$genres){
            return response()->json([
                'message' => 'Data Tidak Ditemukan',  
            ], 404);;
        }

        //jika berhasil
        return response()->json([
            'message' => 'Berhasil Tampil Cast Movie',
            'data'    => $genres  
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required',
            'cast_id' => 'required|exists:casts,id',
            'movie_id' => 'required|exists:movies,id',
        ]);
        
        //mengirim validasi error jika ada kesalahan input
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //menyimpan ke database
        $genre = CastMovie::create([
            'name'   => $request->name,
            'cast_id' => $request->cast_id,
            'movie_id' => $request->movie_id,
        ]);

        //jika berhasil menyimpan ke database
        if($genre) {

            return response()->json([
                'message' => 'Berhasil tambah cast movie', 
            ], 201);

        } 

        //jika gagal menyimpan ke database
        return response()->json([
            'message' => 'Review tidak Berhasil disimpan',
        ], 409);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //mencari movie berdasarkan ID
        $cast_movie = CastMovie::with([
            'movie', 
            'cast', 
        ])->find($id);

        if(!$cast_movie) {
            return response()->json([
                'message' => 'Cast Movie Tidak Ditemukan',
            ], 404);
        }

        //mengirim response dalam bentuk JSON
        return response()->json([
            'message' => 'Berhasil Tampil Cast Movie',
            'data'    => $cast_movie 
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //membuat error validation required
       $validator = Validator::make($request->all(), [
            'name'   => 'required',
            'cast_id' => 'required|exists:casts,id',
            'movie_id' => 'required|exists:movies,id',
        ]);
    
        //mengirim validasi error jika ada kesalahan input
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //mencari cast berdasarkan ID
        $cast_movie = CastMovie::findOrFail($id);

        if($cast_movie) {

            //mengupdate data cast
            $cast_movie->update([
                'name'   => $request->name,
                'cast_id' => $request->cast_id,
                'movie_id' => $request->movie_id,
            ]);

            return response()->json([
                'message' => 'Berhasil Update cast Movie',
            ], 200);

        }

        //data cast tidak ditemukan
        return response()->json([
            'message' => 'Cast Movie Tidak Ditemukan',
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //mencari cast berdasarkan ID
        $cast_movie = CastMovie::findOrfail($id);

        if($cast_movie) {

            //menghapus cast
            $cast_movie->delete();

            return response()->json([
                'message' => 'Berhasil Delete Cast Movie',
            ], 200);

        }

        //jika data cast tidak ditemukan
        return response()->json([
            'message' => 'Cast Tidak Ditemukan',
        ], 404);
    }
}
