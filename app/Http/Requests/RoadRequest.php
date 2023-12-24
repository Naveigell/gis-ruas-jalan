<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoadRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "desa_id"       => "required|integer",
            "eksisting_id"  => "required|integer",
            "kondisi_id"    => "required|integer",
            "jenisjalan_id" => "required|integer",
            "kode_ruas"     => "required|string|max:100",
            "nama_ruas"     => "required|string|max:100",
            "lebar"         => "required|numeric",
            "panjang"       => "required|numeric",
            "keterangan"    => "string|max:10000",
            "paths"         => "required|string",
        ];
    }
}
