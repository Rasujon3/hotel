<?php

namespace App\Modules\Hotels\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

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
    ];

    public static function rules($areaId = null)
    {
        $uniqueCodeRule = Rule::unique('areas', 'code');

        if ($areaId) {
            $uniqueCodeRule->ignore($areaId);
        }
        return [
            'code' => ['nullable', 'string', 'max:45', $uniqueCodeRule],
            'name' => 'nullable|string|max:191|regex:/^[ ]*[a-zA-Z][ a-zA-Z]*[ ]*$/u', // regex for English characters with spaces
            'name_in_bangla' => 'nullable|string|max:191|regex:/^[\p{Bengali}\s]+$/u', // regex for Bangla characters with spaces
            'name_in_arabic' => 'nullable|string|max:191|regex:/^[\p{Arabic}\s]+$/u', // regex for Arabic characters with spaces
            'is_default' => 'nullable|boolean',
            'draft' => 'nullable|boolean',
            'drafted_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
            'country_id' => [
                'required',
                Rule::exists('countries', 'id')->whereNull('deleted_at')
            ],
            'state_id' => [
                'required',
                Rule::exists('states', 'id')->whereNull('deleted_at')
            ],
            'city_id' => [
                'required',
                Rule::exists('cities', 'id')->whereNull('deleted_at')
            ],
        ];
    }
    public function user() : belongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
