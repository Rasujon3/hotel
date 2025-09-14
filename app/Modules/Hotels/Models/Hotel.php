<?php

namespace App\Modules\Hotels\Models;

use App\Models\User;
use App\Modules\Facilities\Models\Facility;
use App\Modules\Floors\Models\Floor;
use App\Modules\Packages\Models\Package;
use App\Modules\Rooms\Models\Room;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'booking_percentage',
        'check_in_time',
        'check_out_time',
        'package_id',
        'package_start_date',
        'package_end_date',
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
    public static function updateRules()
    {
        return [
            'user_id'     => 'nullable|exists:users,id',
            'hotel_id'    => 'nullable|exists:hotels,id',
            'full_name'    => 'nullable|string',
            'hotel_name'     => 'nullable|string',
            'email'    => 'nullable|email',
            'hotel_address'      => 'nullable|string',
            'hotel_description' => 'nullable|string',
            'booking_percentage' => 'nullable|numeric|min:1|max:100',
            'images' => 'nullable|array|min:1',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ];
    }
    public function user() : belongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function floor(): HasMany
    {
        return $this->hasMany(Floor::class);
    }
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
    public function package(): belongsTo
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
    public function images(): HasMany
    {
        return $this->hasMany(HotelImg::class, 'hotel_id');
    }
    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }
}
