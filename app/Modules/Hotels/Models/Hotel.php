<?php

namespace App\Modules\Hotels\Models;

use App\Models\User;
use App\Modules\Buildings\Models\Building;
use App\Modules\Facilities\Models\Facility;
use App\Modules\Floors\Models\Floor;
use App\Modules\Packages\Models\Package;
use App\Modules\PopularPlaces\Models\PopularPlace;
use App\Modules\Ratings\Models\Rating;
use App\Modules\Rooms\Models\Room;
use App\Modules\WithdrawalMethods\Models\WithdrawalMethod;
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
        'balance',
        'status',
        'booking_percentage',
        'check_in_time',
        'check_out_time',
        'package_id',
        'popular_place_id',
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
            'booking_percentage' => 'required|numeric|min:1|max:100',
            'popular_place_id'    => 'nullable|integer|exists:popular_places,id',
            'property_type_id'    => 'required|integer|exists:property_types,id',
            'system_commission' => 'required|numeric|min:1|max:99999999.99',
            'status'      => 'required|in:Active,Inactive',
            'check_in_time'      => 'required',
            'check_out_time'      => 'required',
            'images' => 'nullable|array|min:1',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ];
    }
    public static function checkBalanceRules()
    {
        return [
            'hotel_id'   => ['required', 'integer', 'exists:hotels,id'],
            'start_date' => ['nullable', 'date', 'before_or_equal:end_date', 'required_with:end_date'],
            'end_date'   => ['nullable', 'date', 'after_or_equal:start_date', 'required_with:start_date'],
        ];
    }
    public function getRatingsAvgRatingAttribute($value)
    {
        return $value ? number_format($value, 1) : null;
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
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }
    public function popularPlace(): belongsTo
    {
        return $this->belongsTo(PopularPlace::class, 'popular_place_id');
    }
    public function withdrawMethod(): hasOne
    {
        return $this->hasOne(WithdrawalMethod::class, 'hotel_id');
    }

    public function propertyType(): belongsTo
    {
        return $this->belongsTo(PropertyType::class, 'property_type_id');
    }

    public function buildings(): hasMany
    {
        return $this->hasMany(Building::class, 'hotel_id');
    }
}
