<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\AuctionLot;

class LotStoreRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return [
      'product_id'  => ['required','exists:products,id',
        function($attr,$val,$fail){
          $overlap = AuctionLot::where('product_id',$val)->active()
            ->where(function($q){
              $q->whereBetween('start_at',[request('start_at'),request('end_at')])
                ->orWhereBetween('end_at',[request('start_at'),request('end_at')])
                ->orWhere(function($qq){
                  $qq->where('start_at','<=',request('start_at'))
                     ->where('end_at','>=',request('end_at'));
                });
            })->exists();
          if ($overlap) $fail('Produk ini sudah terjadwal/berjalan pada rentang waktu tersebut.');
        }
      ],
      'start_price' => ['required','numeric','min:0'],
      'increment'   => ['required','numeric','min:0.01'],
      'start_at'    => ['required','date','after_or_equal:today'],
      'end_at'      => ['required','date','after:start_at'],
    ];
  }
  // public function messages(): array
  // {
  //     return [
  //         'product_id.required' => 'Silakan pilih produk.',
  //         'product_id.exists'   => 'Produk yang dipilih tidak ditemukan.',
  //         'title.required'      => 'Nama lelang wajib diisi.',
  //         'title.max'           => 'Nama lelang maksimal 180 karakter.',
  //         'start_price.required'=> 'Harga awal wajib diisi.',
  //         'start_price.numeric' => 'Harga awal harus berupa angka.',
  //         'start_price.min'     => 'Harga awal tidak boleh negatif.',
  //         'increment.required'  => 'Increment wajib diisi.',
  //         'increment.numeric'   => 'Increment harus berupa angka.',
  //         'increment.min'       => 'Increment minimal 0.01.',
  //         'start_at.required'   => 'Tanggal mulai wajib diisi.',
  //         'start_at.date'       => 'Format tanggal mulai tidak valid.',
  //         'start_at.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
  //         'end_at.required'     => 'Tanggal selesai wajib diisi.',
  //         'end_at.date'         => 'Format tanggal selesai tidak valid.',
  //         'end_at.after'        => 'Tanggal selesai harus setelah tanggal mulai.',
  //     ];
  // }
}
