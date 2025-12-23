<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // Extra fields used by our API auth controller
        'phone',
        'address',
        'google_id',
        'avatar',
        // OTP fields
        'otp_code',
        'otp_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
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
            'password' => 'hashed',
            'otp_expires_at' => 'datetime',
        ];
    }

    /**
     * Generate a 6-digit OTP code and set expiration time
     * 
     * @return string The generated OTP code
     */
    public function generateOtp(): string
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
}
