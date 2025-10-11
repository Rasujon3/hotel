<?php

namespace App\Modules\Payments\Models;

use App\Models\User;
use App\Modules\Bookings\Models\Booking;
use App\Modules\Floors\Models\Floor;
use App\Modules\Hotels\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'booking_id',
        'payment_type',
        'payment_method',
        'acc_no',
        'amount',
        'pay_type',
        'transaction_id',
        'reference',
        'created_by',
        'updated_by',
    ];
    protected $casts = [
        'amount' => 'float',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public static function rules($id = null)
    {
        return [
            'booking_id' => 'required|exists:bookings,id',
            'payment_type' => 'required|in:Online,Offline',
            'payment_method' => 'required|in:bkash,rocket,nagad,cash,bank_transfer,other',
            'acc_no' => 'nullable|string',
            'amount' => 'required|numeric|min:1|max:99999999.99',
            'pay_type' => 'nullable|in:booking,additional',
            'transaction_id' => 'nullable|string|max:100',
            'reference' => 'nullable|string',
            'created_by' => 'nullable|exists:users,id',
            'updated_by' => 'nullable|exists:users,id',
        ];
    }

    public static function dueListRules($id = null)
    {
        return [
            'hotel_id'    => 'required|exists:hotels,id',
        ];
    }

    public static function dueSearchRules($id = null)
    {
        return [
            'hotel_id'    => 'required|exists:hotels,id',
            'phone'    => 'required|exists:users,phone',
        ];
    }

    public static function collectDueRules($id = null)
    {
        return [
            #'user_id' => 'required|exists:users,id',
            'hotel_id' => 'required|exists:hotels,id',
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric|min:1|max:99999999.99',
        ];
    }

    public static function userCollectDueRules($id = null)
    {
        return [
            #'user_id' => 'required|exists:users,id',
            'hotel_id' => 'required|exists:hotels,id',
            'booking_id' => 'required|exists:bookings,id',
            'payment_type' => 'required|in:Online,Offline',
            'payment_method' => 'required|in:bkash',
            'acc_no' => 'required|string',
            'amount' => 'required|numeric|min:1|max:99999999.99',
            'pay_type' => 'required|in:booking,additional',
            'transaction_id' => 'required|string|max:100',
            'reference' => 'nullable|string',
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

    public function booking() : belongsTo
    {
        return $this->belongsTo(Booking::class,'booking_id');
    }
    public function createdBy() : belongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }
    public function updatedBy() : belongsTo
    {
        return $this->belongsTo(User::class,'updated_by');
    }
}
