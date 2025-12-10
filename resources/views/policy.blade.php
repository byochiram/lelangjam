{{-- resources/views/policy.blade.php --}}
<x-auth-layout>
    <div class="w-full max-w-3xl mx-auto bg-white border border-slate-200 rounded-2xl shadow-sm p-8 md:p-10">
        {{-- HEADER --}}
        <div class="flex items-center gap-3 mb-6">
            <x-authentication-card-logo />
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-slate-900">
                    Kebijakan Privasi Tempus Auctions
                </h1>
                <p class="text-sm text-slate-500">
                    Menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi data pribadi Anda.
                </p>
            </div>
        </div>

        {{-- CONTENT --}}
        <div class="prose prose-sm md:prose-base max-w-none text-slate-800">
            <h2>1. Pendahuluan</h2>
            <p>
                Kebijakan Privasi ini berlaku untuk seluruh pengguna platform lelang jam tangan Tempus Auctions.
                Dengan membuat akun atau menggunakan layanan kami, Anda menyetujui pengelolaan data pribadi
                sebagaimana diatur di bawah ini.
            </p>

            <h2>2. Data yang Kami Kumpulkan</h2>

            <h3>2.1 Data Akun Pengguna</h3>
            <ul>
                <li>Nama lengkap</li>
                <li>Username</li>
                <li>Alamat email</li>
                <li>Password (disimpan dalam bentuk terenkripsi / hash)</li>
            </ul>

            <h3>2.2 Data Profil Bidder</h3>
            <ul>
                <li>Nomor telepon</li>
                <li>Alamat, kota, dan kode pos</li>
                <li>Riwayat bid (tabel <code>bids</code>) dan riwayat pembayaran (tabel <code>payments</code>)</li>
            </ul>

            <h3>2.3 Data KYC (Verifikasi Identitas)</h3>
            <ul>
                <li>Jenis identitas (misalnya KTP)</li>
                <li>NIK yang disimpan dalam bentuk hash atau format terproteksi</li>
                <li>Foto KTP dan foto selfie</li>
                <li>Tanggal lahir dan alamat sesuai identitas</li>
            </ul>

            <h3>2.4 Data Teknis &amp; Aktivitas</h3>
            <ul>
                <li>Alamat IP, user agent, dan informasi sesi (tabel <code>sessions</code>)</li>
                <li>Waktu login, logout, dan aktivitas dasar untuk kebutuhan keamanan dan audit</li>
            </ul>

            <h2>3. Tujuan Penggunaan Data</h2>
            <ul>
                <li>Memverifikasi dan mengelola akun pengguna.</li>
                <li>Memungkinkan pengguna mengikuti lelang, menempatkan bid, dan melakukan pembayaran.</li>
                <li>Memproses dan menindaklanjuti KYC untuk mencegah penipuan dan memastikan kepatuhan regulasi.</li>
                <li>Meningkatkan keamanan sistem dan mencegah akses tidak sah.</li>
                <li>Melakukan komunikasi terkait status lelang, pembayaran, dan informasi penting lainnya.</li>
            </ul>

            <h2>4. Dasar Pemrosesan Data</h2>
            <ul>
                <li>
                    <strong>Persetujuan</strong>:
                    Anda menyetujui pengolahan data saat membuat akun dan menyetujui Syarat &amp; Ketentuan.
                </li>
                <li>
                    <strong>Pelaksanaan Perjanjian</strong>:
                    Kami memproses data untuk menyediakan layanan lelang yang Anda gunakan.
                </li>
                <li>
                    <strong>Kepatuhan Hukum</strong>:
                    Dalam beberapa kasus, kami wajib menyimpan atau memberikan data sesuai peraturan yang berlaku.
                </li>
            </ul>

            <h2>5. Penyimpanan &amp; Retensi Data</h2>
            <ul>
                <li>Data disimpan di basis data yang dikelola secara aman dengan pembatasan akses.</li>
                <li>Password disimpan dalam bentuk hash dan tidak dapat dibaca langsung.</li>
                <li>Data KYC dan riwayat transaksi dapat disimpan untuk jangka waktu tertentu sesuai kebutuhan audit dan regulasi.</li>
                <li>Jika Anda menutup akun, sebagian data mungkin tetap disimpan untuk kepentingan hukum atau akuntansi.</li>
            </ul>

            <h2>6. Berbagi Data dengan Pihak Ketiga</h2>
            <p>Kami dapat membagikan sebagian data Anda hanya dalam kondisi terbatas berikut:</p>
            <ul>
                <li>Mitra pembayaran (payment gateway) untuk memproses transaksi.</li>
                <li>Penyedia jasa pengiriman untuk mengirimkan barang yang Anda menangkan.</li>
                <li>Otoritas yang berwenang jika diwajibkan oleh hukum.</li>
            </ul>
            <p>
                Kami tidak menjual data pribadi Anda kepada pihak ketiga untuk tujuan pemasaran.
            </p>

            <h2>7. Hak Pengguna</h2>
            <ul>
                <li>Hak untuk melihat dan memperbarui data profil melalui halaman profil akun.</li>
                <li>Hak untuk meminta perbaikan jika terdapat data yang tidak akurat.</li>
                <li>Hak untuk mengajukan permintaan penghapusan akun, sejauh tidak bertentangan dengan kewajiban hukum.</li>
            </ul>

            <h2>8. Keamanan Data</h2>
            <ul>
                <li>Kami menggunakan kombinasi kontrol teknis dan organisasi untuk melindungi data Anda.</li>
                <li>Pengguna juga bertanggung jawab menjaga kerahasiaan password dan kredensial login.</li>
            </ul>

            <h2>9. Cookie &amp; Log Aktivitas</h2>
            <ul>
                <li>Platform dapat menggunakan cookie atau teknologi serupa untuk menjaga sesi login dan meningkatkan pengalaman pengguna.</li>
                <li>Log aktivitas dapat disimpan untuk mendeteksi aktivitas mencurigakan dan melakukan troubleshooting.</li>
            </ul>

            <h2>10. Perubahan Kebijakan Privasi</h2>
            <p>
                Kebijakan Privasi ini dapat diperbarui dari waktu ke waktu. Setiap perubahan akan diumumkan melalui halaman ini.
                Dengan tetap menggunakan platform setelah perubahan, Anda dianggap menyetujui versi terbaru Kebijakan Privasi.
            </p>

            <h2>11. Kontak</h2>
            <p>
                Jika Anda memiliki pertanyaan terkait perlindungan data atau Kebijakan Privasi ini, silakan hubungi kami
                melalui menu kontak yang tersedia pada situs Tempus Auctions.
            </p>
        </div>
    </div>
</x-auth-layout>
