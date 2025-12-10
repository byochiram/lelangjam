{{-- resources/views/terms.blade.php --}}
<x-auth-layout>
    <div class="w-full max-w-3xl mx-auto bg-white border border-slate-200 rounded-2xl shadow-sm p-8 md:p-10">
        {{-- HEADER --}}
        <div class="flex items-center gap-3 mb-6">
            <x-authentication-card-logo />
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-slate-900">
                    Syarat &amp; Ketentuan Tempus Auctions
                </h1>
                <p class="text-sm text-slate-500">
                    Berlaku untuk seluruh pengguna platform lelang jam tangan Tempus Auctions.
                </p>
            </div>
        </div>

        {{-- CONTENT --}}
        <div class="prose prose-sm md:prose-base max-w-none text-slate-800">
            <h2>1. Pendahuluan</h2>
            <p>
                Tempus Auctions adalah platform lelang jam tangan yang memungkinkan pengguna untuk menelusuri,
                menawar, dan membeli jam tangan melalui mekanisme lelang secara online. Dengan membuat akun,
                mengakses, atau menggunakan layanan kami, Anda menyatakan telah membaca, memahami, dan menyetujui
                Syarat &amp; Ketentuan ini.
            </p>

            <h2>2. Definisi</h2>
            <ul>
                <li><strong>Platform</strong>: Situs web Tempus Auctions yang digunakan untuk lelang jam tangan.</li>
                <li><strong>Pengguna</strong>: Setiap orang yang mengakses platform (Guest, Bidder, atau Admin).</li>
                <li><strong>Bidder</strong>: Pengguna terdaftar yang telah masuk (login) dan melakukan penawaran (bid).</li>
                <li><strong>Admin</strong>: Pihak pengelola Tempus Auctions yang mengatur produk, lot lelang, dan sistem.</li>
                <li><strong>Lot</strong>: Satu unit atau paket jam tangan yang dilelang dalam periode waktu tertentu.</li>
                <li><strong>Bid</strong>: Penawaran harga yang diajukan Bidder pada suatu Lot.</li>
                <li><strong>Pemenang Lelang</strong>: Bidder dengan penawaran tertinggi yang sah pada saat lelang ditutup.</li>
            </ul>

            <h2>3. Pendaftaran Akun &amp; Verifikasi Email (UC-01)</h2>
            <ol>
                <li>Untuk mengikuti lelang, Pengguna wajib membuat akun dengan mengisi nama, username, email, dan password yang valid.</li>
                <li>Pengguna wajib menjaga kerahasiaan password dan bertanggung jawab atas seluruh aktivitas yang terjadi pada akunnya.</li>
                <li>Setelah registrasi, sistem akan mengirimkan tautan verifikasi email. Akun baru hanya dianggap aktif setelah email diverifikasi.</li>
                <li>Admin berhak menolak, menonaktifkan, atau menghapus akun yang melanggar ketentuan, memberikan data palsu, atau disalahgunakan.</li>
            </ol>

            <h2>4. Keamanan Akun &amp; Sesi (Sessions Table)</h2>
            <ul>
                <li>Platform dapat menyimpan informasi sesi seperti alamat IP dan user agent untuk keperluan keamanan dan audit.</li>
                <li>Pengguna diimbau melakukan <em>logout</em> setelah selesai menggunakan platform, khususnya pada perangkat umum.</li>
                <li>Penyalahgunaan akun (misalnya digunakan pihak lain tanpa izin) menjadi tanggung jawab pemilik akun sehingga password harus dijaga dengan baik.</li>
            </ul>

            <h2>5. Verifikasi Identitas (KYC – UC-09)</h2>
            <ol>
                <li>Untuk mengikuti lelang tertentu atau melakukan pembayaran di atas batas nilai tertentu, Bidder dapat diwajibkan melakukan verifikasi identitas (KYC).</li>
                <li>Data KYC dapat meliputi NIK (yang disimpan dalam bentuk terproteksi), foto KTP, foto selfie, alamat lengkap, dan informasi lain yang relevan.</li>
                <li>Data KYC digunakan untuk mencegah penipuan, pencucian uang, serta memastikan kepatuhan terhadap regulasi yang berlaku.</li>
                <li>Admin berhak menyetujui, menunda, atau menolak pengajuan KYC berdasarkan hasil verifikasi.</li>
            </ol>

            <h2>6. Informasi Lelang &amp; Produk (UC-02 &amp; UC-03)</h2>
            <ul>
                <li>Guest dan Bidder dapat menelusuri daftar lelang dan melihat detail Lot, termasuk foto, spesifikasi, kondisi, harga awal, dan waktu berakhir.</li>
                <li>Deskripsi produk dibuat seakurat mungkin, namun variasi warna/tampilan dapat terjadi akibat perbedaan layar atau perangkat.</li>
                <li>Pengguna diharapkan membaca deskripsi dengan teliti sebelum melakukan penawaran.</li>
            </ul>

            <h2>7. Aturan Penawaran (Place Bid – UC-10)</h2>
            <ol>
                <li>Hanya Bidder yang telah login dan memenuhi syarat (misalnya KYC dan status akun) yang dapat menempatkan bid.</li>
                <li>Setiap bid yang berhasil disimpan dalam sistem bersifat mengikat dan tidak dapat dibatalkan sepihak oleh Bidder.</li>
                <li>Bidder wajib memastikan nominal bid sudah sesuai sebelum menekan tombol konfirmasi.</li>
                <li>Sistem akan menentukan penawaran tertinggi sebagai bid yang <em>WINNING</em> sampai ada bid yang lebih tinggi.</li>
                <li>Upaya manipulasi lelang (misalnya bid palsu, multi-akun untuk mengerek harga) dilarang dan dapat berakibat pemblokiran akun.</li>
            </ol>

            <h2>8. Penetapan Pemenang &amp; Pembayaran (UC-11)</h2>
            <ol>
                <li>Setelah periode lelang berakhir, sistem akan menetapkan pemenang berdasarkan bid tertinggi yang sah.</li>
                <li>Pemenang akan menerima instruksi pembayaran melalui sistem (tabel <code>payments</code>), termasuk batas waktu pembayaran.</li>
                <li>
                    Jika pembayaran tidak dilakukan dalam batas waktu yang ditentukan, Admin berhak:
                    <ul>
                        <li>membatalkan kemenangan, dan/atau</li>
                        <li>menawarkan Lot kepada bidder lain atau melelang ulang di sesi berikutnya.</li>
                    </ul>
                </li>
                <li>Biaya administrasi, biaya pengiriman, dan/atau pajak mungkin dikenakan sesuai ketentuan yang ditampilkan saat checkout.</li>
            </ol>

            <h2>9. Pengiriman &amp; Penyerahan Barang</h2>
            <ul>
                <li>Barang akan dikirim ke alamat yang tercantum pada data Bidder atau alamat yang dikonfirmasi saat pembayaran.</li>
                <li>Resi pengiriman atau nomor pelacakan akan diberikan jika tersedia.</li>
                <li>Risiko kerusakan atau kehilangan selama pengiriman dapat diatur sesuai ketentuan jasa ekspedisi yang digunakan.</li>
            </ul>

            <h2>10. Larangan Perilaku</h2>
            <p>Pengguna dilarang melakukan hal-hal berikut:</p>
            <ul>
                <li>Menggunakan data palsu atau mencatut identitas pihak lain.</li>
                <li>Menggandakan akun untuk memanipulasi harga lelang.</li>
                <li>Mencoba meretas, mengganggu, atau merusak sistem.</li>
                <li>Mengunggah konten yang melanggar hukum, SARA, atau hak kekayaan intelektual pihak lain.</li>
            </ul>

            <h2>11. Batasan Tanggung Jawab</h2>
            <ul>
                <li>Platform disediakan “sebagaimana adanya” tanpa jaminan apa pun di luar yang secara tegas dinyatakan.</li>
                <li>Tempus Auctions tidak bertanggung jawab atas kerugian tidak langsung, kehilangan keuntungan, atau kerusakan yang timbul dari penyalahgunaan akun oleh pengguna sendiri.</li>
                <li>Jika terjadi kesalahan teknis sistem, Admin berhak membatalkan transaksi yang terdampak dan menginformasikan kepada pengguna.</li>
            </ul>

            <h2>12. Perubahan Syarat &amp; Ketentuan</h2>
            <p>
                Syarat &amp; Ketentuan ini dapat diperbarui sewaktu-waktu. Versi terbaru akan ditampilkan di halaman ini.
                Dengan terus menggunakan platform setelah perubahan, Anda dianggap menyetujui Syarat &amp; Ketentuan yang baru.
            </p>

            <h2>13. Kontak</h2>
            <p>
                Untuk pertanyaan terkait Syarat &amp; Ketentuan atau penggunaan platform, Anda dapat menghubungi tim Tempus
                melalui menu kontak yang tersedia di situs.
            </p>
        </div>
    </div>
</x-auth-layout>
