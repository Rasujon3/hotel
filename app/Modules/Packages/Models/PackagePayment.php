<?php

namespace App\Modules\Packages\Models;

use App\Models\User;
use App\Modules\Hotels\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class PackagePayment extends Model
{
    use HasFactory;

    protected $table = 'package_payments';

    protected $fillable = [
        'hotel_id',
        'package_id',
        'amount',
        'payment_method',
        'transaction_id',
        'status',
        'payment_date',
    ];

    public static function rules($id = null)
    {
        $uniqueCodeRule = Rule::unique('packages', 'name');

        if ($id) {
            $uniqueCodeRule->ignore($id);
        }
        return [
            'name' => ['required', 'string', 'max:45', $uniqueCodeRule],
            'duration' => 'required|string|max:191|in:weekly,monthly,yearly',
            'price' => 'required|numeric|min:1',
            'status' => 'required|in:Active,Inactive',

        ];
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get the package associated with the package payment.
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
