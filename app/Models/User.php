<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Interets; // Ensure the Interet class exists in the App\Models namespace
use Illuminate\Auth\Notifications\ResetPassword;

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
        'fullname',
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

    // public function sendEmailVerificationNotification()
    // {
    //     $this->notify(new \Illuminate\Auth\Notifications\VerifyEmail);
    // }
    //  public function sendPasswordResetNotification($token)
    // {
    //     $url = config('app.frontend_url').'/reset-password?token='.$token.'&email='.$this->email;
        
    //     $this->notify(new ResetPassword($url));
    // }
public function sendPasswordResetNotification($token)
{
    $url = config('app.frontend_url').'/reset-password?token='.$token.'&email='.urlencode($this->email);
    
    $this->notify(new ResetPassword($url));
}



}