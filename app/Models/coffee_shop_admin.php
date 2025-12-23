<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class coffee_shop_admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'admin_id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'coffee_shop_admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'admin_name',
        'admin_email',
        'admin_password',
        'email_verified_at',
        'otp_code',
        'otp_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'admin_password',
        'remember_token',
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
            'admin_password' => 'hashed',
            'otp_expires_at' => 'datetime',
        ];
    }

    /**
     * Ensure notifications use the admin email field.
     *
     * @return string
     */
    public function routeNotificationForMail(): string
    {
        return $this->admin_email;
    }

    /**
     * Get the password attribute for authentication.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->admin_password;
    }

    /**
     * Generate a 6-digit OTP code and set expiration time
     * 
     * @return string The generated OTP code
     */
    public function generateOtpCode(): string
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->otp_code = $otp;
        $this->otp_expires_at = now()->addMinutes(10); // OTP expires in 10 minutes
        $this->save();
        
        return $otp;
    }

    /**
     * Verify if the provided OTP code is valid
     * 
     * @param string $otpCode The OTP code to verify
     * @return bool True if valid, false otherwise
     */
    public function verifyOtpCode(string $otpCode): bool
    {
        // Check if OTP exists and matches
        if (!$this->otp_code || $this->otp_code !== $otpCode) {
            return false;
        }

        // Check if OTP has expired
        if (!$this->otp_expires_at || $this->otp_expires_at->isPast()) {
            return false;
        }

        // OTP is valid - mark email as verified and clear OTP
        $this->email_verified_at = now();
        $this->otp_code = null;
        $this->otp_expires_at = null;
        $this->save();

        return true;
    }

    /**
     * Clear OTP code and expiration
     */
    public function clearOtp(): void
    {
        $this->otp_code = null;
        $this->otp_expires_at = null;
        $this->save();
    }

    /**
     * Determine if the admin has verified their email address.
     *
     * @return bool
     */
    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }
}
