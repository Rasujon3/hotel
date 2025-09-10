<?php

namespace App\Modules\Rooms\Models;

use App\Models\User;
use App\Modules\Floors\Models\Floor;
use App\Modules\Hotels\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Room extends Model
{
    use HasFactory;

    protected $table = 'rooms';

    protected $fillable = [
        'user_id',
        'hotel_id',
        'floor_id',
        'room_no',
        'bed_type',
        'has_ac',
        'description',
        'status',
        'price',
        'booking_price',
        'calculate_booking_price',
        'start_booking_time',
        'end_booking_time',
        'current_status',
    ];

    protected $hidden = [
        'start_booking_time',
        'end_booking_time',
        'current_status',
        'calculate_booking_price',
    ];

    public static function rules($id = null)
    {
        return [
            'user_id'     => 'nullable|exists:users,id',
            'hotel_id'    => 'required|exists:hotels,id',
            'floor_id'    => 'required|exists:floors,id',
            'room_no'     => 'required|string|max:50',
            'bed_type'    => 'required|in:Single,Double,Triple',
            'has_ac'      => 'required|boolean',
            'description' => 'required|string',
            'status'      => 'required|in:Active,Inactive',
            'price'       => 'required|numeric|min:1|max:99999999.99',
            'booking_price' => 'required|numeric|min:1|max:100',
        ];
    }

    public static function listRules()
    {
        return [
            'hotel_id'    => 'required|exists:hotels,id',
            'floor_id'    => 'required|exists:floors,id',
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
    public function floor() : belongsTo
    {
        return $this->belongsTo(Floor::class,'floor_id');
    }
}
