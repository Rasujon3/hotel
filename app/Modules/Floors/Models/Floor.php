<?php

namespace App\Modules\Floors\Models;

use App\Models\User;
use App\Modules\Buildings\Models\Building;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Rooms\Models\Room;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Floor extends Model
{
    use HasFactory;

    protected $table = 'floors';

    protected $fillable = [
        'building_id',
        'user_id',
        'hotel_id',
        'name',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'user_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public static function rules()
    {
        return [
            'building_id' => 'required|numeric|exists:buildings,id',
            'name' => ['required', 'string', 'max:45'],
            'hotel_id' => 'required|string|max:191|exists:hotels,id',
            'status' => 'required|in:Active,Inactive',
            'images' => 'required|array|min:1|max:3',
            'images.*' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ];
    }
    public static function listRules()
    {
        return [
            'hotel_id' => 'required|string|max:191|exists:hotels,id',
            'building_id' => 'required|numeric|exists:buildings,id',
        ];
    }
    public static function updateRules()
    {
        return [
            'building_id' => 'required|numeric|exists:buildings,id',
            'name' => ['required', 'string', 'max:45'],
            'hotel_id' => 'required|string|max:191|exists:hotels,id',
            'status' => 'required|in:Active,Inactive',
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
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
    public function images(): HasMany
    {
        return $this->hasMany(FloorImg::class);
    }
}
