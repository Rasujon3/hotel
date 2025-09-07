<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'user_type_id',
        'role',
        'ip_address',
        'lat',
        'long',
        'day',
        'month',
        'year',
        'fbase',
        'refer_code',
        'my_refer_code',
        'email_verified_at',
        'password',
        'token',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'otp_enabled' => 'boolean',
        ];
    }

    public static function rules()
    {
        return [
            'full_name'        => 'nullable|string|max:255',
            'email'            => 'required|email|max:255|unique:users,email',
            'phone'            => 'required|string|max:20|unique:users,phone',
            'user_type_id'     => 'nullable|string|max:50',
            'role'             => 'nullable|string|max:50',
            'ip_address'       => 'nullable|ip',
            'lat'              => 'nullable|numeric',
            'long'             => 'nullable|numeric',
            'day'              => 'nullable|string|max:2',
            'month'            => 'nullable|string',
            'year'             => 'nullable|string|max:4',
            'fbase'            => 'nullable|string|max:255',
            'refer_code'       => 'nullable|string|max:50',
            'my_refer_code'       => 'nullable|string|max:50',
            'email_verified_at'=> 'nullable|date',
            'password'         => 'required|string|min:6',
        ];
    }
}
