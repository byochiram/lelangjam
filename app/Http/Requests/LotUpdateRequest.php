<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use App\Models\AuctionLot;

class LotUpdateRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    protected function prepareForValidation()
    {
        // hapus titik pemisah ribuan sebelum divalidasi
        foreach (['start_price','increment'] as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => preg_replace('/[^\d]/', '', $this->$field),
                ]);
            }
        }
    }

    public function rules(): array
    {
        /** @var \App\Models\AuctionLot|null $lot */
        $lot    = $this->route('lot');
        $status = $lot?->runtime_status;
        $id     = $lot?->id;

        if ($status === 'ACTIVE') {
            return [
                'end_at' => [
                    'required','date',
                    function ($attr, $val, $fail) use ($lot) {
                        $newEnd = Carbon::parse($val);

                        // dan wajib setelah sekarang
                        if ($newEnd->lessThanOrEqualTo(now())) {
                            $fail('Waktu selesai harus setelah waktu saat ini.');
                        }
                    },
                ],
            ];
        }

        // SCHEDULED (atau lainnya) → aturan penuh seperti sebelumnya
        return [
            'product_id'  => ['required','exists:products,id',
                function($attr,$val,$fail) use ($id){
                    $overlap = AuctionLot::where('product_id',$val)
                        ->where('id','!=',$id)
                        ->active()
                        ->where(function($q){
                            $q->whereBetween('start_at',[request('start_at'),request('end_at')])
                              ->orWhereBetween('end_at',[request('start_at'),request('end_at')])
                              ->orWhere(function($qq){
                                  $qq->where('start_at','<=',request('start_at'))
                                    ->where('end_at','>=',request('end_at'));
                              });
                        })->exists();
                    if ($overlap) {
                        $fail('Produk ini sudah terjadwal/berjalan pada rentang waktu tersebut.');
                    }
                }
            ],
            'start_price' => ['required','numeric','min:0'],
            'increment'   => ['required','numeric','min:0.01'],
            'start_at'    => ['required','date','after_or_equal:today'],
            'end_at'      => [
                'required','date','after:start_at',
                function ($attr,$val,$fail) {
                    if (now()->greaterThan($val)) {
                        $fail('Waktu selesai harus setelah waktu saat ini.');
                    }
                },
            ],
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
