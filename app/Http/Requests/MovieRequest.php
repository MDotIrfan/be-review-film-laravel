<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovieRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'summary' => 'required|string',
            'year' => 'required|integer',
            'poster'=> 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'genre_id' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'title film harus diisi.',
            'summary.required' => 'content summary harus diisi.',
            'title.max' => 'jumlah karakter title maksimal 255',
            'year.required' => 'Tahun Rilis Film harus diisi.',
            'poster.mimes' => 'Format File yang diizinkan diunggah adalah .jpg, .bmp, .png',
            'poster.max' => 'maksimal ukuran file yang bisa diupload adalah 2048 Bytes / 2 MB',
            'genre_id.required' => 'genre harus dipilih.'
        ];
    }
}
