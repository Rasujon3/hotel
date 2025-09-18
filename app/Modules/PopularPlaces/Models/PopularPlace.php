<?php

namespace App\Modules\PopularPlaces\Models;

use App\Models\User;
use App\Modules\Facilities\Models\Facility;
use App\Modules\Floors\Models\Floor;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Packages\Models\Package;
use App\Modules\Ratings\Models\Rating;
use App\Modules\Rooms\Models\Room;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PopularPlace extends Model
{
    use HasFactory;

    protected $table = 'popular_places';

    protected $fillable = [
        'name',
        'status',
        'image_url',
        'image_path',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'image_path',
    ];

    public static function rules($id = null)
    {
        $rules = [
            'user_id'     => 'nullable|exists:users,id',
            'name' => 'required|string|unique:popular_places,name,' . $id,
            'status'      => 'required|in:Active,Inactive',
        ];

        if (is_null($id)) {
            // Rule for create (if $id is null)
            $rules['image'] = 'required|image|mimes:jpg,jpeg,png|max:5120';
        } else {
            // Rule for update (if $id is not null)
            $rules['image'] = 'nullable|image|mimes:jpg,jpeg,png|max:5120';
        }

        return $rules;
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
    public static function checkBalanceRules()
    {
        return [
            'hotel_id'   => ['required', 'integer', 'exists:hotels,id'],
            'start_date' => ['nullable', 'date', 'before_or_equal:end_date', 'required_with:end_date'],
            'end_date'   => ['nullable', 'date', 'after_or_equal:start_date', 'required_with:start_date'],
        ];
    }
    public function hotel(): HasOne
    {
        return $this->hasOne(Hotel::class, 'popular_place_id', 'id');
    }
}
