{{-- resources/views/admin/products/_table.blade.php --}}

<div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
    <table class="min-w-full text-sm md:text-base">
        <thead class="bg-slate-50 text-xs uppercase tracking-[0.16em] text-slate-500">
            <tr>
                <th class="px-4 sm:px-6 py-3 w-10 text-left"></th>
                <th class="px-4 sm:px-6 py-3 w-16 text-center">ID</th>
                <th class="px-4 sm:px-6 py-3 text-left">Foto</th>
                <th class="px-4 sm:px-6 py-3 text-left">Brand &amp; Model</th>
                <th class="px-4 sm:px-6 py-3 text-center">Kategori</th>
                <th class="px-4 sm:px-6 py-3 text-center">Tahun</th>
                <th class="px-4 sm:px-6 py-3 text-center">Kondisi</th>
                <th class="px-4 sm:px-6 py-3 text-center">Dipakai di Lot</th>
                <th class="px-4 sm:px-6 py-3 text-center">Dibuat</th>
                <th class="px-4 sm:px-6 py-3 text-center">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
        @forelse($products as $p)
            @php
                $thumb = $p->images()->orderBy('sort_order')->first(); // thumbnail utama
                $imgs  = $p->images()->orderBy('sort_order')->take(10)->get(); // untuk accordion gambar
            @endphp

            {{-- ROW UTAMA --}}
            <tr class="bg-white align-top hover:bg-slate-50/70">
                {{-- tombol expand --}}
                <td class="px-4 sm:px-6 py-3 align-middle">
                    <button type="button"
                            class="grid place-items-center size-7 rounded hover:bg-slate-100"
                            @click="toggleRow({{ $p->id }})">
                        <svg class="size-4 transition-transform"
                             :class="open[{{ $p->id }}] ? 'rotate-180' : ''"
                             viewBox="0 0 20 20" fill="currentColor">
                            <path d="M5.23 7.21a.75.75 0 011.06.02L10 11.06l3.71-3.83a.75.75 0 111.08 1.04l-4.25 4.39a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z"/>
                        </svg>
                        <span class="sr-only">Toggle gambar</span>
                    </button>
                </td>

                {{-- ID --}}
                <td class="px-4 sm:px-6 py-3 text-center text-sm align-middle text-slate-900 whitespace-nowrap">
                    {{ $p->id }}
                </td>

                {{-- Foto --}}
                <td class="px-4 sm:px-6 py-3">
                    <div class="h-14 w-14 overflow-hidden rounded-md bg-slate-100 shadow-sm">
                        @if($thumb)
                            <img src="{{ asset('storage/products/'.$thumb->filename) }}"
                                 class="h-full w-full object-cover"
                                 alt="thumb">
                        @else
                            <div class="h-full w-full grid place-content-center text-slate-400">–</div>
                        @endif
                    </div>
                </td>

                {{-- Brand & Model (link ke SHOW) --}}
                <td class="px-4 sm:px-6 py-3 align-middle">
                    @php
                        // pastikan route show ada: Route::get('/admin/products/{product}', ...)->name('products.show');
                    @endphp
                    <a href="{{ route('products.show', $p) }}" class="block">
                        <div class="font-medium text-sm text-blue-600 hover:underline">
                            {{ $p->brand }}
                        </div>
                        <div class="text-slate-600 text-xs">
                            {{ $p->model }}
                        </div>
                    </a>
                </td>

                {{-- Kategori --}}
                <td class="px-4 sm:px-6 py-3 text-center text-xs align-middle">
                    @if($p->category)
                        @php $cats = explode(',', $p->category); @endphp
                        <span class="inline-flex flex-wrap gap-1 justify-center">
                            @foreach($cats as $cat)
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5">
                                    {{ trim($cat) }}
                                </span>
                            @endforeach
                        </span>
                    @else
                        —
                    @endif
                </td>

                {{-- Tahun --}}
                <td class="px-4 sm:px-6 py-3 text-center text-sm text-slate-700 align-middle whitespace-nowrap">
                    {{ $p->year ?? '—' }}
                </td>

                {{-- Kondisi --}}
                <td class="px-4 sm:px-6 py-3 text-center align-middle whitespace-nowrap">
                    @if($p->condition === 'NEW')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                            NEW
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                            USED
                        </span>
                    @endif
                </td>

                {{-- Dipakai di Lot --}}
                <td class="px-4 sm:px-6 py-3 text-center text-sm text-slate-700 align-middle whitespace-nowrap">
                    {{ $p->auction_lots_count ?? 0 }}
                </td>

                {{-- Dibuat --}}
                <td class="px-4 sm:px-6 py-3 text-center text-sm text-slate-500 align-middle whitespace-nowrap">
                    {{ $p->created_at->format('d M Y, H:i') }}
                </td>

                {{-- Aksi --}}
                <td class="px-4 sm:px-6 py-3 text-center align-middle">
                    <div class="inline-flex items-center justify-end gap-1">
                        {{-- Edit --}}
                        <button type="button"
                                class="inline-flex items-center justify-center h-9 w-9 rounded-full border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 hover:border-slate-400 shadow-sm"
                                title="Edit"
                                @click="openEdit({
                                    id: {{ $p->id }},
                                    brand: @js($p->brand),
                                    model: @js($p->model),
                                    category: @js($p->category ? explode(',', $p->category) : []),
                                    year: @js($p->year),
                                    condition: @js($p->condition),
                                    description: @js($p->description),
                                    weight_grams: @js($p->weight_grams),
                                })">
                            <svg viewBox="0 0 20 20" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M4 13.5V16h2.5L14 8.5l-2.5-2.5L4 13.5z"/>
                                <path d="M11.5 6l2-2 2 2-2 2z"/>
                            </svg>
                        </button>

                        {{-- Hapus --}}
                        <form method="POST" action="{{ route('products.destroy', $p->id) }}"
                              @submit.prevent="
                                    (async () => {
                                        const ok = await $store.dialog.confirm({
                                            title: 'Hapus Produk',
                                            message: 'Produk ini akan dihapus. Jika sedang/pernah dipakai pada lot aktif, penghapusan akan diblokir.\nLanjutkan?',
                                            confirmText: 'Hapus'
                                        });
                                        if (ok) $el.submit();
                                    })()
                              ">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center justify-center h-9 w-9 rounded-full border border-red-200 bg-white text-red-600 hover:bg-red-50 hover:border-red-300 shadow-sm"
                                    title="Hapus">
                                <svg viewBox="0 0 20 20" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M6 7h8l-.5 9h-7z"/>
                                    <path d="M8 7V5h4v2M5 5h10M9 9v5M11 9v5"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>

            {{-- ROW DETAIL: GRID GAMBAR (ACCORDION) --}}
            <tr x-show="open[{{ $p->id }}]" x-cloak>
                <td colspan="10" class="px-4 sm:px-6 py-4 bg-slate-50">
                    <div class="rounded-lg border border-slate-300 bg-white p-4"
                         x-data="imageManager({{ $p->id }}, @js($imgs))"
                         x-init="initSortable()">

                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-slate-700">Gambar Produk</h3>

                            <template x-if="!edit">
                                <button class="rounded border px-3 py-1 text-sm" @click="edit=true">
                                    Kelola gambar
                                </button>
                            </template>

                            <template x-if="edit">
                                <div class="flex gap-2">
                                    <button type="button"
                                            class="rounded bg-slate-100 px-3 py-1 text-sm"
                                            @click="cancel()">
                                        Batal
                                    </button>

                                    <form method="POST"
                                          :action="syncUrl"
                                          enctype="multipart/form-data"
                                          @submit.prevent="submit($event)">
                                        @csrf
                                        <input type="hidden" name="order"   :value="JSON.stringify(order)">
                                        <input type="hidden" name="removed" :value="JSON.stringify(removed)">
                                        <input type="hidden" name="primary" :value="primary">
                                        <input type="file" name="uploads[]" multiple class="hidden" x-ref="uploadSink">
                                        <button type="submit"
                                                :disabled="submitting"
                                                class="rounded bg-yellow-500 px-3 py-1 text-sm font-semibold text-slate-900 disabled:opacity-60">
                                            <span x-show="!submitting">Simpan</span>
                                            <span x-show="submitting">Menyimpan…</span>
                                        </button>
                                    </form>
                                </div>
                            </template>
                        </div>

                        {{-- Grid gambar --}}
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3" x-ref="grid">
                            <template x-if="images.length===0 && !edit">
                                <div class="col-span-full">
                                    <div class="rounded-lg border border-dashed py-6 grid place-content-center text-slate-500">
                                        Tidak ada gambar untuk produk ini.
                                    </div>
                                </div>
                            </template>

                            <template x-for="(img,i) in images" :key="img.key">
                                <div data-img :data-id="img.id"
                                     class="relative aspect-square overflow-hidden rounded-lg border bg-white">
                                    <img :src="img.preview" class="h-full w-full object-cover">
                                    <span class="absolute left-1 top-1 bg-white/90 text-[10px] px-1.5 py-0.5 rounded">
                                        #<span x-text="i+1"></span>
                                    </span>
                                    <span x-show="i===0"
                                          class="absolute left-1 top-6 bg-yellow-400/90 px-1.5 py-0.5 text-[10px] font-semibold rounded">
                                        UTAMA
                                    </span>

                                    <template x-if="edit">
                                        <div class="absolute inset-x-1 bottom-1 flex justify-between">
                                            <button type="button"
                                                    class="bg-white/90 px-2 py-1 text-xs rounded shadow"
                                                    @click="moveToFirst(i)">
                                                Jadikan utama
                                            </button>
                                            <button type="button"
                                                    class="bg-red-50 text-red-600 px-2 py-1 text-xs rounded shadow"
                                                    @click="remove(i)">
                                                Hapus
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            {{-- Slot tambah --}}
                            <template x-if="edit && images.length < 10">
                                <div class="relative aspect-square rounded-lg border-2 border-dashed grid place-content-center text-slate-400"
                                     @dragover.prevent
                                     @drop.prevent="handleDrop($event)">
                                    <input type="file" multiple accept="image/*" class="hidden" x-ref="fileInput" @change="handleChoose($event)">
                                    <button type="button" class="text-xs" @click="$refs.fileInput.click()">
                                        Seret & lepas gambar ke sini<br>atau klik untuk tambah
                                    </button>
                                </div>
                            </template>
                        </div>

                        <p class="mt-3 text-xs text-slate-500">
                            Format: JPG, JPEG, PNG, WEBP • Maks 1 MB per file • Maks 10 gambar •
                            Urutan pertama = <b>Utama / Thumbnail / Sampul Lot</b>.
                        </p>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="px-6 py-6 text-center text-sm text-slate-500">
                    Belum ada produk.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

