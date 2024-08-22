<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Validator;
// use App\Http\Requests\GenreRequest;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api'])->only('store');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $genres = Review::get();

        //jika tak ada data
        if(!$genres){
            return response()->json([
                'message' => 'Data Tidak Ditemukan',  
            ], 404);;
        }

        //jika berhasil
        return response()->json([
            'message' => 'Tampil Data Berhasil',
            'data'    => $genres  
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'critics'   => 'required',
            'rating' => 'required|integer|between:1,5',
            'movie_id' => 'required|exists:movies,id',
        ]);
        
        //mengirim validasi error jika ada kesalahan input
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $currentUser=auth()->user();

        //menyimpan ke database
        $genre = Review::updateOrCreate(
            ['user_id'=>$currentUser->id],
            [
                'critic'   => $request->critics,
                'rating' => $request->rating,
                'user_id' => $currentUser->id,
                'movie_id' => $request->movie_id,
            ]);

        //jika berhasil menyimpan ke database
        if($genre) {

            return response()->json([
                'message' => 'Review Berhasil ditambah / dibuat',
                'data'    => $genre  
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
