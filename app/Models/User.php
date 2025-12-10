<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\VerifyEmailNotification;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    // use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username', 
        'email',
        'password',
        'role',
        'status',
        'suspended_until',
        'suspend_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        // 'two_factor_recovery_codes',
        // 'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'suspended_until'   => 'datetime',
        ];
    }

    public function updateProfilePhoto(UploadedFile $photo)
    {
        $disk = $this->profilePhotoDisk(); // biasanya 'public'

        // Hapus foto lama kalau ada
        if ($this->profile_photo_path) {
            Storage::disk($disk)->delete($this->profile_photo_path);
        }

        // Ambil nama asli file
        $orig = $photo->getClientOriginalName();
        $base = pathinfo($orig, PATHINFO_FILENAME);
        $ext  = $photo->getClientOriginalExtension();

        // Format: 20251210123456_userid_nama-file-bersih.jpg
        $ts       = now()->format('YmdHis');
        $safeBase = Str::slug($base, '-');
        $filename = "{$ts}_{$this->id}_{$safeBase}.{$ext}";

        // Simpan ke storage/app/public/profile-photos
        // yang di-publish ke public/storage/profile-photos
        $path = $photo->storeAs('profile-photos', $filename, $disk);

        // Simpan path-nya ke kolom profile_photo_path (Jetstream default)
        $this->forceFill([
            'profile_photo_path' => $path, // contoh: "profile-photos/20251210_1_nama-file.jpg"
        ])->save();
    }

    // ---- ROLE HELPERS ----
    public function isSuperAdmin(): bool
    {
        return $this->role === 'SUPERADMIN';
    }

    public function isAdmin(): bool
    {
        // superadmin juga dianggap admin untuk akses panel
        return in_array($this->role, ['ADMIN', 'SUPERADMIN'], true);
    }

    public function isBidder(): bool
    {
        return $this->role === 'BIDDER';
    }

    public function isSuspended()
    {
        // Kalau status SUSPENDED tapi sudah lewat tanggal suspended_until,
        // auto aktifkan lagi.
        if (
            $this->status === 'SUSPENDED' &&
            $this->suspended_until &&
            now()->greaterThanOrEqualTo($this->suspended_until)
        ) {
            $this->status = 'ACTIVE';
            $this->suspended_until = null;
            $this->save();
        }

        return $this->status === 'SUSPENDED';
    }

    public function isActive()
    {
        return $this->status === 'ACTIVE';
    }

    public function bidderProfile()
    {
        return $this->hasOne(\App\Models\BidderProfile::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(
            \App\Models\Payment::class,
            \App\Models\BidderProfile::class,
            'user_id',            // FK di bidder_profiles
            'bidder_profile_id',  // FK di payments
            'id',                 // PK di users
            'id'                  // PK di bidder_profiles
        );
    }

    // Kirim email verifikasi pakai template custom
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification);
    }

    // Kirim email reset password pakai template custom
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
