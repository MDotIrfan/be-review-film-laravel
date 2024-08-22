<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Genres;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\MovieRequest;
use Illuminate\Support\Facades\Storage;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class MovieController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'isAdmin'])->only(['store', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movies = Movie::get();

        //jika tak ada data
        if (!$movies) {
            return response()->json([
                'message' => 'Data Tidak Ditemukan',
            ], 404);
            ;
        }

        //jika berhasil
        return response()->json([
            'message' => 'Data Berhasil ditampilkan',
            'data' => $movies
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MovieRequest $request)
    {
        //membuat error validation required
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'summary' => 'required',
            'year' => 'required',
            'genre_id' => 'required|exists:genres,id',
            'poster' => 'mimes:jpg,jpeg,png'
        ]);

        //mengirim validasi error jika ada kesalahan input
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $genre = Genres::find($request->genre_id);

        //jika tak ada data genre
        if (!$genre) {
            return response()->json([
                'message' => 'Data Genre Tidak Ditemukan',
            ], 404);
            ;
        }

        $data = $request->validated();

        // jika file gambar diinput

        if ($request->hasFile('poster')) {

            // membuat unique name pada gamabr yang di input

            $imageName = time() . '.' . $request->poster->extension();

            // simpan gambar pada file storage

            $uploadedFileUrl = Cloudinary::upload($request->file('poster')->getRealPath())->getSecurePath();

            $data['poster'] = $uploadedFileUrl;

        }

        //menyimpan ke database
        $movie = Movie::create($data);

        //jika berhasil menyimpan ke database
        if ($movie) {

            return response()->json([
                'message' => 'Data berhasil ditambahkan',
            ], 201);

        }

        //jika gagal menyimpan ke database
        return response()->json([
            'message' => 'Movie tidak Berhasil disimpan',
        ], 409);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //mencari movie berdasarkan ID
        $movie = Movie::with([
            'genre',
            'list_cast',
            'list_reviews'
        ])->find($id);

        if (!$movie) {
            return response()->json([
                'message' => 'Movie Tidak Ditemukan',
            ], 404);
        }

        //mengirim response dalam bentuk JSON
        return response()->json([
            'message' => 'Data Detail ditampilkan',
            'data' => $movie
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MovieRequest $request, string $id)
    {
        //membuat error validation required
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'summary' => 'required',
            'year' => 'required',
            'genre_id' => 'required',
        ]);

        //mengirim validasi error jika ada kesalahan input
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $genre = Genres::find($request->genre_id);

        //jika tak ada data genre
        if (!$genre) {
            return response()->json([
                'message' => 'Data Genre Tidak Ditemukan',
            ], 404);
            ;
        }

        //mencari movie berdasarkan ID
        $movie = Movie::find($id);

        if (!$movie) {
            return response()->json([
                'message' => 'Movie Tidak Ditemukan',
            ], 404);
        }

        $data = $request->validated();

        if ($request->hasFile('poster')) {

            // Hapus gambar lama jika ada

            if ($movie->poster) {

                Storage::delete('public/images/' . $movie->poster);

            }

            $imageName = time() . '.' . $request->poster->extension();

            // $request->poster->storeAs('public/images', $imageName);

            $uploadedFileUrl = Cloudinary::upload($request->file('poster')->getRealPath())->getSecurePath();

            $data['poster'] = $uploadedFileUrl;

        }

        $movie->update($data);

        return response()->json([
            'message' => 'Data Berhasil diupdate',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $movie = Movie::find($id);

        if (!$movie) {
            return response()->json([
                'message' => 'Movie Tidak Ditemukan',
            ], 404);
        }

        if ($movie->poster) {

            Storage::delete('public/images/' . $movie->poster);

        }

        if ($movie) {

            //menghapus movie
            $movie->delete();

            return response()->json([
                'message' => 'Data Berhasil dihapus',
            ], 200);

        }

        //jika data movie tidak ditemukan
        return response()->json([
            'message' => 'Movie Tidak Ditemukan',
        ], 404);
    }
}
