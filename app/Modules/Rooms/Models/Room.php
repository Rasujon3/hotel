<?php

namespace App\Modules\Rooms\Models;

use App\Models\User;
use App\Modules\Buildings\Models\Building;
use App\Modules\Floors\Models\Floor;
use App\Modules\Hotels\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Room extends Model
{
    use HasFactory;

    protected $table = 'rooms';

    protected $fillable = [
        'user_id',
        'hotel_id',
        'building_id',
        'floor_id',
        'room_no',
        'bed_type',
        'room_type',
        'view',
        'num_of_beds',
        'current_status',
        'end_booking_time',
        'start_booking_time',
        'booking_price',
        'system_commission',
        'rent',
        'icon',
        'status',
        'discount_amount',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'start_booking_time',
        'end_booking_time',
        'current_status',
        'calculate_booking_price',
        'created_by',
        'updated_by',
    ];

    public static function rules($id = null)
    {
        return [
            'user_id'     => 'nullable|exists:users,id',
            'hotel_id'    => 'required|exists:hotels,id',
            'building_id'    => 'required|exists:buildings,id',
            'floor_id'    => 'required|exists:floors,id',
            'room_no'     => 'required|string|max:50',
            'bed_type'    => 'required|in:Single,Double',
            'num_of_beds'    => 'required|in:1,2,3',
            'room_type'      => 'required|in:AC,Non-AC',
            'view' => 'required|string',
            'status'      => 'required|in:Active,Inactive',
            'rent'       => 'required|numeric|min:1|max:99999999.99',
            'discount_amount' => 'nullable|numeric|min:1|max:99999999.99',
            'system_commission' => 'nullable|numeric|min:1|max:99999999.99',
            'images' => 'nullable|array|min:1|max:3',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ];
    }

    public static function listRules($id = null)
    {
        return [
            'building_id'    => 'required|exists:buildings,id',
            'hotel_id'    => 'required|exists:hotels,id',
            'floor_id'    => 'required|exists:floors,id',
        ];
    }

    public static function updateRules($id = null)
    {
        return [
            'user_id'     => 'nullable|exists:users,id',
            'hotel_id'    => 'required|exists:hotels,id',
            'building_id'    => 'required|exists:buildings,id',
            'floor_id'    => 'required|exists:floors,id',
            'room_no'     => 'nullable|string|max:50',
            'bed_type'    => 'nullable|in:Single,Double',
            'num_of_beds'    => 'nullable|in:1,2,3',
            'room_type'      => 'nullable|in:AC,Non-AC',
            'view' => 'nullable|string',
            'status'      => 'nullable|in:Active,Inactive',
            'rent'       => 'nullable|numeric|min:1|max:99999999.99',
            'discount_amount' => 'nullable|numeric|min:1|max:99999999.99',
            'system_commission' => 'nullable|numeric|min:1|max:99999999.99',
            'images' => 'nullable|array|min:1|max:3',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
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
    public function building() : belongsTo
    {
        return $this->belongsTo(Building::class,'building_id');
    }
    public function floor() : belongsTo
    {
        return $this->belongsTo(Floor::class,'floor_id');
    }
    public function images() : hasMany
    {
        return $this->hasMany(RoomImg::class,'room_id');
    }
}
