<?php

namespace App\Modules\Homes\Models;

use App\Models\User;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Rooms\Models\Room;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Home extends Model
{
    use HasFactory;

    protected $table = 'ratings';

    protected $fillable = [
        'user_id',
        'hotel_id',
        'rating',
    ];

    protected $casts = [
        'rating' => 'float',
    ];

    protected $hidden = [
        'created_by',
        'updated_by',
        'user_id',
    ];

    public static function hotelDetailsRules()
    {
        return [
            'hotel_id' => 'required|exists:hotels,id',
        ];
    }

    public static function roomDetailsRules()
    {
        return [
            'hotel_id' => 'required|exists:hotels,id',
            'floor_id' => 'required|exists:floors,id',
        ];
    }
    public static function listRules()
    {
        return [
            'user_id' => 'required|string|max:191|exists:users,id',
        ];
    }
    public function user() : belongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function hotel() : belongsTo
    {
        return $this->belongsTo(Hotel::class,'hotel_id');
    }
}
