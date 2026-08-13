<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\UserRolesEnum;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


// #[Fillable(['name', 'email', 'password', 'role'])]   // laravel 13
// #[Hidden(['password', 'remember_token'])]  // laravel 13 
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable,HasApiTokens;

    // The attributes that are mass assignable.
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];
    // The attributes that should be hidden for serialization.
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
            'role' => UserRolesEnum::class,
        ];
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function confirmEmail(): bool
    {
        if ($this->email_verified_at !== null) {
            return false; // Email is already confirmed
        }

        $this->email_verified_at = now();
        $this->save();

        return true; // Email confirmation successful
    }

}