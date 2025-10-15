<?php

namespace App\Modules\Ratings\Models;

use App\Models\User;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Rooms\Models\Room;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Rating extends Model
{
    use HasFactory;

    protected $table = 'ratings';

    protected $fillable = [
        'user_id',
        'hotel_id',
        'rating',
        'description',
    ];

    protected $casts = [
        'rating' => 'float',
    ];

    protected $hidden = [
        'created_at',
        'created_by',
        'updated_by',
        'user_id',
    ];

    public static function rules()
    {
        return [
            'user_id' => 'nullable|exists:users,id',
            'hotel_id' => 'required|exists:hotels,id',
            'rating' => [
                'required',
                'numeric',
                'min:1',
                'max:5',
                // A custom rule to check for increments of 0.5
                function ($attribute, $value, $fail) {
                    if (fmod($value, 0.5) !== 0.0) {
                        $fail("The :attribute must be a number with increments of 0.5 (e.g., 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5).");
                    }
                },
            ],
            'description' => 'nullable|string|max:1000',
        ];
    }
    public static function listRules()
    {
        return [
            'user_id' => 'nullable|string|exists:users,id',
            'hotel_id' => 'nullable|string|exists:hotels,id',
        ];
    }
    public function getUpdatedAtAttribute($value)
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
}
