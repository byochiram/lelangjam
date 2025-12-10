{{-- admin/products/index.blade.php --}}
<x-app-layout title="Produk">
    <div x-data="productModals('{{ url('/admin/products') }}')" class="py-6">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Produk Lelang') }}
            </h2>
        </x-slot>

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    {{-- Header Katalog --}}
                    <div class="px-6 pt-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h1 class="text-lg font-semibold text-slate-900">Daftar Produk</h1>
                            <p class="text-sm text-slate-500">Kelola katalog jam yang akan dilelang.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                x-data
                                @click="$dispatch('open-create-product')"
                                type="button"
                                class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-sm text-white hover:bg-blue-500">
                                + Tambah Produk
                            </button>
                        </div>
                    </div>

                    {{-- STAT CARDS --}}
                    <div class="px-6 pt-4 pb-1 grid grid-cols-1 sm:grid-cols-5 gap-4 text-sm">
                        <div class="rounded-xl border border-slate-100 bg-slate-50 px-3 py-3">
                            <div class="text-xs text-slate-500">Total Produk</div>
                            <div class="mt-1 text-xl font-semibold text-slate-900">
                                {{ number_format($totalProducts) }}
                            </div>
                        </div>
                    </div>

                    {{-- WRAPPER: FILTER + TABLE + PAGER --}}
                    <div x-data="productFilters()" x-init="init()" class="mb-4">
                        
                        {{-- Filter Bar --}}
                        <div class="px-6 pt-4 pb-3">
                            <form x-ref="filterForm"
                                class="grid gap-3 grid-cols-2 lg:grid-cols-9 text-[15px] items-center"
                                @submit.prevent>

                                <div class="lg:col-span-2">
                                    <input type="text"
                                        name="search"
                                        value="{{ request('search') }}"
                                        placeholder="Cari brand / model…"
                                        class="w-full rounded-lg border-slate-300 text-[15px]"
                                        @input.debounce.500ms="apply()" />
                                </div>

                                <select name="brand" class="w-full rounded-lg border-slate-300 text-[15px]" @change="apply()">
                                    <option value="">Brand</option>
                                    @foreach($brands as $b)
                                        <option value="{{ $b }}" @selected(request('brand')===$b)>{{ $b }}</option>
                                    @endforeach
                                </select>

                                <select name="category" class="w-full rounded-lg border-slate-300 text-[15px]" @change="apply()">
                                    <option value="">Kategori</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
                                    @endforeach
                                </select>

                                <select name="condition" class="w-full rounded-lg border-slate-300 text-[15px]" @change="apply()">
                                    <option value="">Kondisi</option>
                                    <option value="NEW"  @selected(request('condition')==='NEW')>Baru</option>
                                    <option value="USED" @selected(request('condition')==='USED')>Bekas</option>
                                </select>

                                <input type="number" name="year_min" value="{{ request('year_min') }}"
                                    placeholder="Tahun ≥"
                                    class="w-full rounded-lg border-slate-300 text-[15px]"
                                    @input.debounce.500ms="apply()" />

                                <input type="number" name="year_max" value="{{ request('year_max') }}"
                                    placeholder="Tahun ≤"
                                    class="w-full rounded-lg border-slate-300 text-[15px]"
                                    @input.debounce.500ms="apply()" />

                                <select name="sort" class="w-full rounded-lg border-slate-300 text-[15px]" @change="apply()">
                                    <option value="newest"    @selected($sort==='newest')>Terbaru</option>
                                    <option value="lots_desc" @selected($sort==='lots_desc')>Terbanyak dipakai lot</option>
                                    <option value="brand_asc" @selected($sort==='brand_asc')>Brand A–Z</option>
                                    <option value="brand_desc"@selected($sort==='brand_desc')>Brand Z–A</option>
                                </select>

                                <div class="flex gap-2">
                                    <select name="per" class="w-full rounded-lg border-slate-300 text-[15px]" @change="apply()">
                                        @foreach([10,25,50] as $n)
                                            <option value="{{ $n }}" @selected((int)request('per', $perPage) === $n)>{{ $n }}/Hal</option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>

                        {{-- TABLE --}}
                        <div class="px-6 pb-4">
                            <div class="overflow-x-auto" x-ref="tableWrap">
                                @include('admin.products._table')
                            </div>
                        </div>

                        {{-- PAGINATION: sama seperti transaksi & pengguna --}}
                        <div class="px-6 py-3 border-t border-slate-100" x-ref="pager">
                            {{ $products->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        

        {{-- =================== MODAL: CREATE PRODUK =================== --}}
        <div
            x-data="{ open: {{ ($errors->any() && old('_from')==='create') ? 'true' : 'false' }} }"
            x-on:open-create-product.window="open = true"
            x-show="open"
            x-cloak
            class="fixed inset-0 z-[100] flex items-center justify-center"
            aria-modal="true" role="dialog">

            {{-- backdrop --}}
            <div class="absolute inset-0 bg-black/50" @click="open=false"></div>

            {{-- panel --}}
            <div class="relative mx-4 w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b px-5 py-4">
                    <h3 class="text-lg font-semibold">Tambah Produk Baru</h3>
                    <button class="text-slate-500 hover:text-slate-800" @click="open=false" aria-label="Close">✕</button>
                </div>

                <form
                    method="POST"
                    action="{{ route('products.store') }}"
                    enctype="multipart/form-data"
                    class="space-y-5 px-5 pb-5"
                    x-data="imagePicker(10)"
                    @submit.prevent="attachAndSubmit($event)"
                    >
                    @csrf
                    <input type="hidden" name="_from" value="create">
                    {{-- Brand --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Brand <span class="text-red-500">*</span></label>
                        <input type="text" name="brand" value="{{ old('brand') }}" required
                            class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                        @error('brand') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Model --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Model <span class="text-red-500">*</span></label>
                        <input type="text" name="model" value="{{ old('model') }}" required
                            class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                        @error('model') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        {{-- Berat (gram) --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">
                                Berat (gram) <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                name="weight_grams"
                                value="{{ old('weight_grams') }}"
                                min="0"
                                step="1"
                                required
                                class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                            <p class="mt-1 text-xs text-slate-500">
                                Berat total jam + packing, dipakai untuk perhitungan ongkos kirim.
                            </p>
                            @error('weight_grams')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>    
                        {{-- Kondisi --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Kondisi <span class="text-red-500">*</span></label>
                            <select name="condition" required
                                    class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                                <option value="">-- Pilih Kondisi --</option>
                                <option value="NEW"  @selected(old('condition')==='NEW')>Baru</option>
                                <option value="USED" @selected(old('condition')==='USED')>Bekas</option>
                            </select>
                            @error('condition') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Tahun --}}
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Tahun</label>
                            <input type="number" name="year" value="{{ old('year') }}"
                                    class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                            @error('year') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Kategori
                        </label>

                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                            @foreach($categories as $cat)
                                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                    <input type="checkbox"
                                        name="category[]"
                                        value="{{ $cat }}"
                                        class="rounded text-sm border-slate-300 text-yellow-600 focus:ring-yellow-500"
                                        @checked(collect(old('category'))->contains($cat))>
                                    <span>{{ $cat }}</span>
                                </label>
                            @endforeach
                        </div>

                        <p class="mt-1 text-xs text-slate-500">
                            Bisa pilih lebih dari satu kategori.
                        </p>

                        @error('category') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        @error('category.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Deskripsi</label>
                        <textarea name="description" rows="6"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Gambar Produk --}}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-medium text-slate-700">Gambar Produk</label>
                            <span class="text-xs text-slate-500" x-text="`${files.length}/10`"></span>
                        </div>

                        {{-- dropzone style --}}
                        <div
                            class="rounded-lg border border-dashed border-slate-300 p-4 text-center"
                            @dragover.prevent
                            @drop.prevent="handleDrop($event)">
                            <p class="text-sm text-slate-600">Seret & lepas gambar ke sini atau</p>
                            <div class="mt-2">
                            <input type="file" multiple accept="image/*" class="hidden" x-ref="fileInput" @change="handleChoose($event)">
                            <button type="button"
                                    class="rounded-md text-sm bg-slate-800 px-3 py-1.5 text-white hover:bg-slate-700"
                                    @click="$refs.fileInput.click()">
                                Pilih Gambar
                            </button>
                            </div>
                            <p class="mt-2 text-xs text-slate-500">Format: JPG, JPEG, PNG, WEBP • Maks 1 MB per file • Maks 10 gambar • <br> Gambar pertama akan menjadi <b>utama</b>.</p>
                            @error('images') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            @error('images.*') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- preview grid --}}
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <template x-for="(file, i) in files" :key="i">
                                <div class="relative rounded-lg border p-2">
                                    <img :src="file.preview" class="h-28 w-full object-cover rounded" />
                                    <div class="mt-2 flex items-center justify-between text-xs">
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100" x-show="i===0">UTAMA</span>
                                    <button type="button" class="text-red-600 hover:underline" @click="remove(i)">Hapus</button>
                                    </div>
                                    {{-- input nyata yang akan ikut tersubmit --}}
                                    <input type="file" name="images[]" class="hidden" :files="file.blob">
                                </div>
                            </template>
                        </div>
                        <input type="file" name="images[]" multiple class="hidden" x-ref="filesSink">
                    </div>

                    <div class="flex justify-end gap-3 border-t pt-4">
                        <button type="button" @click="open=false"
                                class="rounded-md bg-slate-100 px-4 py-2 text-slate-700 hover:bg-slate-200">Batal
                        </button>
                        <button type="submit"
                                class="rounded-md bg-yellow-500 px-4 py-2 font-semibold text-slate-900 hover:bg-yellow-400 focus:ring-4 focus:ring-yellow-300">Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        {{-- =================== /MODAL =================== --}}

        {{-- =================== MODAL: EDIT PRODUK =================== --}}
        <div
            x-show="editOpen"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center"
            aria-modal="true" role="dialog"
        >
            {{-- backdrop --}}
            <div class="absolute inset-0 bg-black/50" @click="closeEdit()"></div>

            {{-- panel --}}
            <div
                class="relative mx-4 w-full max-w-2xl max-h-[90vh]
                    overflow-y-auto rounded-xl bg-white shadow-xl"
            >
                {{-- header --}}
                <div class="flex items-center justify-between border-b px-5 py-4">
                    <h3 class="text-lg font-semibold">Edit Produk</h3>
                    <button class="text-slate-500 hover:text-slate-800" @click="closeEdit()">✕</button>
                </div>

                {{-- body form --}}
                <form method="POST"
                    :action="updateAction"
                    class="space-y-5 px-5 pb-5"
                    @submit.prevent="
                        (async () => {
                            const ok = await $store.dialog.confirm({
                                title: 'Konfirmasi Update',
                                message: 'Simpan perubahan pada produk ini?',
                                confirmText: 'Simpan'
                            });
                            if (ok) $el.submit();
                        })()
                    ">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Brand <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="brand" x-model="form.brand" required
                                class="w-full text-sm border-gray-300 rounded-md focus:ring-yellow-500 focus:border-yellow-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Model <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="model" x-model="form.model" required
                                class="w-full text-sm border-gray-300 rounded-md focus:ring-yellow-500 focus:border-yellow-500">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            {{-- Berat (gram) --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">
                                    Berat (gram) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    name="weight_grams"
                                    x-model="form.weight_grams"
                                    min="0"
                                    step="1"
                                    required
                                    class="w-full text-sm border-gray-300 rounded-md focus:ring-yellow-500 focus:border-yellow-500">
                                <p class="mt-1 text-xs text-slate-500">
                                    Berat total jam + packing, dipakai untuk perhitungan ongkos kirim.
                                </p>
                            </div>
                            {{-- Kondisi --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">
                                    Kondisi <span class="text-red-500">*</span>
                                </label>
                                <select name="condition" x-model="form.condition" required
                                        class="w-full text-sm border-gray-300 rounded-md focus:ring-yellow-500 focus:border-yellow-500">
                                    <option value="NEW">Baru</option>
                                    <option value="USED">Bekas</option>
                                </select>
                            </div>

                            {{-- Tahun --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Tahun</label>
                                <input type="number"
                                    name="year"
                                    x-model="form.year"
                                    min="1900"
                                    max="{{ now()->year }}"
                                    step="1"
                                    class="w-full text-sm border-gray-300 rounded-md focus:ring-yellow-500 focus:border-yellow-500">
                            </div>
                        </div>

                        {{-- Kategori --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                                @foreach($categories as $cat)
                                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                        <input type="checkbox"
                                            name="category[]"
                                            value="{{ $cat }}"
                                            class="rounded text-sm border-slate-300 text-yellow-600 focus:ring-yellow-500"
                                            :checked="form.category.includes('{{ $cat }}')"
                                            @change="toggleCategory('{{ $cat }}', $event)">
                                        <span>{{ $cat }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="mt-1 text-xs text-slate-500">
                                Bisa pilih lebih dari satu kategori.
                            </p>
                        </div>

                        {{-- Deskripsi --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                            <textarea name="description" rows="6" x-model="form.description"
                                    class="w-full text-sm border-gray-300 rounded-md focus:ring-yellow-500 focus:border-yellow-500"></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3 border-t pt-4">
                        <button type="button"
                                @click="closeEdit()"
                                class="px-4 py-2 bg-slate-100 text-slate-700 rounded-md hover:bg-slate-200">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-yellow-500 text-slate-900 font-semibold rounded-md hover:bg-yellow-400 focus:ring-4 focus:ring-yellow-300">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        {{-- =================== /MODAL =================== --}}
    </div>

    <script>
        function productModals(baseUpdateUrl) {
            return {
                // === Row expand/collapse (accordion gambar) ===
                open: {}, // { [productId]: true|false }
                toggleRow(id) {
                    this.open[id] = !this.open[id];
                },

                // === STATE EDIT FORM PRODUK ===
                editOpen: false,
                updateAction: '#',
                form: {
                    id: null,
                    brand: '',
                    model: '',
                    category: [],
                    year: '',
                    condition: 'USED',
                    description: '',
                    weight_grams: ''
                },

                openEdit(p) {
                    this.form = { ...this.form, ...p };
                    this.updateAction = `${baseUpdateUrl}/${p.id}`;
                    this.editOpen = true;
                },

                closeEdit() {
                    this.editOpen = false;
                },

                toggleCategory(cat, ev) {
                    if (ev.target.checked) {
                        if (!this.form.category.includes(cat)) {
                            this.form.category.push(cat);
                        }
                    } else {
                        this.form.category = this.form.category.filter(c => c !== cat);
                    }
                },
            }
        }
    </script>

    <script>
        function productFilters() {
            return {
                baseUrl: "{{ route('products.index') }}",

                init() {
                    // supaya pagination klik pertama juga via AJAX
                    this._wirePagination();
                },

                async apply() {
                    const form = this.$refs.filterForm;
                    const params = new URLSearchParams(new FormData(form));
                    await this._swap(`${this.baseUrl}?${params.toString()}`);
                },

                async resetFilters() {
                    const form = this.$refs.filterForm;
                    form.querySelectorAll('input').forEach(i => i.value = '');
                    form.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
                    await this._swap(this.baseUrl);
                },

                async _swap(url) {
                    const res  = await fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const html = await res.text();
                    const tmp  = document.createElement('div');
                    tmp.innerHTML = html;

                    const newTable = tmp.querySelector('[x-ref="tableWrap"]');
                    const newPager = tmp.querySelector('[x-ref="pager"]');

                    if (newTable && this.$refs.tableWrap) {
                        this.$refs.tableWrap.innerHTML = newTable.innerHTML;
                    }
                    if (newPager && this.$refs.pager) {
                        this.$refs.pager.innerHTML = newPager.innerHTML;
                    }

                    // pasang ulang handler pagination di HTML baru
                    this._wirePagination();

                    // update URL (biar kelihatan ?search=casio dll)
                    history.replaceState({}, '', url);
                },

                _wirePagination() {
                    ['tableWrap','pager'].forEach(refName => {
                        const el = this.$refs[refName];
                        if (!el) return;

                        el.querySelectorAll('a[href*="page="]').forEach(a => {
                            a.addEventListener('click', (e) => {
                                e.preventDefault();
                                this._swap(a.href);
                            }, { once: true });
                        });
                    });
                },
            }
        }
    </script>

    <script>
        function imagePicker(max = 10) {
            return {
                files: [], // [{ file, preview }]
                ALLOWED: ['image/jpeg','image/jpg','image/png','image/webp'],
                MAX: 1024 * 1024, // 1MB
                pushFile(file) {
                const errs = [];
                if (!file) return;
                if (!this.ALLOWED.includes(file.type)) errs.push(`• ${file.name}: format tidak didukung.`);
                if (file.size > this.MAX) errs.push(`• ${file.name}: ukuran > 1 MB.`);
                if (this.files.length >= max) errs.push(`• Batas ${max} gambar tercapai.`);

                if (errs.length) {
                    Alpine.store('dialog').alert({
                    title: 'Gambar ditolak',
                    message: errs.join('\n'),
                    confirmText: 'Mengerti'
                    });
                    return;
                }
                const preview = URL.createObjectURL(file);
                this.files.push({ file, preview });
                },
                handleChoose(e){ for (const f of e.target.files) this.pushFile(f); e.target.value=''; },
                handleDrop(e){ const ds=(e.dataTransfer?.files)||[]; for (const f of ds) this.pushFile(f); },
                remove(i){ URL.revokeObjectURL(this.files[i].preview); this.files.splice(i,1); },

                async attachAndSubmit(evt) {
                    const form = evt.target;

                    // === 1) Jalankan HTML5 form validation dulu ===
                    if (!form.checkValidity()) {
                        // ini yang akan memunculkan bubble seperti di CRUD lelang (gambar pertama)
                        form.reportValidity();
                        return; // stop, jangan lanjut ke dialog konfirmasi
                    }

                    // === 2) Kalau lolos validasi baru masuk logika dialog ===
                    if (this.files.length === 0) {
                        const ok = await Alpine.store('dialog').confirm({
                            title: 'Simpan tanpa gambar?',
                            message: 'Anda belum menambahkan gambar. Lanjutkan menyimpan produk?',
                            confirmText: 'Ya, simpan'
                        });
                        if (!ok) return;
                    } else {
                        const ok = await Alpine.store('dialog').confirm({
                            title: 'Konfirmasi Simpan',
                            message: `Simpan produk dengan ${this.files.length} gambar?`,
                            confirmText: 'Simpan'
                        });
                        if (!ok) return;
                    }

                    // === 3) Packing file & submit ===
                    const sink = this.$refs.filesSink;
                    const dt = new DataTransfer();
                    this.files.forEach(({ file }) => dt.items.add(file));
                    sink.files = dt.files;

                    form.submit();
                }
            };
        }
    </script>

    <script>
        function imageManager(productId, existing) {
            return {
                edit:false,
                submitting: false,
                syncUrl:`/admin/products/${productId}/images/sync`,
                images:[],
                order:[],
                removed:[],
                primary:null,
                sortable:null,
                seenKeys: new Set(),
                ALLOWED: ['image/jpeg','image/jpg','image/png','image/webp'],
                MAX: 1024*1024,

                // === INIT ===
                initSortable(){
                    this.images = existing.map(x => ({
                        id: String(x.id),
                        key: 'old-'+x.id,
                        // build URL publik dari filename:
                        preview: '/storage/products/' + x.filename,
                        isNew: false
                    }));
                    this.refreshOrder();

                    this.$watch('edit', (val) => {
                        if (val) this.mountSortable();
                        else this.destroySortable();
                    });
                },

                // === Sortable helpers ===
                mountSortable(){
                    if (!window.Sortable || this.sortable) return;
                    this.sortable = new Sortable(this.$refs.grid, {
                        animation: 150,
                        draggable: '[data-img]',
                        handle: '[data-img]',
                        filter: 'button, a, [data-no-drag]',
                        ghostClass: 'opacity-60',
                        onEnd: () => {
                            const ids = Array.from(this.$refs.grid.querySelectorAll('[data-img]')).map(el => el.dataset.id);
                            this.images.sort((a,b) => ids.indexOf(a.id) - ids.indexOf(b.id));
                            this.refreshOrder();
                        }
                    });
                },

                destroySortable(){
                    if (this.sortable){ this.sortable.destroy(); this.sortable = null; }
                },

                // === State ops ===
                refreshOrder(){
                    this.order = this.images.map(i => i.id);
                    this.primary = this.order[0] || null; // urutan pertama = PRIMARY
                },

                moveToFirst(i){
                    const img = this.images.splice(i,1)[0];
                    this.images.unshift(img);
                    this.refreshOrder();
                },

                remove(i){
                    const img = this.images.splice(i,1)[0];
                    if (!img.isNew) this.removed.push(img.id);
                    this.refreshOrder();
                },

                cancel(){
                    this.edit = false;
                    this.removed = [];
                    this.seenKeys.clear();
                    this.images = existing.map(x => ({
                        id: String(x.id), key:'old-'+x.id,
                        preview: '/storage/products/' + x.filename, // ← was x.url
                        isNew:false
                    }));
                    this.refreshOrder();
                },

                // === File intake ===
                handleChoose(e){
                    for (const f of e.target.files) this._pushFile(f);
                    e.target.value = '';
                },

                handleDrop(e){
                    const files = e.dataTransfer?.files || [];
                    for (const f of files) this._pushFile(f);
                },

                _fingerprint(file) {
                    return `${file.name}|${file.size}|${file.lastModified}`;

                },
                _pushFile(file){
                    if (!file) return;
                    const errs = [];
                    if (!file.type.startsWith('image/')) errs.push(`• ${file.name}: bukan file gambar.`);
                    if (!this.ALLOWED.includes(file.type)) errs.push(`• ${file.name}: format tidak didukung.`);
                    if (file.size > this.MAX) errs.push(`• ${file.name}: ukuran > 1 MB.`);
                    if (this.images.length >= 10) errs.push('• Batas 10 gambar tercapai.');
                    if (errs.length) {
                        Alpine.store('dialog').alert({ title:'Gambar ditolak', message:errs.join('\n') });
                        return;
                    }
                    const url = URL.createObjectURL(file);
                    const key = 'new-'+Math.random().toString(36).slice(2,8);
                    this.images.push({ id:key, key, file, preview:url, isNew:true });
                    this.refreshOrder();
                },

                // === Submit ===
                async submit(evt){
                    if (this.submitting) return;
                    const addCount = this.images.filter(i=>i.isNew).length;
                    const delCount = this.removed.length;
                    const msg = `Simpan perubahan gambar?\n` +
                                `• Tambah: ${addCount}\n` +
                                `• Hapus: ${delCount}\n` +
                                `• Urutan ulang & UTAMA ikut disimpan.`;
                    const ok = await Alpine.store('dialog').confirm({ title:'Konfirmasi', message: msg, confirmText:'Simpan' });
                    if (!ok) return;

                    this.submitting = true;
                    const sink = this.$refs.uploadSink;
                    const dt = new DataTransfer();
                    this.images.filter(i=>i.isNew).forEach(i => dt.items.add(i.file));
                    sink.files = dt.files;
                    evt.target.submit();
                },
            }
        }
    </script>
</x-app-layout>
