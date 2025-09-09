<?php

namespace App\Modules\Hotels\Models;

use App\Models\User;
use App\Modules\Floors\Models\Floor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Hotel extends Model
{
    use HasFactory;

    protected $table = 'hotels';

    protected $fillable = [
        'user_id',
        'hotel_name',
        'hotel_description',
        'hotel_address',
        'lat',
        'long',
        'status',
        'package_id',
        'package_start_date',
        'package_end_date',
    ];
    public function user() : belongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function floor(): HasMany
    {
        return $this->hasMany(Floor::class);
    }
}
