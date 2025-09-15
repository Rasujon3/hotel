<?php

namespace App\Modules\WithdrawalMethods\Models;

use App\Models\User;
use App\Modules\Floors\Models\Floor;
use App\Modules\Hotels\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class WithdrawalMethod extends Model
{
    use HasFactory;

    protected $table = 'withdrawal_methods';

    protected $fillable = [
        'user_id',
        'hotel_id',
        'payment_method',
        'acc_no',
        'bank_name',
        'branch_name',
        'routing_number',
    ];

    public static function rules($id = null)
    {
        return [
            'user_id'        => 'nullable|exists:users,id',
            'hotel_id'       => 'required|exists:hotels,id',
            'payment_method' => 'required|in:bkash,rocket,nagad,bank_account',
            'acc_no'         => 'required|string|max:50',
            'bank_name'      => 'nullable|string|max:100',
            'branch_name'    => 'nullable|string|max:100',
            'routing_number' => 'nullable|string|max:50',
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
            'user_id'        => 'nullable|exists:users,id',
            'hotel_id'       => 'required|exists:hotels,id',
            'payment_method' => 'required|in:bkash,rocket,nagad,bank_account',
            'acc_no'         => 'required|string|max:50',
            'bank_name'      => 'nullable,bank_account|string|max:100',
            'branch_name'    => 'nullable,bank_account|string|max:100',
            'routing_number' => 'nullable,bank_account|string|max:50',
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
