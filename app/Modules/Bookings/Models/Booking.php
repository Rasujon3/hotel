<?php

namespace App\Modules\Bookings\Models;

use App\Models\User;
use App\Modules\Floors\Models\Floor;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Payments\Models\Payment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    protected $fillable = [
        'user_id',
        'hotel_id',
        'payment_type',
        'booking_detail_id',
        'booking_start_date',
        'booking_end_date',
        'check_in',
        'check_out',
        'total',
        'paid',
        'due',
        'status',
    ];

    protected $hidden = [
        'booking_start_date',
        'booking_end_date',
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
            // Booking main fields
            'hotel_id'           => ['nullable', 'integer', 'exists:hotels,id'],
//            'booking_start_date' => ['nullable', 'date', 'after_or_equal:today'],
//            'booking_end_date'   => ['nullable', 'date', 'after:booking_start_date'],
//            'check_in'           => ['nullable', 'date'],
//            'check_out'          => ['nullable', 'date', 'after_or_equal:check_in'],
            'total'              => ['required', 'numeric', 'min:1'],
            'paid'               => ['required', 'numeric', 'min:1', 'lte:total'],
            'due'                => ['required', 'numeric', 'min:0', 'lte:total'],

            // Rooms array
            'rooms'                        => ['required', 'array', 'min:1'],
            'rooms.*.hotel_id'             => ['required', 'integer', 'exists:hotels,id'],
            'rooms.*.building_id'          => ['required', 'integer', 'exists:buildings,id'],
            'rooms.*.floor_id'             => ['nullable', 'integer', 'exists:floors,id'],
            'rooms.*.room_id'              => ['required', 'integer', 'exists:rooms,id'],
            'rooms.*.day_count'            => ['required', 'integer', 'min:1'],
            'rooms.*.rent'                 => ['required', 'integer', 'min:1'],

            // Booking Dates
            'rooms.*.booking_start_date'   => ['required', 'date', 'after_or_equal:today'],
            'rooms.*.booking_end_date'     => ['required', 'date', 'after_or_equal:rooms.*.booking_start_date'],

            // Optional Check-in/out
            'rooms.*.check_in'             => ['nullable', 'date'],
            'rooms.*.check_out'            => ['nullable', 'date', 'after_or_equal:rooms.*.check_in'],

            // Payment fields
            'payment'                     => ['required', 'array'],
            'payment.payment_type'        => ['required', 'in:Online,Offline'],
            'payment.payment_method'      => ['required', 'in:bkash,rocket,nagad,credit_card,cash,bank_transfer,other'],
            'payment.acc_no'              => ['nullable', 'string', 'max:100'],
            'payment.amount'              => ['required', 'numeric', 'min:0'],
            'payment.transaction_id'      => ['required_if:payment.payment_type,Online', 'string', 'max:255'],
            'payment.reference'           => ['nullable', 'string', 'max:1000'],
        ];
    }

    public static function listRules($id = null)
    {
        return [
            'hotel_id'    => 'required|exists:hotels,id',
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
    public static function updateStatusRules($id = null)
    {
        return [
            'hotel_id'    => 'required|exists:hotels,id',
            'booking_detail_id' => 'required|exists:booking_details,id',
            'status' => 'required|in:checked_in,checked_out',
        ];
    }
    public static function updateCheckInStatusRules($id = null)
    {
        return [
            'booking_id'     => 'required|exists:bookings,id',
        ];
    }
    public static function searchBookingByUserRules()
    {
        return [
            'hotel_id'    => 'required|exists:hotels,id',
            'phone'     => 'required|exists:users,phone',
        ];
    }
    public static function userBookingsRules()
    {
        return [
            'booking_id' => 'nullable|exists:bookings,id',
            'status' => 'nullable|in:pending,confirmed,checked_in,checked_out,cancelled',
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
    public function payments() : hasMany
    {
        return $this->hasMany(Payment::class,'booking_id');
    }
    public function bookingDetails() : hasMany
    {
        return $this->hasMany(BookingDetail::class,'booking_id');
    }
}
