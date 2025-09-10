<?php

namespace App\Modules\Floors\Models;

use App\Models\User;
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
        'user_id',
        'hotel_id',
        'name',
        'status',
    ];

    public static function rules()
    {
        return [
            'name' => ['required', 'string', 'max:45'],
            'hotel_id' => 'required|string|max:191|exists:hotels,id',
            'status' => 'required|in:Active,Inactive',
            'images' => 'required|array|min:1',
            'images.*' => 'required|image|mimes:jpg,jpeg,png|max:2048',
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
            'name' => ['required', 'string', 'max:45'],
            'hotel_id' => 'required|string|max:191|exists:hotels,id',
            'status' => 'required|in:Active,Inactive',
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
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
    public function images(): HasMany
    {
        return $this->hasMany(FloorImg::class);
    }
}
