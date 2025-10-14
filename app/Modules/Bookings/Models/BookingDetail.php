<?php

namespace App\Modules\Bookings\Models;

use App\Models\User;
use App\Modules\Buildings\Models\Building;
use App\Modules\Floors\Models\Floor;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Payments\Models\Payment;
use App\Modules\Rooms\Models\Room;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class BookingDetail extends Model
{
    use HasFactory;

    protected $table = 'booking_details';

    protected $fillable = [
        'user_id',
        'booking_id',
        'hotel_id',
        'building_id',
        'floor_id',
        'room_id',
        'booking_start_date',
        'booking_end_date',
        'check_in',
        'check_out',
        'rent',
        'status',
        'day_count',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'booking_start_date' => 'datetime',
        'booking_end_date' => 'datetime',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
    ];

    public static function rules($id = null)
    {
        return [
            'user_id' => 'required|exists:users,id',
            'hotel_id' => 'required|exists:hotels,id',
            'floor_id' => 'required||exists:floors,id',
            'room_id' => 'required|exists:rooms,id',
            'booking_start_date' => 'required|date|before:booking_end_date',
            'booking_end_date' => 'required|date|after:booking_start_date',
            'check_in' => 'nullable|date|after_or_equal:booking_start_date',
            'check_out' => 'nullable|date|after:check_in',
            'rent' => 'nullable|numeric|min:1|max:99999999.99',
            'status' => 'nullable|in:pending, confirmed, checked_in, checked_out, cancelled',
            'day_count' => 'nullable|integer|min:1|max:365',
        ];
    }

    public static function listRules($id = null)
    {
        return [
            'hotel_id'    => 'required|exists:hotels,id',
            'floor_id'    => 'required|exists:floors,id',
        ];
    }

    public static function updateRules($id = null)
    {
        return [
            'user_id'     => 'nullable|exists:users,id',
            'hotel_id'    => 'required|exists:hotels,id',
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
            'images' => 'nullable|array|min:1',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function getBookingStartDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('Y-m-d h:i A') : null;
    }

    public function getBookingEndDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('Y-m-d h:i A') : null;
    }

    public function getCheckInAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('Y-m-d h:i A') : null;
    }

    public function getCheckOutAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('Y-m-d h:i A') : null;
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
    public function room() : belongsTo
    {
        return $this->belongsTo(Room::class,'room_id');
    }
    public function booking() : belongsTo
    {
        return $this->belongsTo(Booking::class,'booking_id');
    }
    public function payments() : hasMany
    {
        return $this->hasMany(Payment::class,'booking_id');
    }
}
