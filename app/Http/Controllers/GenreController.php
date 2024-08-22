<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genres;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\GenreRequest;

class GenreController extends Controller
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
        $genres = Genres::get();

        //jika tak ada data
        if(!$genres){
            return response()->json([
                'message' => 'Data Tidak Ditemukan',  
            ], 404);;
        }

        //jika berhasil
        return response()->json([
            'message' => 'Berhasil Tampil Semua Genre',
            'data'    => $genres  
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GenreRequest $request)
    {
         //membuat error validation required
         $validator = Validator::make($request->all(), [
            'name'   => 'required',
        ]);
        
        //mengirim validasi error jika ada kesalahan input
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //menyimpan ke database
        $genre = Genres::create([
            'name'     => $request->name,
        ]);

        //jika berhasil menyimpan ke database
        if($genre) {

            return response()->json([
                'message' => 'Berhasil Tambah Genre ',
            ], 201);

        } 

        //jika gagal menyimpan ke database
        return response()->json([
            'message' => 'Genre tidak Berhasil disimpan',
        ], 409);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
         //mencari genre berdasarkan ID
         $genre = Genres::with('list_movies')->find($id);

         //jika tak ada data
         if(!$genre){
             return response()->json([
                 'message' => 'Data Tidak Ditemukan',  
             ], 404);;
         }
 
         //mengirim response dalam bentuk JSON
         return response()->json([
             'message' => 'Berhasil Detail data dengan id '.$id,
             'data'    => $genre 
         ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GenreRequest $request, string $id)
    {
        //membuat error validation required
       $validator = Validator::make($request->all(), [
        'name'   => 'required',
        ]);
    
        //mengirim validasi error jika ada kesalahan input
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //mencari genre berdasarkan ID
        $genre = Genres::findOrFail($id);

        if($genre) {

            //mengupdate data genre
            $genre->update([
                'name'     => $request->name,
            ]);

            return response()->json([
                'message' => 'Berhasil melakukan update Genre id : '.$id,
            ], 200);

        }

        //data genre tidak ditemukan
        return response()->json([
            'message' => 'Genre Tidak Ditemukan',
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //mencari genre berdasarkan ID
        $genre = Genres::findOrfail($id);

        if($genre) {

            //menghapus genre
            $genre->delete();

            return response()->json([
                'message' => 'data dengan id : '.$id.' berhasil terhapus',
            ], 200);

        }

        //jika data genre tidak ditemukan
        return response()->json([
            'message' => 'Genre Tidak Ditemukan',
        ], 404);
    }
}
