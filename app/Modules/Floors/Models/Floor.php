<?php

namespace App\Modules\Floors\Models;

use App\Models\User;
use App\Modules\Hotels\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        ];
    }
    public static function listRules()
    {
        return [
            'hotel_id' => 'required|string|max:191|exists:hotels,id',
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
}
