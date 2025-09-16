<?php

namespace App\Modules\Withdraws\Models;

use App\Models\User;
use App\Modules\Floors\Models\Floor;
use App\Modules\Hotels\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Withdraw extends Model
{
    use HasFactory;

    protected $table = 'withdraws';

    protected $fillable = [
        'user_id',
        'hotel_id',
        'withdrawal_method_id',
        'title',
        'payment_type',
        'amount',
        'withdraw_at',
        'trx_id',
        'reference',
        'created_by',
    ];

    public static function rules($id = null)
    {
        return [
            'user_id'               => 'nullable|exists:users,id',
            'hotel_id'              => 'required|exists:hotels,id',
            'withdrawal_method_id'  => 'nullable|exists:withdrawal_methods,id',
            'title'                 => 'nullable|string|max:100',
            'payment_type'          => 'required|string|max:100',
            'amount'                => 'required|numeric|min:1',
            'withdraw_at'           => 'required|date',   // expects full datetime (e.g., 2025-09-10 14:42:00)
            'trx_id'                => 'nullable|string|max:100',
            'reference'             => 'nullable|string',
            'created_by'            => 'nullable|exists:users,id',
        ];
    }

    public static function listRules($id = null)
    {
        return [
            'hotel_id'    => 'required|exists:hotels,id',
        ];
    }

    public static function updateRules($id = null)
    {
        return [
            'user_id'               => 'nullable|exists:users,id',
            'hotel_id'              => 'required|exists:hotels,id',
            'withdrawal_method_id'  => 'nullable|exists:withdrawal_methods,id',
            'title'                 => 'nullable|string|max:100',
            'payment_type'          => 'nullable|string|max:100',
            'amount'                => 'nullable|numeric|min:1',
            'withdraw_at'           => 'required|date',   // expects full datetime (e.g., 2025-09-10 14:42:00)
            'trx_id'                => 'nullable|string|max:100',
            'reference'             => 'nullable|string',
            'created_by'            => 'nullable|exists:users,id',
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
