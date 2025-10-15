<?php

namespace App\Modules\Offers\Models;

use App\Models\User;
use App\Modules\Buildings\Models\Building;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Expenses\Models\ExpenseImg;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Offer extends Model
{
    use HasFactory;

    protected $table = 'offers';

    protected $fillable = [
        'hotel_id',
        'building_id',
        'floor_id',
        'room_id',
        'room_no',
        'start_date',
        'end_date',
        'booking_price',
        'rent',
        'discount_amount',
    ];

    public static function rules()
    {
        return [
            'hotel_id'        => ['required', 'integer', 'exists:hotels,id'],
            'building_id'     => ['required', 'integer', 'exists:buildings,id'],
            'floor_id'        => ['required', 'integer', 'exists:floors,id'],
            'room_id'        => ['required', 'integer', 'exists:rooms,id'],
            'room_no'         => ['required', 'string', 'max:50'],
            'start_date'      => ['required', 'date', 'before_or_equal:end_date'],
            'end_date'        => ['required', 'date', 'after_or_equal:start_date'],
            'rent'            => ['required', 'numeric', 'min:1', 'max:99999999.99'],
            'discount_amount' => ['required', 'numeric', 'min:1', 'max:99999999.99', 'lt:rent'],
            'booking_price'   => ['nullable', 'numeric', 'min:1', 'max:99999999.99'],
        ];
    }
    public static function listRules()
    {
        return [
            'hotel_id' => 'required|string|max:191|exists:hotels,id',
        ];
    }
    public static function updateRules()
    {
        return [
            'hotel_id' => 'required|string|max:191|exists:hotels,id',
            'name'      => 'required|string|max:255',
            'quantity'  => 'required|numeric|min:1',
            'unit'      => 'required|string|max:50',
            'amount'    => 'required|numeric|min:1|max:99999999.99',
            'images' => 'nullable|array|min:1',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
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
    public function images(): HasMany
    {
        return $this->hasMany(ExpenseImg::class);
    }
}
