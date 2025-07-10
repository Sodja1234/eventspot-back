<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Interet; // Ensure the Interet class exists in the App\Models namespace
use Illuminate\Auth\Notifications\ResetPassword;

/**
 * @OA\Schema(
 *     schema="User",
 *     title="User",
 *     description="User model",
 *     @OA\Property(property="id", type="integer", format="int64", description="ID"),
 *     @OA\Property(property="name", type="string", description="User's name"),
 *     @OA\Property(property="email", type="string", format="email", description="User's email address"),
 *     @OA\Property(property="role", type="string", enum={"user", "organisateur"}, description="User's role"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true, description="Timestamp of email verification"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp")
 * )
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
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

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function organisateur()
    {
        return $this->hasOne(Organisateur::class);
    }

    public function events()
    {
    return $this->belongsToMany(Event::class, 'event_user');
    }





    public function interets()
    {
        return $this->belongsToMany(Interets::class);
    }

    public function isOrganisateur()
    {
        return $this->role === 'organisateur';
    }

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
        ];
    }


    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }



public function sendPasswordResetNotification($token)
{
    // $url = config('app.frontend_url').'/reset-password?token='.$token.'&email='.urlencode($this->email);
   // $url = config('app.frontend_url').'/reset-password?token='.$token.'&email='.urlencode($this->email);

    $this->notify(new ResetPassword($token));
}



public function emailOtp()
{
    return $this->hasOne(EmailOtp::class);
}



function subscribEvents()
{
    return $this->belongsToMany(Event::class, 'subscribes')
                ->withPivot('etat')
                ->withTimestamps();
}


}