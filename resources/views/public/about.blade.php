<x-guest-layout>
    <div class="max-w-screen-xl mx-auto px-4 space-y-10">

        {{-- HEADER / INTRO --}}
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.18em] text-slate-400 mb-1">
                    Tentang Tempus Auctions
                </p>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-900">
                    Tempus Auctions
                </h1>
                <p class="mt-2 text-base text-slate-600 max-w-2xl">
                    Platform lelang jam tangan yang mengutamakan kurasi, transparansi, dan pengalaman
                    pengguna yang tenang—baik untuk kolektor serius maupun pecinta jam yang baru mulai.
                </p>
            </div>

            <div class="flex items-center gap-2 text-sm text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-slate-900">Beranda</a>
                <span>/</span>
                <span class="font-medium text-slate-900">Tentang Kami</span>
            </div>
        </div>

        {{-- HIGHLIGHT CARDS --}}
        <section class="grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                <p class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                    ⌚ Fokus pada jam tangan
                </p>
                <p class="mt-1 text-sm text-slate-600">
                    Tempus Auctions lahir dari kecintaan pada dunia horologi—tiap lot dipilih dengan
                    mempertimbangkan desain, kondisi, dan karakter.
                </p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                <p class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                    🧩 Kurasi yang terarah
                </p>
                <p class="mt-1 text-sm text-slate-600">
                    Bukan sekadar banyak, tapi tepat sasaran. Kami berupaya menghadirkan kombinasi
                    antara daily wearer, koleksi unik, hingga piece yang lebih spesial.
                </p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                <p class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                    🔍 Transparansi informasi
                </p>
                <p class="mt-1 text-sm text-slate-600">
                    Detail produk, kondisi, dan periode lelang disajikan sejelas mungkin agar Anda
                    bisa mengambil keputusan dengan nyaman.
                </p>
            </div>
        </section>

        {{-- SIAPA KAMI --}}
        <section class="grid gap-6 lg:grid-cols-3 lg:items-start">
            <div class="lg:col-span-2 space-y-3">
                <h2 class="text-xl md:text-2xl font-semibold text-slate-900">
                    Siapa Kami
                </h2>
                <p class="text-sm md:text-base text-slate-600">
                    Tempus Auctions adalah platform lelang jam tangan yang dibangun oleh tim kecil
                    pecinta jam yang percaya bahwa setiap jam punya cerita—dan setiap kolektor layak
                    mendapatkan pengalaman lelang yang rapi, jujur, dan menyenangkan.
                </p>
                <p class="text-sm md:text-base text-slate-600">
                    Kami tidak hanya melihat jam sebagai komoditas, tapi sebagai penghubung momen,
                    perjalanan, dan preferensi personal. Karena itu, kami mengutamakan <span class="font-semibold">
                    kurasi, foto yang jelas, dan penjelasan yang informatif</span> di setiap lot.
                </p>
                <p class="text-sm md:text-base text-slate-600">
                    Platform ini dirancang agar mudah digunakan: baik oleh pengguna yang baru pertama
                    kali ikut lelang, maupun kolektor yang sudah terbiasa memantau banyak lot sekaligus.
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 space-y-3">
                <h3 class="text-base font-semibold text-slate-900">
                    Apa yang kami utamakan
                </h3>
                <ul class="text-sm text-slate-600 space-y-2 list-disc list-inside">
                    <li>Pengalaman pengguna yang jelas, tenang, dan tidak membingungkan.</li>
                    <li>Deskripsi lot yang informatif dan tidak berbelit-belit.</li>
                    <li>Proses lelang yang tertib, dengan aturan yang konsisten.</li>
                    <li>Komunikasi yang responsif dan melalui kanal resmi.</li>
                </ul>
            </div>
        </section>

        {{-- KENAPA TEMPUS AUCTIONS --}}
        <section class="space-y-4">
            <h2 class="text-xl md:text-2xl font-semibold text-slate-900">
                Kenapa Memilih Tempus Auctions?
            </h2>

            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-white p-4 space-y-2">
                    <p class="flex items-center gap-2 text-base font-semibold text-slate-900">
                        🧭 Kurasi yang terarah
                    </p>
                    <p class="text-sm text-slate-600">
                        Kami berupaya menghindari “noise” dengan menghadirkan pilihan yang terpilih,
                        sehingga Anda tidak perlu scroll ratusan item yang tidak relevan.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 space-y-2">
                    <p class="flex items-center gap-2 text-base font-semibold text-slate-900">
                        💬 Informasi yang mudah dicerna
                    </p>
                    <p class="text-sm text-slate-600">
                        Layout halaman lot didesain agar <span class="font-semibold">harga awal, bid terakhir, kelipatan bid,
                        dan countdown</span> langsung terbaca tanpa perlu menebak-nebak.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 space-y-2">
                    <p class="flex items-center gap-2 text-base font-semibold text-slate-900">
                        🕒 Fokus pada pengalaman jangka panjang
                    </p>
                    <p class="text-sm text-slate-600">
                        Kami ingin Anda merasa nyaman untuk kembali: baik untuk menjual, membeli, 
                        maupun sekadar memantau lelang dan belajar mengenali karakter jam.
                    </p>
                </div>
            </div>
        </section>

        {{-- BAGAIANA PLATFORM BEKERJA --}}
        <section class="space-y-4">
            <h2 class="text-xl md:text-2xl font-semibold text-slate-900">
                Bagaimana Platform Ini Bekerja
            </h2>

            <div class="grid gap-4 lg:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 space-y-2">
                    <p class="text-sm font-semibold text-slate-500 uppercase tracking-wide">
                        Langkah 1
                    </p>
                    <p class="text-base font-semibold text-slate-900">
                        Kurasi & Penjadwalan Lot
                    </p>
                    <p class="text-sm text-slate-600">
                        Tim kami memilih, memverifikasi informasi dasar, dan menyusun jadwal lelang.
                        Setiap lot memiliki periode mulai dan berakhir yang jelas.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 space-y-2">
                    <p class="text-sm font-semibold text-slate-500 uppercase tracking-wide">
                        Langkah 2
                    </p>
                    <p class="text-base font-semibold text-slate-900">
                        Lelang Berjalan Secara Online
                    </p>
                    <p class="text-sm text-slate-600">
                        Pengguna dapat membuat akun, login, dan melakukan bid saat status lelang
                        <span class="font-semibold text-emerald-700">Live</span>. Semua bid tercatat oleh sistem secara real time.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 space-y-2">
                    <p class="text-sm font-semibold text-slate-500 uppercase tracking-wide">
                        Langkah 3
                    </p>
                    <p class="text-base font-semibold text-slate-900">
                        Penentuan Pemenang & Penyelesaian
                    </p>
                    <p class="text-sm text-slate-600">
                        Setelah periode berakhir, sistem akan menentukan bid tertinggi yang sah.
                        Pemenang akan mendapatkan instruksi pembayaran dan proses serah terima akan dijadwalkan.
                    </p>
                </div>
            </div>
        </section>

        {{-- FILOSOFI KURASI --}}
        <section class="space-y-4">
            <h2 class="text-xl md:text-2xl font-semibold text-slate-900">
                Filosofi Kurasi Jam di Tempus
            </h2>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 md:p-5 space-y-3">
                <p class="text-sm md:text-base text-slate-600">
                    Kami percaya bahwa jam yang “tepat” untuk seseorang tidak selalu harus paling mahal,
                    paling langka, atau paling populer. Karena itu, kurasi kami mengutamakan:
                </p>
                <ul class="text-sm md:text-base text-slate-600 space-y-2 list-disc list-inside">
                    <li><span class="font-semibold">Kejelasan karakter</span> – desain, ukuran, dan kesan yang ingin dibangun.</li>
                    <li><span class="font-semibold">Kondisi yang dijelaskan dengan jujur</span> – termasuk jejak pemakaian jika ada.</li>
                    <li><span class="font-semibold">Konteks penggunaan</span> – apakah cocok sebagai daily wearer, dress watch, atau koleksi khusus.</li>
                </ul>
                <p class="text-sm md:text-base text-slate-600">
                    Di halaman detail lot, Anda akan menemukan informasi yang dirancang untuk membantu mengambil keputusan:
                    brand, model, tahun, kondisi, kategori, hingga riwayat bid yang transparan.
                </p>
            </div>
        </section>

        {{-- KEAMANAN & KEPERCAYAAN --}}
        <section class="space-y-4">
            <h2 class="text-xl md:text-2xl font-semibold text-slate-900">
                Keamanan, Aturan, & Kepercayaan
            </h2>

            <div class="grid gap-4 lg:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 space-y-2">
                    <h3 class="text-base font-semibold text-slate-900">
                        Aturan yang jelas untuk semua pihak
                    </h3>
                    <p class="text-sm text-slate-600">
                        Untuk menjaga pengalaman bersama, kami menerapkan aturan yang konsisten—baik untuk
                        peserta lelang maupun pengelola lot. Mulai dari tata cara bid, penentuan pemenang,
                        hingga kewajiban setelah menang.
                    </p>
                    <p class="text-sm text-slate-600">
                        Anda dapat membaca detail lengkapnya di halaman
                        <a href="{{ route('rules') }}" class="underline text-slate-900 font-medium">
                            Panduan & Aturan Lelang
                        </a>.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 space-y-2">
                    <h3 class="text-base font-semibold text-slate-900">
                        Komunikasi melalui kanal resmi
                    </h3>
                    <p class="text-sm text-slate-600">
                        Untuk mengurangi risiko miskomunikasi dan penipuan, kami menganjurkan semua
                        komunikasi penting terkait lelang, pembayaran, dan konfirmasi dilakukan melalui
                        kanal resmi yang tercantum di platform.
                    </p>
                    <p class="text-sm text-slate-600">
                        Jika Anda ragu terhadap suatu informasi, Anda selalu dapat kembali ke situs ini
                        atau menghubungi kami melalui halaman
                        <a href="{{ route('about') }}" class="underline text-slate-900 font-medium">
                            Tentang Kami
                        </a>
                        sebagai referensi utama.
                    </p>
                </div>
            </div>
        </section>

        {{-- SIAPA DI BALIK TEMPUS --}}
        <section class="space-y-4">
            <h2 class="text-xl md:text-2xl font-semibold text-slate-900">
                Siapa di Balik Tempus
            </h2>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 md:p-5 space-y-3">
                <p class="text-sm md:text-base text-slate-600">
                    Tempus Auctions dikelola oleh tim kecil yang memiliki latar belakang kombinasi:
                    teknologi, produk digital, dan minat kuat pada dunia jam tangan. 
                </p>
                <p class="text-sm md:text-base text-slate-600">
                    Misi kami sederhana: <span class="font-semibold">membuat pengalaman lelang jam terasa rapi, 
                    ramah, dan dapat dipercaya</span>. Mulai dari cara informasi disusun,
                    cara countdown ditampilkan, hingga cara bid divalidasi semuanya dirancang dengan
                    perspektif pengguna.
                </p>
                <p class="text-sm md:text-base text-slate-600">
                    Seiring waktu, kami akan terus menyempurnakan fitur—mulai dari histori aktivitas,
                    notifikasi yang lebih pintar, hingga cara baru untuk menjelajah koleksi jam.
                </p>
            </div>
        </section>

        {{-- CTA AKHIR --}}
        <section class="border-t border-slate-200 pt-6 pb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-lg md:text-xl font-semibold text-slate-900">
                        Siap menjelajahi lelang jam?
                    </h2>
                    <p class="mt-1 text-sm md:text-base text-slate-600">
                        Mulai lihat lot yang sedang Live, atau baca kembali panduan lelang sebelum ikut bid pertama Anda.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold
                              bg-slate-900 text-white hover:bg-slate-800 transition">
                        <span>Lihat Lelang yang Sedang Berjalan</span>
                    </a>
                    <a href="{{ route('rules') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold
                              bg-slate-50 text-slate-800 border border-slate-200 hover:bg-slate-100 transition">
                        <span>Baca Panduan & Aturan</span>
                    </a>
                </div>
            </div>
        </section>
    </div>
</x-guest-layout>
